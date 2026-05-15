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

class ReportController extends Controller
{
    public function salesReport(Request $request)
    {
        $user = auth()->user();
        $period = $request->input('period', 'all'); // today, month, year, all

        $query = DB::table('sales');

        if ($period === 'today') {
            $query->whereDate('sales.created_at', today());
            $chartDays = 1;
        } elseif ($period === 'month') {
            $query->whereMonth('sales.created_at', now()->month)
                  ->whereYear('sales.created_at', now()->year);
            $chartDays = 30;
        } elseif ($period === 'year') {
            $query->whereYear('sales.created_at', now()->year);
            $chartDays = 365;
        } else {
            $chartDays = 7;
        }

        // Summary Metrics for the selected period
        $totalSales = (clone $query)->sum('total_amount');
        $totalOrders = (clone $query)->count();
        $averageOrder = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
        $discounts = (clone $query)->sum('discount_amount'); 

        // Top Selling Products for the period
        $topProductsData = (clone $query)
            ->join('products', 'sales.product_id', '=', 'products.id')
            ->select('products.product_name as name', DB::raw('SUM(sales.sold) as units_sold'), DB::raw('SUM(sales.total_amount) as revenue'))
            ->groupBy('products.id', 'products.product_name')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        $maxRevenue = $topProductsData->max(function($item) {
            return (float)$item->revenue;
        }) ?: 1;
        
        $topProducts = $topProductsData->map(function($product) use ($maxRevenue) {
            $revenue = (float)$product->revenue;
            return (object)[
                'name' => $product->name,
                'units_sold' => $product->units_sold,
                'revenue' => $revenue,
                'performance' => $maxRevenue > 0 ? ($revenue / $maxRevenue) * 100 : 0
            ];
        });

        // Chart Data based on period
        if ($period === 'year') {
            // Monthly grouping for year view
            $chartData = DB::table('sales')
                ->select(DB::raw('MONTHNAME(created_at) as label'), DB::raw('SUM(total_amount) as total'))
                ->whereYear('created_at', now()->year)
                ->groupBy(DB::raw('MONTH(created_at)'), DB::raw('MONTHNAME(created_at)'))
                ->orderBy(DB::raw('MONTH(created_at)'))
                ->get();
        } else {
            // Daily grouping
            $days = $period === 'today' ? 0 : ($period === 'month' ? 30 : 7);
            $chartData = DB::table('sales')
                ->select(DB::raw('DATE(created_at) as label'), DB::raw('SUM(total_amount) as total'))
                ->where('created_at', '>=', now()->subDays($days))
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('label', 'asc')
                ->get();
        }

        $chartLabels = $chartData->pluck('label');
        $chartValues = $chartData->pluck('total');

        // Chart Data: Sales by Category (for the period)
        $salesByCategory = (clone $query)
            ->join('products', 'sales.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.category_name', DB::raw('SUM(sales.total_amount) as revenue'))
            ->groupBy('categories.category_name')
            ->get();

        $categoryLabels = $salesByCategory->pluck('category_name');
        $categorySalesData = $salesByCategory->pluck('revenue');

        $view = $user->role === 'admin' ? 'sales-report.index' : 'manager-sales-report.index';

        return view($view, compact(
            'totalSales', 
            'averageOrder', 
            'totalOrders', 
            'discounts', 
            'topProducts',
            'chartLabels',
            'chartValues',
            'categoryLabels',
            'categorySalesData',
            'period'
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
        $type = $request->input('type', 'in');
        $viewType = $request->input('view', 'summary');
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());

        if ($type === 'in') {
            $query = ProductionIn::with(['createdBy', 'approvedBy'])
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->where('status', 'Approved');
            
            if ($viewType === 'detailed') {
                $query->with('items.product');
            }
            
            $data = $query->get();
            return view('reports.production-in', compact('data', 'viewType', 'dateFrom', 'dateTo'));
        } else {
            $query = ProductionOut::with(['createdBy', 'approvedBy'])
                ->whereBetween('date', [$dateFrom, $dateTo])
                ->where('status', 'Approved');
            
            if ($viewType === 'detailed') {
                $query->with('items.product');
            }
            
            $data = $query->get();
            return view('reports.production-out', compact('data', 'viewType', 'dateFrom', 'dateTo'));
        }
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
