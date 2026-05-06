<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\Auth;

class ProductionInController extends Controller
{
    public function index()
    {
        // Using InventoryMovement as a source for production-in (new_luto > 0)
        $batches = InventoryMovement::where('new_luto', '>', 0)
            ->with(['product'])
            ->latest()
            ->paginate(10);
            
        return view('production-in.index', compact('batches'));
    }

    public function create()
    {
        $products = Product::all();
        return view('production-in.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $oldBalance = $product->quantity;
        
        // Update product quantity
        $product->increment('quantity', $request->quantity);

        // Record movement
        InventoryMovement::create([
            'product_id' => $request->product_id,
            'employee_id' => Auth::id() ?? 1, // Fallback for dev
            'balance_forwarded' => $oldBalance,
            'new_luto' => $request->quantity,
            'new_balance' => $oldBalance,
            'total_inventory' => $oldBalance + $request->quantity,
            'date' => now(),
            'transaction_type' => 'PRODUCTION_IN'
        ]);

        return redirect()->route('production-in.index')->with('success', 'Production recorded successfully.');
    }

    public function destroy($id)
    {
        $movement = InventoryMovement::findOrFail($id);
        
        // Reverse stock if needed, but usually production records are permanent logs
        // For this CRUD, we'll just delete the log entry
        $movement->delete();
        
        return redirect()->route('production-in.index')->with('success', 'Production record removed.');
    }
}
