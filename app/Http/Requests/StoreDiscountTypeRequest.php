<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiscountTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->can('view-discounts');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'discount_name' => 'required|string|max:50|unique:discount_types,discount_name',
            'discount_type' => 'required|in:Percentage,Fixed Amount',
            'discount_value' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'minimum_purchase_amount' => 'nullable|numeric|min:0',
            'applicable_to' => 'required|in:All,Category,Product',
            'applicable_ids' => 'nullable|array',
            'applicable_ids.*' => 'integer',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string|max:255',
            'status' => 'required|in:Active,Inactive,Expired',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'discount_name.required' => 'Discount name is required.',
            'discount_name.unique' => 'Discount name already exists.',
            'discount_value.required' => 'Discount value is required.',
            'discount_value.numeric' => 'Discount value must be a valid number.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be Active, Inactive, or Expired.',
        ];
    }
}
