<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\InventoryMovement;
use App\Models\Products;

class SalesController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'product_id' => 'required|array',
            'quantities' => 'required|array'
        ]);

        foreach ($request->product_id as $index => $productId) {
            $quantity = $request->quantities[$index];
            $product = Products::find($productId);

            // Ensure stock is available
            if ($product->stock_quantity < $quantity) {
                return back()->withErrors(['error' => 'Not enough stock for ' . $product->product_name]);
            }

            // Record Sale
            Sale::create([
                'product_id' => $productId,
                'date' => $request->date,
                'sold' => $quantity,
                'employee_id' => auth()->id(),
            ]);

            // Record Inventory Movement (STACK OUT)
            InventoryMovement::create([
                'product_id' => $productId,
                'employee_id' => auth()->id(),
                'date' => now(),
                'balance_forwarded' => $product->stock_quantity,
                'pull_out' => $quantity, // Stock removed
                'new_balance' => $product->stock_quantity - $quantity,
                'new_luto' => 0,
                'total_inventory' => $product->stock_quantity - $quantity,
            ]);

            // Update Product Stock
            $product->decrement('stock_quantity', $quantity);
        }

        return redirect()->route('sales.index')->with('success', 'Sale recorded & Inventory Updated');
    }
}
