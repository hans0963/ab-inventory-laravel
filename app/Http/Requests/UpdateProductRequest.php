<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('view-products');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // product_name is NOT editable after creation
            'category_id' => 'required|integer|exists:categories,id',
            'selling_price' => 'required|numeric|min:0|max:999999.99',
            'quantity' => 'required|integer|min:0|max:999999',
            'status' => 'required|in:Active,Inactive',
            'unit' => 'nullable|string|max:50',
            'expiration_date' => 'nullable|date|after_or_equal:today',
            'expiry_alert_days' => 'nullable|integer|min:0|max:365',
            'stock_alert_threshold' => 'required|integer|min:0|max:999999',
            'reorder_level' => 'nullable|integer|min:0|max:999999',
            'reorder_quantity' => 'nullable|integer|min:0|max:999999',
            'default_supplier_id' => 'nullable|integer|exists:suppliers,id',
            'supplier_unit_price' => 'nullable|numeric|min:0|max:999999.99',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'category_id.required' => 'Category selection is required.',
            'category_id.exists' => 'Selected category is invalid.',
            'selling_price.required' => 'Selling price is required.',
            'selling_price.numeric' => 'Selling price must be a valid number.',
            'quantity.required' => 'Quantity is required.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be Active or Inactive.',
            'expiration_date.date' => 'Expiration date must be a valid date.',
            'expiration_date.after_or_equal' => 'Expiration date must be today or later.',
            'stock_alert_threshold.required' => 'Stock alert threshold is required.',
        ];
    }
}
