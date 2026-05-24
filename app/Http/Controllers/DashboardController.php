<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Employee;
use App\Models\Customer;
use App\Models\InventoryMovement;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Common metrics
        $todaySales = \App\Models\Sale::whereDate('created_at', today())->sum('total_amount');
        $totalOrdersToday = \App\Models\Sale::whereDate('created_at', today())->count();
        $totalProducts = Product::count();
        $lowStockItemCount = Product::whereColumn('quantity', '<=', 'stock_alert_threshold')->count();
        $totalCustomers = Customer::count();
        $monthlySales = \App\Models\Sale::whereMonth('created_at', now()->month)
                                     ->whereYear('created_at', now()->year)
                                     ->sum('total_amount');
        $yearlySales = \App\Models\Sale::whereYear('created_at', now()->year)->sum('total_amount');

        $lastSevenDays = collect(range(6, 0))->map(function ($days) {
            return today()->subDays($days);
        });

        $dailySalesRaw = \App\Models\Sale::whereBetween('created_at', [today()->subDays(6)->startOfDay(), today()->endOfDay()])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('total', 'date');

        $dailySalesLabels = $lastSevenDays->map(function ($date) {
            return $date->format('D d');
        })->toArray();

        $dailySalesData = $lastSevenDays->map(function ($date) use ($dailySalesRaw) {
            return (float) ($dailySalesRaw[$date->toDateString()] ?? 0);
        })->toArray();

        $currentMonthStart = now()->startOfMonth();
        $currentDay = now()->day;

        $revenueTrendRaw = \App\Models\Sale::whereBetween('created_at', [$currentMonthStart, now()->endOfDay()])
            ->selectRaw('DAY(created_at) as day, SUM(total_amount) as total')
            ->groupBy(DB::raw('DAY(created_at)'))
            ->pluck('total', 'day')
            ->toArray();

        $revenueTrendLabels = collect(range(1, $currentDay))->map(function ($day) {
            return $day;
        })->toArray();

        $revenueTrendData = collect(range(1, $currentDay))->map(function ($day) use ($revenueTrendRaw) {
            return (float) ($revenueTrendRaw[$day] ?? 0);
        })->toArray();

        $recentStockUpdates = InventoryMovement::with('product', 'employee')->latest('created_at')->limit(5)->get();

        $fastMovingProducts = DB::table('sales')
            ->join('products', 'sales.product_id', '=', 'products.id')
            ->select('products.product_name', DB::raw('SUM(sales.sold) as total_sold'))
            ->groupBy('products.id', 'products.product_name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        $slowMovingProducts = DB::table('products')
            ->leftJoin('sales', 'products.id', '=', 'sales.product_id')
            ->select('products.product_name', DB::raw('COALESCE(SUM(sales.sold), 0) as total_sold'))
            ->where('products.inventory_type', 'Finished Product')
            ->groupBy('products.id', 'products.product_name')
            ->orderBy('total_sold', 'asc')
            ->limit(5)
            ->get();

        $salesByCategory = DB::table('sales')
            ->join('products', 'sales.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.category_name', DB::raw('SUM(sales.total_amount) as revenue'))
            ->groupBy('categories.category_name')
            ->get();

        // Admin Dashboard
        if ($user->role === 'admin') {
            $totalSalesCount = \App\Models\Sale::count();
            $totalRevenue = \App\Models\Sale::sum('total_amount');
            $totalEmployees = Employee::count();
            $totalProducts = Product::count();
            
            $recentOrders = \App\Models\Sale::with('product', 'customer')->latest()->limit(5)->get();
            $lowStockAlerts = Product::where('quantity', '<=', DB::raw('stock_alert_threshold'))->get();

            return view('dashboards.dashboard-admin', compact(
                'totalSalesCount', 'totalRevenue', 'totalEmployees', 'totalProducts', 'recentOrders', 'lowStockAlerts',
                'todaySales', 'monthlySales', 'yearlySales', 'fastMovingProducts', 'slowMovingProducts', 'salesByCategory',
                'totalOrdersToday', 'lowStockItemCount', 'totalCustomers', 'dailySalesLabels', 'dailySalesData', 'revenueTrendLabels', 'revenueTrendData', 'recentStockUpdates'
            ));
        }

        // Manager Dashboard
        if ($user->role === 'manager') {
            $totalOrders = \App\Models\Sale::count();
            $totalProducts = Product::count();
            $totalCustomers = Customer::count();
            
            $recentOrders = \App\Models\Sale::with('product', 'customer')->latest()->limit(5)->get();
            $lowStockAlerts = Product::where('quantity', '<=', DB::raw('stock_alert_threshold'))->get();

            return view('dashboards.dashboard-manager', compact(
                'todaySales', 'monthlySales', 'yearlySales', 'totalOrders', 'totalProducts', 'totalCustomers', 'recentOrders', 'lowStockAlerts',
                'fastMovingProducts', 'slowMovingProducts', 'salesByCategory',
                'totalOrdersToday', 'lowStockItemCount', 'dailySalesLabels', 'dailySalesData', 'revenueTrendLabels', 'revenueTrendData', 'recentStockUpdates'
            ));
        }

        // Cashier Dashboard
        if ($user->role === 'cashier') {
            $todaysSales = $todaySales;
            $transactionsCount = \App\Models\Sale::whereDate('created_at', today())->count();
            $recentOrders = \App\Models\Sale::with('product', 'customer')->whereDate('created_at', today())->latest()->limit(5)->get();
            $lowStockAlerts = Product::where('quantity', '<=', DB::raw('stock_alert_threshold'))->get();

            return view('dashboards.dashboard-cashier', compact(
                'todaysSales', 'transactionsCount', 'recentOrders', 'lowStockAlerts'
            ));
        }

        // Baker Dashboard
        if ($user->role === 'baker') {
            $totalOrders = \App\Models\Sale::count();
            $totalProducts = Product::count();
            $totalCustomers = Customer::count();

            $recentOrders = \App\Models\Sale::with('product', 'customer')->latest()->limit(5)->get();
            $lowStockAlerts = Product::where('quantity', '<=', DB::raw('stock_alert_threshold'))->get();

            return view('dashboards.dashboard-manager', compact(
                'todaySales', 'monthlySales', 'yearlySales', 'totalOrders', 'totalProducts', 'totalCustomers', 'recentOrders', 'lowStockAlerts',
                'fastMovingProducts', 'slowMovingProducts', 'salesByCategory',
                'totalOrdersToday', 'lowStockItemCount', 'dailySalesLabels', 'dailySalesData', 'revenueTrendLabels', 'revenueTrendData', 'recentStockUpdates'
            ));
        }

        // HR Dashboard
        if ($user->role === 'hr') {
            $totalEmployees = Employee::count();
            $recentEmployees = Employee::latest()->limit(5)->get();

            return view('dashboards.dashboard-hr', compact(
                'totalEmployees', 'recentEmployees',
                'todaySales', 'monthlySales', 'yearlySales', 'fastMovingProducts', 'slowMovingProducts', 'salesByCategory'
            ));
        }

        return view('dashboard');
    }
}
