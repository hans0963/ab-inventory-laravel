<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
use App\Models\InventoryMovement;
use App\Models\Sale;
use DB;

class InventoryStatusController extends Controller
{
    public function index(Request $request)
    {
        $query = Products::with('category');

        if ($request->has('product') && $request->product != '') {
            $query->where('id', $request->product);
        }

        $products = $query->get();

        $inventoryMovements = InventoryMovement::with('product')
            ->when(request('date'), function ($query) {
                $query->whereDate('date', request('date'));
            })
            ->when(request('product'), function ($query) {
                $query->where('product_id', request('product'));
            })
            ->orderByDesc('date')
            ->paginate(5); 

        $lowStockAlerts = DB::table('vw_low_stock_alerts')->get();

        $sales = DB::table('vw_recent_sales')
            ->when($request->date, function ($q) use ($request) {
                return $q->whereDate('order_date', $request->date);
            })
            ->when($request->product, function ($q) use ($request) {
                return $q->whereExists(function ($subQuery) use ($request) {
                    $subQuery->select(DB::raw(1))
                        ->from('order_details')
                        ->whereRaw('order_details.order_id = vw_recent_sales.order_id')
                        ->where('order_details.product_id', $request->product);
                });
            })
            ->orderByDesc('order_date')
            ->paginate(5); 

        return view('inventory.status', compact('products', 'inventoryMovements', 'lowStockAlerts', 'sales'));
    }

    public function salesReport(Request $request)
    {
        $salesQuery = DB::table('vw_recent_sales') //recent sales
            ->when($request->start_date, function ($q) use ($request) {
                return $q->whereDate('order_date', '>=', $request->start_date);
            })
            ->when($request->end_date, function ($q) use ($request) {
                return $q->whereDate('order_date', '<=', $request->end_date);
            })
            ->when($request->product, function ($q) use ($request) {
                return $q->whereExists(function ($subQuery) use ($request) {
                    $subQuery->select(DB::raw(1))
                        ->from('order_details')
                        ->whereRaw('order_details.order_id = vw_recent_sales.order_id')
                        ->where('order_details.product_id', $request->product);
                });
            })
            ->orderByDesc('order_date')
            ->paginate(10); 

        $products = Products::all(); 

        $bestSellingProducts = DB::table('vw_best_selling_products') // best selling view
            ->orderByDesc('total_sold')
            ->paginate(5); 

        $salesSummary = DB::table('vw_sales_summary')->first();

        return view('inventory.sales', compact('salesQuery', 'products', 'bestSellingProducts', 'salesSummary'));
    }

}
