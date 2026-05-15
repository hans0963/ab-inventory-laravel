<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::paginate(5);
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function show(Category $category)
    {
        $category->loadCount('products');
        $products = $category->products()->latest()->paginate(10);
        return view('categories.show', compact('category', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:50|unique:categories',
            'description' => 'nullable|string|max:255',
        ]);

        Category::create($request->all());

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'category_name' => 'required|string|max:50|unique:categories,category_name,' . $category->id . ',id',
            'description' => 'nullable|string|max:255',
        ]);

        $category->update($request->all());

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        // Check if category has products
        if ($category->products()->exists()) {
            return redirect()->route('categories.index')->with('error', 'Cannot delete "' . $category->category_name . '" because it has existing products. Please move or delete the products first.');
        }

        $category_name = $category->category_name;
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category "' . $category_name . '" archived successfully.');
    }
}
