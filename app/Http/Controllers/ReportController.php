<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function salesReport()
    {
        // Summary Metrics
        $totalSales = DB::table('orders')->sum('total');
        $totalOrders = DB::table('orders')->count();
        $averageOrder = $totalOrders > 0 ? $totalSales / $totalOrders : 0;
        
        $discounts = 0; 

        // Top Selling Products from the view
        $topProductsData = DB::table('vw_best_selling_products')->limit(5)->get();
        
        $maxRevenue = $topProductsData->max('total_revenue') ?: 1;
        
        $topProducts = $topProductsData->map(function($product) use ($maxRevenue) {
            return (object)[
                'name' => $product->product_name,
                'units_sold' => $product->total_sold,
                'revenue' => $product->total_revenue,
                'performance' => ($product->total_revenue / $maxRevenue) * 100
            ];
        });

        // Add variables expected by inventory.sales view if needed
        $salesQuery = DB::table('vw_recent_sales')->paginate(10);
        $products = DB::table('products')->get();
        $bestSellingProducts = DB::table('vw_best_selling_products')->paginate(5);
        $salesSummary = DB::table('vw_sales_summary')->first();

        return view('inventory.sales', compact(
            'totalSales', 
            'averageOrder', 
            'totalOrders', 
            'discounts', 
            'topProducts',
            'salesQuery',
            'products',
            'bestSellingProducts',
            'salesSummary'
        ));
    }

    public function adminReports()
    {
        // 1. Total Inventory Value
        $totalInventoryValue = DB::table('products')
            ->sum(DB::raw('quantity * selling_price'));

        // 2. Total Customers
        $totalCustomers = DB::table('customers')->count();

        // 3. Total Employees
        $totalEmployees = DB::table('employees')->count();

        // 4. Monthly Sales (Current Month)
        $monthlySales = DB::table('orders')
            ->whereMonth('order_date', now()->month)
            ->whereYear('order_date', now()->year)
            ->sum('total');

        // 5. Recent Orders (Last 5)
        $recentOrders = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->select('orders.*', 'customers.name as customer_name')
            ->latest('order_date')
            ->limit(5)
            ->get();

        // 6. Top Customers (by total spent)
        $topCustomers = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->select('customers.name', DB::raw('SUM(orders.total) as total_spent'), DB::raw('COUNT(orders.id) as order_count'))
            ->groupBy('customers.id', 'customers.name')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get();

        // 7. Top Selling Products
        $topProducts = DB::table('vw_best_selling_products')
            ->select('product_name as name', 'total_sold as quantity_sold', 'total_revenue as revenue')
            ->limit(5)
            ->get();

        // 8. Chart Data: Monthly Sales Trend (Last 6 Months)
        $monthlySalesQuery = DB::table('orders')
            ->select(
                DB::raw("DATE_FORMAT(order_date, '%b') as month"),
                DB::raw('SUM(total) as total')
            )
            ->where('order_date', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('order_date')
            ->get();

        $monthlySalesLabels = $monthlySalesQuery->pluck('month');
        $monthlySalesData = $monthlySalesQuery->pluck('total');

        // 9. Chart Data: Sales by Category
        $categorySalesQuery = DB::table('vw_best_selling_products')
            ->select('category_name', DB::raw('SUM(total_revenue) as revenue'))
            ->groupBy('category_name')
            ->get();

        $categoryLabels = $categorySalesQuery->pluck('category_name');
        $categorySalesData = $categorySalesQuery->pluck('revenue');

        return view('reports.index', compact(
            'totalInventoryValue',
            'totalCustomers',
            'totalEmployees',
            'monthlySales',
            'recentOrders',
            'topCustomers',
            'topProducts',
            'monthlySalesLabels',
            'monthlySalesData',
            'categoryLabels',
            'categorySalesData'
        ));
    }

    public function managerReports()
    {
        // 1. Total Products
        $totalProducts = DB::table('products')->count();

        // 2. Low Stock Count
        $lowStockCount = DB::table('vw_low_stock_alerts')->count();

        // 3. Total Categories
        $totalCategories = DB::table('categories')->count();

        // 4. Total Stock Quantity
        $totalStock = DB::table('products')->sum('quantity');

        // 5. Top Selling Products (Last 5)
        $topSelling = DB::table('vw_best_selling_products')
            ->limit(5)
            ->get();

        // 6. Recent Stock Movements (Last 5)
        $recentMovements = DB::table('inventory_movements')
            ->join('products', 'inventory_movements.product_id', '=', 'products.id')
            ->select('inventory_movements.*', 'products.product_name')
            ->latest('date')
            ->limit(5)
            ->get();

        return view('manager-reports.index', compact(
            'totalProducts',
            'lowStockCount',
            'totalCategories',
            'totalStock',
            'topSelling',
            'recentMovements'
        ));
    }
}
