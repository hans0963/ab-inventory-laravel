<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userRole = $user->role;

        // Common data for all dashboards
        $lowStockAlerts = DB::table('vw_low_stock_alerts')->get();
        $totalSalesCount = DB::table('orders')->count();
        $totalRevenue = DB::table('orders')->sum('total');
        $totalProducts = DB::table('products')->count();
        $totalCustomers = DB::table('customers')->count();
        $totalEmployees = DB::table('employees')->count();

        // Role-specific data
        switch($userRole) {
            case 'admin':
                $recentOrders = DB::table('orders')
                    ->join('customers', 'orders.customer_id', '=', 'customers.id')
                    ->select('orders.*', 'customers.name as customer_name')
                    ->latest()
                    ->limit(5)
                    ->get();
                
                return view('dashboards.dashboard-admin', compact(
                    'lowStockAlerts', 
                    'totalSalesCount', 
                    'totalRevenue', 
                    'totalEmployees', 
                    'totalProducts',
                    'recentOrders'
                ));

            case 'manager':
                $todaySales = DB::table('orders')
                    ->whereDate('order_date', now())
                    ->sum('total');
                $totalOrders = $totalSalesCount;
                $pendingOrders = DB::table('orders')->where('order_status', 'Pending')->count();
                $recentOrders = DB::table('orders')
                    ->join('customers', 'orders.customer_id', '=', 'customers.id')
                    ->select('orders.*', 'customers.name as customer_name')
                    ->latest()
                    ->limit(5)
                    ->get();
                
                return view('dashboards.dashboard-manager', compact(
                    'lowStockAlerts', 
                    'todaySales', 
                    'totalOrders', 
                    'pendingOrders',
                    'totalProducts',
                    'totalCustomers',
                    'recentOrders'
                ));

            case 'cashier':
                $todaysSales = DB::table('orders')
                    ->whereDate('order_date', now())
                    ->sum('total');
                $transactionsCount = DB::table('orders')
                    ->whereDate('order_date', now())
                    ->count();
                $recentOrders = DB::table('orders')
                    ->join('customers', 'orders.customer_id', '=', 'customers.id')
                    ->select('orders.*', 'customers.name as customer_name')
                    ->whereDate('order_date', now())
                    ->latest()
                    ->limit(5)
                    ->get();
                
                return view('dashboards.dashboard-cashier', compact(
                    'lowStockAlerts', 
                    'todaysSales', 
                    'transactionsCount',
                    'totalSalesCount', 
                    'totalRevenue', 
                    'totalProducts',
                    'totalEmployees',
                    'recentOrders'
                ));

            default:
                return redirect()->route('login')->with('error', 'Unauthorized role.');
        }
    }
}
