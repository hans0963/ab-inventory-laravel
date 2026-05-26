<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\RawMaterial;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->productQuery($request);
        $products = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::all();
        $totalProducts = Product::where('inventory_type', 'Finished Product')->count();
        $outOfStockCount = Product::where('inventory_type', 'Finished Product')->where('quantity', '<=', 0)->count();
        $lowStockCount = Product::where('inventory_type', 'Finished Product')->whereColumn('quantity', '<=', 'stock_alert_threshold')->where('quantity', '>', 0)->count();
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
        $product->load('category', 'recipe.ingredients.rawMaterial');
        return view('products.show', compact('product'));
    }

    private function productQuery(Request $request)
    {
        $query = Product::with('category')->where('inventory_type', 'Finished Product');

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
        $suppliers = \App\Models\Supplier::where('status', 'Active')->get();
        $rawMaterials = RawMaterial::where('status', 'Active')->orderBy('material_name')->get();
        return view('products.create', compact('categories', 'suppliers', 'rawMaterials'));
    }

    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('products', 'public');
        }

        DB::transaction(function () use ($request, $validated) {
            $product = Product::create($this->productData($validated));
            $this->syncRecipe($product, $request);
        });

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $product->load('recipe.ingredients');
        $categories = Category::all();
        $suppliers = \App\Models\Supplier::where('status', 'Active')->get();
        $rawMaterials = RawMaterial::where('status', 'Active')->orderBy('material_name')->get();
        return view('products.edit', compact('product', 'categories', 'suppliers', 'rawMaterials'));
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

        DB::transaction(function () use ($request, $product, $validated) {
            $product->update($this->productData($validated));
            $this->syncRecipe($product, $request);
        });

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->update(['status' => 'Inactive']);
        return redirect()->route('products.index')->with('success', 'Product "' . $product->product_name . '" archived successfully.');
    }

    private function productData(array $validated): array
    {
        return collect($validated)
            ->except(['recipe_enabled', 'recipe_notes', 'recipe_ingredients'])
            ->merge(['inventory_type' => 'Finished Product'])
            ->all();
    }

    private function syncRecipe(Product $product, Request $request): void
    {
        if ($product->inventory_type !== 'Finished Product' || !$request->boolean('recipe_enabled')) {
            $product->recipe?->delete();
            return;
        }

        $ingredients = collect($request->input('recipe_ingredients', []))
            ->filter(fn ($ingredient) => !empty($ingredient['raw_material_id']) && !empty($ingredient['quantity_per_unit']))
            ->unique('raw_material_id')
            ->values();

        if ($ingredients->isEmpty()) {
            $product->recipe?->delete();
            return;
        }

        $recipe = $product->recipe()->updateOrCreate(
            ['product_id' => $product->id],
            [
                'is_active' => true,
                'notes' => $request->input('recipe_notes'),
            ]
        );

        $recipe->ingredients()->delete();

        foreach ($ingredients as $ingredient) {
            $recipe->ingredients()->create([
                'raw_material_id' => $ingredient['raw_material_id'],
                'quantity_per_unit' => $ingredient['quantity_per_unit'],
            ]);
        }
    }
}
