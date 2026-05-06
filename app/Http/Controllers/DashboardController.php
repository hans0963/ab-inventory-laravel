<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Employee;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Admin Dashboard
        if ($user->role === 'admin') {
            $totalSalesCount = Order::count();
            $totalRevenue = Order::sum('total');
            $totalEmployees = Employee::count();
            $totalProducts = Product::count();
            
            $recentOrders = Order::latest()->limit(5)->get();
            $lowStockAlerts = DB::table('vw_low_stock_alerts')->get();

            return view('dashboards.dashboard-admin', compact(
                'totalSalesCount', 'totalRevenue', 'totalEmployees', 'totalProducts', 'recentOrders', 'lowStockAlerts'
            ));
        }

        // Manager Dashboard
        if ($user->role === 'manager') {
            $todaySales = Order::whereDate('order_date', today())->sum('total');
            $totalOrders = Order::count();
            $totalProducts = Product::count();
            $totalCustomers = Customer::count();
            
            $recentOrders = Order::latest()->limit(5)->get();
            $lowStockAlerts = DB::table('vw_low_stock_alerts')->get();

            return view('dashboards.dashboard-manager', compact(
                'todaySales', 'totalOrders', 'totalProducts', 'totalCustomers', 'recentOrders', 'lowStockAlerts'
            ));
        }

        // Cashier Dashboard
        if ($user->role === 'cashier') {
            $todaysSales = Order::whereDate('order_date', today())->sum('total');
            $transactionsCount = Order::whereDate('order_date', today())->count();
            $recentOrders = Order::whereDate('order_date', today())->latest()->limit(5)->get();
            $lowStockAlerts = DB::table('vw_low_stock_alerts')->get();

            return view('dashboards.dashboard-cashier', compact(
                'todaysSales', 'transactionsCount', 'recentOrders', 'lowStockAlerts'
            ));
        }

        return view('dashboard');
    }
}
