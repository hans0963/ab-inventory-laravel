<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Products;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function create()
    {
        $products = Products::all();
        return view('transactions.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'transactions' => 'required|array',
            'transactions.*.product_id' => 'required|exists:products,id',
            'transactions.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->transactions as $item) {
                $product = Products::find($item['product_id']);
                if ($product) {
                    $product->stock -= $item['quantity']; // Deduct stock
                    $product->save();
                }
            }
        });

        return redirect()->route('dashboard')->with('success', 'Transaction completed successfully!');
    }
}

