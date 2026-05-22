<?php

namespace App\Http\Controllers;

use App\Models\InventoryReceiving;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ProcurementManagementController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(
            $request->user()->can('view-purchases') || $request->user()->can('view-inventory-receiving'),
            403
        );

        $tab = $request->get('tab', 'purchase');
        $status = $request->get('status');
        $supplierId = $request->get('supplier_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $suppliers = Supplier::where('status', 'Active')->get();

        $purchases = Purchase::with(['supplier', 'details'])
            ->when($status, fn($query) => $query->where('status', $status))
            ->when($supplierId, fn($query) => $query->where('supplier_id', $supplierId))
            ->when($dateFrom, fn($query) => $query->whereDate('purchase_date', '>=', $dateFrom))
            ->when($dateTo, fn($query) => $query->whereDate('purchase_date', '<=', $dateTo))
            ->latest('purchase_date')
            ->paginate(12, ['*'], 'purchase_page');

        $receivings = InventoryReceiving::with(['supplier', 'purchase', 'items'])
            ->when($status, fn($query) => $query->where('status', $status))
            ->when($supplierId, fn($query) => $query->where('supplier_id', $supplierId))
            ->when($dateFrom, fn($query) => $query->whereDate('date', '>=', $dateFrom))
            ->when($dateTo, fn($query) => $query->whereDate('date', '<=', $dateTo))
            ->latest('date')
            ->paginate(12, ['*'], 'receiving_page');

        return view('procurement-management.index', compact('tab', 'suppliers', 'purchases', 'receivings'));
    }
}
