<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'product_name' => 'required|string|max:255|unique:products,product_name',
            'category_id' => 'required|integer|exists:categories,id',
            'selling_price' => 'required|numeric|min:0|max:999999.99',
            'quantity' => 'required|integer|min:0|max:999999',
            'stock_alert_threshold' => 'required|integer|min:0|max:999999',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'product_name.required' => 'Product name is required.',
            'product_name.unique' => 'Product name already exists.',
            'category_id.required' => 'Category selection is required.',
            'category_id.exists' => 'Selected category is invalid.',
            'selling_price.required' => 'Selling price is required.',
            'selling_price.numeric' => 'Selling price must be a valid number.',
            'quantity.required' => 'Quantity is required.',
            'stock_alert_threshold.required' => 'Stock alert threshold is required.',
        ];
    }
}
