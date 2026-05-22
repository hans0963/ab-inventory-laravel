<?php

namespace App\Http\Controllers;

use App\Models\ProductionIn;
use App\Models\ProductionOut;
use Illuminate\Http\Request;

class ProductionManagementController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->can('view-production-in') || $request->user()->can('view-production-out'), 403);

        $tab = $request->get('tab', 'in');
        $status = $request->get('status');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        $productionIns = ProductionIn::with(['items.product', 'createdBy'])
            ->when($status, fn($query) => $query->where('status', $status))
            ->when($dateFrom, fn($query) => $query->where('date', '>=', $dateFrom))
            ->when($dateTo, fn($query) => $query->where('date', '<=', $dateTo))
            ->latest()
            ->paginate(8, ['*'], 'ins_page');

        $productionOuts = ProductionOut::with(['items.product', 'createdBy'])
            ->when($status, fn($query) => $query->where('status', $status))
            ->when($dateFrom, fn($query) => $query->where('date', '>=', $dateFrom))
            ->when($dateTo, fn($query) => $query->where('date', '<=', $dateTo))
            ->latest()
            ->paginate(8, ['*'], 'outs_page');

        return view('production-management.index', compact('productionIns', 'productionOuts', 'tab'));
    }
}
