<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
{
    $lowStockAlerts = DB::table('vw_low_stock_alerts')->get();

    return view('dashboard', compact('lowStockAlerts'));
}
}
