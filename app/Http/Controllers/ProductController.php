<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('product_name', 'LIKE', "%{$search}%")
                  ->orWhereHas('category', function($q) use ($search) {
                      $q->where('category_name', 'LIKE', "%{$search}%");
                  });
        }

        if ($request->has('category') && $request->category != '') {
            $query->where('category_id', $request->category);
        }

        $products = $query->latest()->paginate(10);
        $categories = Category::all();
        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'selling_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'stock_alert_threshold' => 'required|integer|min:0',
        ]);

        $validated['buying_price'] = 0; // Defaulting to 0 since it's removed from UI

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'selling_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'stock_alert_threshold' => 'required|integer|min:0',
        ]);

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        // Check if product has sales history in order_details
        if ($product->orderDetails()->exists()) {
            return redirect()->route('products.index')->with('error', 'Cannot delete "' . $product->product_name . '" because it has existing sales records. You should keep it for historical reporting.');
        }

        $product_name = $product->product_name;
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product "' . $product_name . '" deleted successfully.');
    }
}

