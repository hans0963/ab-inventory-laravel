<?php

namespace App\Http\Controllers;

use App\Models\ProductionOut;
use App\Models\ProductionOutItem;
use App\Models\Product;
use App\Http\Requests\StoreProductionOutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductionOutController extends Controller
{
    public function index(Request $request)
    {
        $query = ProductionOut::with('items', 'createdBy', 'approvedBy');

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->where('date', '<=', $request->date_to);
        }

        $productionOuts = $query->latest()->paginate(10);
        return view('production-out.index', compact('productionOuts'));
    }

    public function create()
    {
        $products = Product::where('status', 'Active')
                          ->where('inventory_type', 'Finished Product')
                          ->where('quantity', '>', 0)
                          ->get();
        return view('production-out.create', compact('products'));
    }

    public function store(StoreProductionOutRequest $request)
    {
        try {
            DB::beginTransaction();

            // Validate stock availability
            foreach ($request->items as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                if ($product->quantity < $itemData['quantity']) {
                    return redirect()->back()
                        ->with('error', "Insufficient stock for {$product->product_name}. Available: {$product->quantity}");
                }
            }

            $productionOut = ProductionOut::create([
                'production_out_no' => ProductionOut::generateProductionOutNo(),
                'date' => $request->date,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
                'created_date' => now()->toDateString(),
                'status' => 'Pending'
            ]);

            foreach ($request->items as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                $itemValue = $itemData['quantity'] * $itemData['unit_price'];
                ProductionOutItem::create([
                    'production_out_id' => $productionOut->id,
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'total_value' => $itemValue
                ]);
            }

            DB::commit();

            return redirect()->route('production-out.show', $productionOut->id)
                           ->with('success', 'Production OUT created successfully. Reference: ' . $productionOut->production_out_no);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create production OUT: ' . $e->getMessage());
        }
    }

    public function show(ProductionOut $productionOut)
    {
        $productionOut->load('items.product', 'createdBy', 'approvedBy');
        return view('production-out.show', compact('productionOut'));
    }

    public function approve(ProductionOut $productionOut)
    {
        if (!Auth::user()->can('view-inventory')) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        try {
            DB::beginTransaction();

            $productionOut->approve(Auth::user());

            DB::commit();

            return redirect()->route('production-out.show', $productionOut->id)
                           ->with('success', 'Production OUT approved and stock updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to approve: ' . $e->getMessage());
        }
    }

    public function reject(ProductionOut $productionOut)
    {
        if (!Auth::user()->can('view-inventory')) {
            return redirect()->back()->with('error', 'Unauthorized');
        }

        $productionOut->reject(Auth::user());

        return redirect()->route('production-out.show', $productionOut->id)
                       ->with('success', 'Production OUT rejected.');
    }

    public function destroy(ProductionOut $productionOut)
    {
        if ($productionOut->status !== 'Pending') {
            return redirect()->back()->with('error', 'Cannot delete approved or rejected production OUT records.');
        }

        $productionOut->delete();

        return redirect()->route('production-out.index')
                       ->with('success', 'Production OUT deleted successfully.');
    }
}
