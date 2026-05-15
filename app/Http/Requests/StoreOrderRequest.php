<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('view-sales-orders');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'customer_id' => 'required|integer|exists:customers,id',
            'payment_type' => 'required|in:Cash,Credit Card,Bank Transfer,Online Payment',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|integer|exists:products,id',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|integer|min:1|max:999999',
            'price' => 'required|array|min:1',
            'price.*' => 'required|numeric|min:0|max:999999.99',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'customer_id.required' => 'Customer selection is required.',
            'customer_id.exists' => 'Selected customer is invalid.',
            'payment_type.required' => 'Payment type is required.',
            'payment_type.in' => 'Invalid payment type selected.',
            'product_id.required' => 'At least one product is required.',
            'product_id.*.exists' => 'One or more selected products are invalid.',
            'quantity.*.required' => 'Quantity is required for each product.',
            'quantity.*.min' => 'Quantity must be at least 1.',
            'price.*.required' => 'Price is required for each product.',
            'price.*.numeric' => 'Price must be a valid number.',
        ];
    }
}
