<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Products;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\Auth;

class ProductionOutController extends Controller
{
    public function index()
    {
        // Using InventoryMovement as a source for production-out (pull_out > 0 and type != SALE)
        $losses = InventoryMovement::where('pull_out', '>', 0)
            ->where('transaction_type', '!=', 'SALE')
            ->with(['product', 'employee'])
            ->latest()
            ->paginate(10);
            
        return view('production-out.index', compact('losses'));
    }

    public function create()
    {
        $products = Products::all();
        return view('production-out.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string',
        ]);

        $product = Products::findOrFail($request->product_id);
        $oldBalance = $product->quantity;
        
        // Update product quantity (decrement for pull out)
        $product->decrement('quantity', min($request->quantity, $oldBalance));

        // Record movement
        InventoryMovement::create([
            'product_id' => $request->product_id,
            'employee_id' => Auth::id() ?? 1,
            'balance_forwarded' => $oldBalance,
            'pull_out' => $request->quantity,
            'new_balance' => max(0, $oldBalance - $request->quantity),
            'new_luto' => 0,
            'total_inventory' => max(0, $oldBalance - $request->quantity),
            'date' => now(),
            'transaction_type' => 'PULL_OUT',
            'remarks' => $request->reason // Assuming we can use a remarks field or just the type
        ]);

        return redirect()->route('production-out.index')->with('success', 'Pull-out recorded successfully.');
    }

    public function destroy($id)
    {
        $movement = InventoryMovement::findOrFail($id);
        $movement->delete();
        return redirect()->route('production-out.index')->with('success', 'Pull-out record removed.');
    }
}
