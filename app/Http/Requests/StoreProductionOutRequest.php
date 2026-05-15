<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductionOutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('view-inventory');
    }

    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1|max:999999',
            'items.*.unit_price' => 'required|numeric|min:0|max:999999.99',
        ];
    }

    public function messages(): array
    {
        return [
            'date.required' => 'Production out date is required.',
            'reason.required' => 'Reason for production out is required.',
            'items.required' => 'At least one item is required.',
            'items.*.product_id.exists' => 'Selected product is invalid.',
            'items.*.quantity.required' => 'Quantity is required for all items.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
        ];
    }
}
