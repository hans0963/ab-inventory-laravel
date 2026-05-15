<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryReceivingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->role === 'manager' || $this->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_id' => 'nullable|exists:purchases,id',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.batch_number' => 'nullable|string|max:50',
            'items.*.quantity_ordered' => 'required|integer|min:0',
            'items.*.quantity_received' => 'required|integer|min:0',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.expiration_date' => 'nullable|date',
            'items.*.condition' => 'required|in:Good,Damaged,Expired,Other',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'At least one item must be added',
            'items.min' => 'At least one item must be added',
            'items.*.product_id.required' => 'Product is required for each item',
            'items.*.quantity_received.required' => 'Received quantity is required',
            'items.*.unit_cost.required' => 'Unit cost is required',
            'items.*.condition.required' => 'Item condition is required'
        ];
    }
}
