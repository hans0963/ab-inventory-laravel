<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->productQuery($request);
        $products = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::all();
        $totalProducts = Product::count();
        $outOfStockCount = Product::where('quantity', '<=', 0)->count();
        $lowStockCount = Product::whereColumn('quantity', '<=', 'stock_alert_threshold')->where('quantity', '>', 0)->count();
        return view('products.index', compact('products', 'categories', 'totalProducts', 'outOfStockCount', 'lowStockCount'));
    }

    public function export(Request $request)
    {
        $products = $this->productQuery($request)->get();
        $filename = 'products_export_'.now()->format('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($products) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Product ID', 'Product Name', 'Category', 'Buying Price', 'Selling Price', 'Current Stock', 'Minimum Stock Level', 'Status', 'Description']);

            foreach ($products as $product) {
                fputcsv($handle, [
                    $product->id,
                    $product->product_name,
                    $product->category?->category_name,
                    number_format($product->buying_price, 2),
                    number_format($product->selling_price, 2),
                    $product->quantity,
                    $product->stock_alert_threshold,
                    $product->status,
                    $product->description,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }

    private function productQuery(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $query->where(function ($query) use ($request) {
                $query->where('product_name', 'LIKE', "%{$request->search}%")
                      ->orWhereHas('category', function ($q) use ($request) {
                          $q->where('category_name', 'LIKE', "%{$request->search}%");
                      });
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('stock_status')) {
            switch ($request->stock_status) {
                case 'out_of_stock':
                    $query->where('quantity', '<=', 0);
                    break;
                case 'low_stock':
                    $query->whereColumn('quantity', '<=', 'stock_alert_threshold')->where('quantity', '>', 0);
                    break;
                case 'in_stock':
                    $query->where('quantity', '>', 0);
                    break;
                case 'inactive':
                    $query->where('status', 'Inactive');
                    break;
            }
        }

        return $query;
    }

    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        // Check if product has transactions (sales, inventory movements, etc.)
        if ($product->orderDetails()->exists() || 
            ($product->inventory_movements ?? collect())->count() > 0) {
            return redirect()->route('products.index')->with('error', 
                'Cannot delete "' . $product->product_name . '" because it has existing transactions. 
                Please set status to Inactive instead for historical reporting.');
        }

        $product_name = $product->product_name;
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product "' . $product_name . '" deleted successfully.');
    }
}

