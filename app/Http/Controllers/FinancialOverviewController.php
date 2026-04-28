<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Http\Request;

class FinancialOverviewController extends Controller
{
    public function index()
    {
        // Calculate revenue from orders
        $totalRevenue = Order::sum('total_amount') ?? 0;
        $orderCount = Order::count();
        
        // Calculate expenses from purchases
        $totalExpenses = Purchase::join('purchase_details', 'purchases.id', '=', 'purchase_details.purchase_id')
            ->selectRaw('SUM(purchase_details.quantity * purchase_details.unit_price) as total')
            ->first()
            ->total ?? 0;
        
        $purchaseCount = Purchase::count();
        
        // Calculate profit/loss
        $profit = $totalRevenue - $totalExpenses;
        $profitMargin = $totalRevenue > 0 ? ($profit / $totalRevenue) * 100 : 0;
        
        // Monthly data for chart
        $monthlyData = Order::selectRaw('MONTH(created_at) as month, SUM(total_amount) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        // Recent transactions
        $recentOrders = Order::latest()->take(5)->get();
        $recentPurchases = Purchase::latest()->take(5)->get();
        
        return view('financial.overview', compact(
            'totalRevenue',
            'totalExpenses',
            'profit',
            'profitMargin',
            'orderCount',
            'purchaseCount',
            'monthlyData',
            'recentOrders',
            'recentPurchases'
        ));
    }
}
