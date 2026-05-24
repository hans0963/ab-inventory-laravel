<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Category;
use App\Models\Employee;
use App\Models\Customer;
use App\Models\ProductionIn;
use App\Models\ProductionOut;
use App\Models\StockWithdrawal;
use App\Models\ProductionInItem;
use App\Models\ProductionOutItem;
use App\Models\InventoryReceivingItem;
use App\Models\StockWithdrawalItem;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function salesReport(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());
        $groupBy = $request->input('group_by', 'day');

        $query = DB::table('sales')
            ->whereBetween('sales.created_at', [
                Carbon::parse($dateFrom)->startOfDay(),
                Carbon::parse($dateTo)->endOfDay(),
            ]);

        $totalSales = (clone $query)->sum('total_amount');
        $totalOrders = (clone $query)->count();
        $averageOrder = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
        $discounts = (clone $query)->sum('discount_amount');

        $periodExpression = match ($groupBy) {
            'week' => 'YEARWEEK(sales.created_at, 1)',
            'month' => 'DATE_FORMAT(sales.created_at, "%Y-%m")',
            default => 'DATE(sales.created_at)',
        };

        $salesByPeriod = (clone $query)
            ->select(DB::raw($periodExpression . ' as label'), DB::raw('SUM(total_amount) as total'), DB::raw('COUNT(*) as transactions'))
            ->groupBy(DB::raw($periodExpression))
            ->orderBy('label')
            ->get();

        $salesPerProduct = (clone $query)
            ->join('products', 'sales.product_id', '=', 'products.id')
            ->select('products.product_name as name', DB::raw('SUM(sales.sold) as units_sold'), DB::raw('SUM(sales.total_amount) as revenue'))
            ->groupBy('products.id', 'products.product_name')
            ->orderByDesc('revenue')
            ->get();

        $salesPerCategory = (clone $query)
            ->join('products', 'sales.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->select(DB::raw('COALESCE(categories.category_name, "Uncategorized") as category_name'), DB::raw('SUM(sales.total_amount) as revenue'))
            ->groupBy('categories.category_name')
            ->get();

        $salesPerCashier = (clone $query)
            ->leftJoin('employees', 'sales.employee_id', '=', 'employees.id')
            ->select(DB::raw('COALESCE(employees.employee_name, "Unassigned") as cashier'), DB::raw('COUNT(*) as transactions'), DB::raw('SUM(sales.total_amount) as revenue'))
            ->groupBy('employees.employee_name')
            ->orderByDesc('revenue')
            ->get();

        $paymentBreakdown = (clone $query)
            ->select(DB::raw('COALESCE(payment_type, "Cash") as payment_type'), DB::raw('COUNT(*) as transactions'), DB::raw('SUM(total_amount) as revenue'))
            ->groupBy('payment_type')
            ->orderByDesc('revenue')
            ->get();

        $bestSellingProducts = $salesPerProduct->sortByDesc('units_sold')->take(10)->values();

        if ($request->input('export') === 'excel') {
            return $this->exportHtmlTable('sales-summary.xls', 'Sales Summary', [
                ['Period', 'Transactions', 'Total Sales'],
                ...$salesByPeriod->map(fn ($row) => [$row->label, $row->transactions, number_format($row->total, 2)])->toArray(),
            ]);
        }

        return view($request->input('export') === 'pdf' ? 'reports.sales-summary-print' : 'reports.sales-summary', compact(
            'dateFrom',
            'dateTo',
            'groupBy',
            'totalSales',
            'averageOrder',
            'totalOrders',
            'discounts',
            'salesByPeriod',
            'salesPerProduct',
            'salesPerCategory',
            'salesPerCashier',
            'paymentBreakdown',
            'bestSellingProducts'
        ));
    }

    public function inventoryReport(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());

        $products = Product::with('category')->get();

        $reportData = $products->map(function($product) use ($dateFrom, $dateTo) {
            $stockIn = 0;
            if ($product->inventory_type === 'Finished Product') {
                $stockIn = ProductionInItem::whereHas('productionIn', function($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('date', [$dateFrom, $dateTo])->where('status', 'Approved');
                })->where('product_id', $product->id)->sum('quantity');
            } else {
                $stockIn = InventoryReceivingItem::whereHas('inventoryReceiving', function($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('date', [$dateFrom, $dateTo])->where('status', 'Approved');
                })->where('product_id', $product->id)->where('condition', 'Good')->sum('quantity_received');
            }

            $stockOut = StockWithdrawalItem::whereHas('stockWithdrawal', function($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('date', [$dateFrom, $dateTo])->where('status', 'Approved');
            })->where('product_id', $product->id)->sum('quantity');

            if ($product->inventory_type === 'Finished Product') {
                $stockOut += ProductionOutItem::whereHas('productionOut', function($q) use ($dateFrom, $dateTo) {
                    $q->whereBetween('date', [$dateFrom, $dateTo])->where('status', 'Approved');
                })->where('product_id', $product->id)->sum('quantity');
            }

            $sold = Sale::whereBetween('created_at', [
                Carbon::parse($dateFrom)->startOfDay(), 
                Carbon::parse($dateTo)->endOfDay()
            ])->where('product_id', $product->id)->sum('sold');

            $endingBalance = $product->quantity;
            $beginningBalance = $endingBalance - $stockIn + $stockOut + $sold;

            return (object)[
                'product_name' => $product->product_name,
                'category' => $product->category->category_name ?? 'Uncategorized',
                'type' => $product->inventory_type,
                'beginning' => $beginningBalance,
                'in' => $stockIn,
                'out' => $stockOut,
                'sold' => $sold,
                'ending' => $endingBalance
            ];
        });

        return view('reports.inventory', compact('reportData', 'dateFrom', 'dateTo'));
    }

    public function productionReports(Request $request)
    {
        $type = $request->input('type', 'all');
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());

        $productionIn = ProductionIn::with(['createdBy', 'items.product'])
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->where('status', 'Approved')
            ->get();

        $productionOut = ProductionOut::with(['createdBy', 'items.product'])
            ->whereBetween('date', [$dateFrom, $dateTo])
            ->where('status', 'Approved')
            ->get();

        if ($type === 'in') {
            $productionOut = collect();
        } elseif ($type === 'out') {
            $productionIn = collect();
        }

        $totalProductionIn = $productionIn->sum(fn ($batch) => $batch->items->sum('quantity'));
        $totalProductionOut = $productionOut->sum(fn ($batch) => $batch->items->sum('quantity'));

        $productionPerProduct = collect()
            ->merge($productionIn->flatMap(fn ($batch) => $batch->items->map(fn ($item) => [
                'product' => $item->product->product_name ?? 'Unknown',
                'type' => 'IN',
                'quantity' => $item->quantity,
            ])))
            ->merge($productionOut->flatMap(fn ($batch) => $batch->items->map(fn ($item) => [
                'product' => $item->product->product_name ?? 'Unknown',
                'type' => 'OUT',
                'quantity' => $item->quantity,
            ])))
            ->groupBy(fn ($row) => $row['product'] . '|' . $row['type'])
            ->map(fn ($rows) => (object) [
                'product' => $rows->first()['product'],
                'type' => $rows->first()['type'],
                'quantity' => $rows->sum('quantity'),
            ])
            ->values();

        $rawMaterialsConsumed = StockWithdrawalItem::with(['product', 'stockWithdrawal'])
            ->whereHas('stockWithdrawal', function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('date', [$dateFrom, $dateTo])->where('status', 'Approved');
            })
            ->get()
            ->groupBy(fn ($item) => $item->product->product_name ?? 'Unknown')
            ->map(fn ($items, $name) => (object) ['product' => $name, 'quantity' => $items->sum('quantity')])
            ->values();

        $withdrawalReasons = $productionOut
            ->groupBy(fn ($batch) => $batch->reason ?: 'Unspecified')
            ->map(fn ($rows, $reason) => (object) ['reason' => $reason, 'count' => $rows->count(), 'quantity' => $rows->sum(fn ($row) => $row->items->sum('quantity'))])
            ->values();

        $productionByEmployee = collect()
            ->merge($productionIn->map(fn ($batch) => ['employee' => $batch->createdBy->name ?? 'Unassigned', 'quantity' => $batch->items->sum('quantity')]))
            ->merge($productionOut->map(fn ($batch) => ['employee' => $batch->createdBy->name ?? 'Unassigned', 'quantity' => $batch->items->sum('quantity')]))
            ->groupBy('employee')
            ->map(fn ($rows, $employee) => (object) ['employee' => $employee, 'quantity' => $rows->sum('quantity')])
            ->values();

        $productionByPeriod = collect()
            ->merge($productionIn->map(fn ($batch) => ['date' => $batch->date->toDateString(), 'type' => 'IN', 'quantity' => $batch->items->sum('quantity')]))
            ->merge($productionOut->map(fn ($batch) => ['date' => $batch->date->toDateString(), 'type' => 'OUT', 'quantity' => $batch->items->sum('quantity')]))
            ->groupBy(fn ($row) => $row['date'] . '|' . $row['type'])
            ->map(fn ($rows) => (object) ['date' => $rows->first()['date'], 'type' => $rows->first()['type'], 'quantity' => $rows->sum('quantity')])
            ->sortBy('date')
            ->values();

        $totalHandled = $totalProductionIn + $totalProductionOut;
        $netProduction = $totalProductionIn - $totalProductionOut;
        $outflowRate = $totalProductionIn > 0 ? round(($totalProductionOut / $totalProductionIn) * 100, 1) : 0;
        $activeDays = $productionByPeriod->pluck('date')->unique()->count();
        $topProducedProduct = $productionPerProduct->where('type', 'IN')->sortByDesc('quantity')->first();
        $topOutProduct = $productionPerProduct->where('type', 'OUT')->sortByDesc('quantity')->first();
        $topConsumedMaterial = $rawMaterialsConsumed->sortByDesc('quantity')->first();
        $topWithdrawalReason = $withdrawalReasons->sortByDesc('quantity')->first();
        $recentProductionActivity = collect()
            ->merge($productionIn->map(fn ($batch) => (object) [
                'date' => $batch->date,
                'reference' => $batch->production_in_no,
                'type' => 'IN',
                'quantity' => $batch->items->sum('quantity'),
                'employee' => $batch->createdBy->name ?? 'Unassigned',
            ]))
            ->merge($productionOut->map(fn ($batch) => (object) [
                'date' => $batch->date,
                'reference' => $batch->production_out_no,
                'type' => 'OUT',
                'quantity' => $batch->items->sum('quantity'),
                'employee' => $batch->createdBy->name ?? 'Unassigned',
            ]))
            ->sortByDesc('date')
            ->take(8)
            ->values();

        if ($request->input('export') === 'excel') {
            return $this->exportHtmlTable('production-report.xls', 'Production Report', [
                ['Date', 'Type', 'Quantity'],
                ...$productionByPeriod->map(fn ($row) => [$row->date, $row->type, $row->quantity])->toArray(),
            ]);
        }

        return view($request->input('export') === 'pdf' ? 'reports.production-print' : 'reports.production', compact(
            'type',
            'dateFrom',
            'dateTo',
            'totalProductionIn',
            'totalProductionOut',
            'totalHandled',
            'netProduction',
            'outflowRate',
            'activeDays',
            'topProducedProduct',
            'topOutProduct',
            'topConsumedMaterial',
            'topWithdrawalReason',
            'recentProductionActivity',
            'productionPerProduct',
            'rawMaterialsConsumed',
            'withdrawalReasons',
            'productionByEmployee',
            'productionByPeriod'
        ));
    }

    private function exportHtmlTable(string $filename, string $title, array $rows): Response
    {
        $html = '<table><caption>' . e($title) . '</caption>';
        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . e((string) $cell) . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>';

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function adminReports()
    {
        // 1. Total Inventory Value
        $totalInventoryValue = DB::table('products')
            ->sum(DB::raw('quantity * selling_price'));

        // 2. Total Customers
        $totalCustomers = Customer::count();

        // 3. Total Employees
        $totalEmployees = Employee::count();

        // 4. Sales Metrics
        $todaySales = Sale::whereDate('created_at', today())->sum('total_amount');
        $monthlySales = Sale::whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->sum('total_amount');
        $yearlySales = Sale::whereYear('created_at', now()->year)->sum('total_amount');

        // 5. Recent Transactions
        $recentOrders = Sale::with(['product', 'customer'])->latest()->limit(5)->get();

        // 6. Top Customers (by total spent)
        $topCustomers = DB::table('sales')
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->select('customers.name', DB::raw('COUNT(sales.id) as order_count'), DB::raw('SUM(sales.total_amount) as total_spent'))
            ->groupBy('customers.id', 'customers.name')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get();

        // 7. Product Performance
        $fastMoving = DB::table('sales')
            ->join('products', 'sales.product_id', '=', 'products.id')
            ->select('products.product_name', DB::raw('SUM(sales.sold) as total_sold'), DB::raw('SUM(sales.total_amount) as revenue'))
            ->groupBy('products.id', 'products.product_name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        $slowMoving = DB::table('products')
            ->leftJoin('sales', 'products.id', '=', 'sales.product_id')
            ->select('products.product_name', DB::raw('COALESCE(SUM(sales.sold), 0) as total_sold'))
            ->where('products.inventory_type', 'Finished Product')
            ->groupBy('products.id', 'products.product_name')
            ->orderBy('total_sold', 'asc')
            ->limit(5)
            ->get();

        return view('reports.index', compact(
            'totalInventoryValue', 
            'totalCustomers', 
            'totalEmployees', 
            'todaySales',
            'monthlySales', 
            'yearlySales',
            'recentOrders', 
            'topCustomers',
            'fastMoving',
            'slowMoving'
        ));
    }

    public function managerReports()
    {
        // 1. Total Products
        $totalProducts = Product::count();

        // 2. Low Stock Count
        $lowStockCount = Product::where('quantity', '<=', DB::raw('stock_alert_threshold'))->count();

        // 3. Weekly Production
        $weeklyProduction = ProductionInItem::whereHas('productionIn', function($q) {
            $q->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])->where('status', 'Approved');
        })->sum('quantity');

        // 4. Weekly Waste (Production OUT)
        $weeklyWaste = ProductionOutItem::whereHas('productionOut', function($q) {
            $q->whereBetween('date', [now()->startOfWeek(), now()->endOfWeek()])->where('status', 'Approved');
        })->sum('quantity');

        // 5. Waste Percentage
        $wastePercent = $weeklyProduction > 0 ? round(($weeklyWaste / $weeklyProduction) * 100, 1) : 0;

        // 6. Raw Materials Stock Level
        $materials = Product::where('inventory_type', 'Raw Material')
            ->select('product_name as name', 'quantity as stock', 'stock_alert_threshold')
            ->get()
            ->map(function($item) {
                $status = 'Good';
                if ($item->stock <= 0) $status = 'Out';
                elseif ($item->stock <= $item->stock_alert_threshold) $status = 'Low';
                return [
                    'name' => $item->name,
                    'stock' => $item->stock,
                    'status' => $status
                ];
            });

        // 7. Recent Movements
        $recentMovements = Sale::with('product')->latest()->limit(5)->get();

        return view('manager-reports.index', compact(
            'totalProducts',
            'lowStockCount',
            'weeklyProduction',
            'weeklyWaste',
            'wastePercent',
            'materials',
            'recentMovements'
        ));
    }
}
