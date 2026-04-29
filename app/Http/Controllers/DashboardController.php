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

        // Common data
        $lowStockAlerts = DB::table('vw_low_stock_alerts')->get();

        // Role-specific data
        switch($userRole) {
            case 'admin':
                $totalSales = DB::table('sales')->count();
                // Calculate total revenue from sales (quantity * selling_price from products)
                $totalRevenue = DB::table('sales')
                    ->join('products', 'sales.product_id', '=', 'products.id')
                    ->sum(DB::raw('sales.sold * products.selling_price'));
                $totalEmployees = DB::table('employees')->count();
                $totalProducts = DB::table('products')->count();
                
                return view('dashboards.dashboard-admin', compact('lowStockAlerts', 'totalSales', 'totalRevenue', 'totalEmployees', 'totalProducts'));

            case 'manager':
                // Calculate monthly sales revenue
                $monthlySales = DB::table('sales')
                    ->join('products', 'sales.product_id', '=', 'products.id')
                    ->whereMonth('sales.date', now()->month)
                    ->sum(DB::raw('sales.sold * products.selling_price'));
                $totalOrders = DB::table('orders')->count();
                $pendingOrders = DB::table('orders')->where('order_status', 'Pending')->count();
                
                return view('dashboards.dashboard-manager', compact('lowStockAlerts', 'monthlySales', 'totalOrders', 'pendingOrders'));

            case 'cashier':
                // Calculate today's sales revenue
                $todaysSales = DB::table('sales')
                    ->join('products', 'sales.product_id', '=', 'products.id')
                    ->whereDate('sales.date', now())
                    ->sum(DB::raw('sales.sold * products.selling_price'));
                $transactionsCount = DB::table('sales')
                    ->whereDate('sales.date', now())
                    ->count();
                
                return view('dashboards.dashboard-cashier', compact('lowStockAlerts', 'todaysSales', 'transactionsCount'));

            default:
                return redirect()->route('login')->with('error', 'Unauthorized role.');
        }
    }
}
