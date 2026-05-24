<?php

namespace App\Http\Controllers;

use App\Models\DiscountType;
use App\Models\Category;
use App\Models\Product;
use App\Http\Requests\StoreDiscountTypeRequest;
use App\Http\Requests\UpdateDiscountTypeRequest;
use Illuminate\Http\Request;

class DiscountTypeController extends Controller
{
    /**
     * Display a listing of the discount types.
     */
    public function index()
    {
        $discountTypes = DiscountType::latest()->paginate(10);
        return view('discounts.index', compact('discountTypes'));
    }

    /**
     * Show the form for creating a new discount type.
     */
    public function create()
    {
        $categories = Category::orderBy('category_name')->get();
        $products = Product::orderBy('product_name')->get();

        return view('discounts.create', compact('categories', 'products'));
    }

    /**
     * Store a newly created discount type in storage.
     */
    public function store(StoreDiscountTypeRequest $request)
    {
        $validated = $this->normalizeDiscountData($request->validated());
        DiscountType::create($validated);

        return redirect()->route('discounts.index')->with('success', 'Discount type created successfully.');
    }

    /**
     * Show the form for editing the specified discount type.
     */
    public function edit(DiscountType $discount)
    {
        $categories = Category::orderBy('category_name')->get();
        $products = Product::orderBy('product_name')->get();

        return view('discounts.edit', compact('discount', 'categories', 'products'));
    }

    public function show(DiscountType $discount)
    {
        return redirect()->route('discounts.edit', $discount);
    }

    /**
     * Update the specified discount type in storage.
     */
    public function update(UpdateDiscountTypeRequest $request, DiscountType $discount)
    {
        $validated = $this->normalizeDiscountData($request->validated());
        $discount->update($validated);

        return redirect()->route('discounts.index')->with('success', 'Discount type updated successfully.');
    }

    /**
     * Remove the specified discount type from storage.
     */
    public function destroy(DiscountType $discount)
    {
        // Prevent deletion if discount type is used in sales
        if ($discount->sales()->exists()) {
            return redirect()->route('discounts.index')->with('error', 
                'Cannot delete "' . $discount->discount_name . '" because it is used in sales transactions. 
                Please set status to Inactive instead.');
        }

        $name = $discount->discount_name;
        $discount->delete();

        return redirect()->route('discounts.index')->with('success', 'Discount type "' . $name . '" deleted successfully.');
    }

    private function normalizeDiscountData(array $validated): array
    {
        $validated['minimum_purchase_amount'] = $validated['minimum_purchase_amount'] ?? 0;
        $validated['applicable_ids'] = $validated['applicable_to'] === 'All'
            ? null
            : array_values($validated['applicable_ids'] ?? []);
        $validated['discount_percentage'] = $validated['discount_type'] === 'Percentage'
            ? $validated['discount_value']
            : 0;

        return $validated;
    }
}
