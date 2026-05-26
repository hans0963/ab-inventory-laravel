<?php

namespace App\Http\Controllers;

use App\Models\ProductionIn;
use App\Models\ProductionInItem;
use App\Models\Product;
use App\Http\Requests\StoreProductionInRequest;
use App\Services\SystemNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductionInController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductionIn::with('items', 'createdBy', 'approvedBy');

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('date', '<=', $request->date_to);
        }

        $productionIns = $query->latest()->paginate(10);
        return view('production-in.index', compact('productionIns'));
    }

    public function create()
    {
        $products = Product::where('status', 'Active')
                          ->where('inventory_type', 'Finished Product')
                          ->get();
        return view('production-in.create', compact('products'));
    }

    public function store(StoreProductionInRequest $request)
    {
        try {
            DB::beginTransaction();

            $productionIn = ProductionIn::create([
                'production_in_no' => ProductionIn::generateProductionInNo(),
                'date' => $request->date,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
                'created_date' => now()->toDateString(),
                'status' => 'Pending'
            ]);

            $totalValue = 0;
            foreach ($request->items as $itemData) {
                $itemValue = $itemData['quantity'] * $itemData['unit_price'];
                ProductionInItem::create([
                    'production_in_id' => $productionIn->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'total_value' => $itemValue,
                    'expiration_date' => $itemData['expiration_date'] ?? null
                ]);
                $totalValue += $itemValue;
            }

            $productionIn->total_inventory_value = $totalValue;
            $productionIn->save();

            DB::commit();

            SystemNotificationService::notifyRoles(
                ['admin', 'manager'],
                'production_in_pending',
                'Production IN needs approval',
                "Production IN {$productionIn->production_in_no} was submitted for approval.",
                route('production-in.show', $productionIn)
            );

            return redirect()->route('production-in.show', $productionIn->id)
                           ->with('success', 'Production IN created successfully. Reference: ' . $productionIn->production_in_no);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create production IN: ' . $e->getMessage());
        }
    }

    public function show(ProductionIn $productionIn)
    {
        $productionIn->load('items.product', 'createdBy', 'approvedBy');
        return view('production-in.show', compact('productionIn'));
    }

    public function approve(ProductionIn $productionIn)
    {
        if (!Auth::user()->can('view-inventory')) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        try {
            DB::beginTransaction();

            $productionIn->approve(Auth::user());

            DB::commit();

            SystemNotificationService::notifyUser(
                $productionIn->created_by,
                'production_in_approved',
                'Production IN approved',
                "Production IN {$productionIn->production_in_no} has been approved.",
                route('production-in.show', $productionIn)
            );

            return redirect()->route('production-in.show', $productionIn->id)
                           ->with('success', 'Production IN approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to approve: ' . $e->getMessage());
        }
    }

    public function reject(ProductionIn $productionIn)
    {
        if (!Auth::user()->can('view-inventory')) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $productionIn->reject(Auth::user());

        SystemNotificationService::notifyUser(
            $productionIn->created_by,
            'production_in_rejected',
            'Production IN rejected',
            "Production IN {$productionIn->production_in_no} has been rejected.",
            route('production-in.show', $productionIn)
        );

        return redirect()->route('production-in.show', $productionIn->id)
                       ->with('success', 'Production IN rejected.');
    }

    public function destroy(ProductionIn $productionIn)
    {
        if ($productionIn->status !== 'Pending') {
            return redirect()->back()->with('error', 'Cannot delete approved or rejected production IN records.');
        }

        $productionIn->delete();

        return redirect()->route('production-in.index')
                       ->with('success', 'Production IN deleted successfully.');
    }
}
