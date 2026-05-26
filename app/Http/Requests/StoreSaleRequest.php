<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
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
            'employee_id' => 'required|integer|exists:employees,id',
            'customer_id' => 'nullable|integer|exists:customers,id',
            'date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.sold' => 'required|integer|min:1|max:999999',
            'discount_type_id' => 'nullable|integer|exists:discount_types,id',
            'payment_type' => 'required|in:Cash,GCash,Card,Credit/Loan',
            'vat_rate' => 'required|numeric|min:0|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'items.required' => 'At least one product is required.',
            'items.*.product_id.required' => 'Product selection is required.',
            'items.*.product_id.exists' => 'Selected product is invalid.',
            'items.*.sold.required' => 'Quantity sold is required.',
            'items.*.sold.integer' => 'Quantity sold must be a number.',
            'items.*.sold.min' => 'Quantity sold must be at least 1.',
            'employee_id.required' => 'Employee selection is required.',
            'employee_id.exists' => 'Selected employee is invalid.',
            'customer_id.exists' => 'Selected customer is invalid.',
            'date.required' => 'Sale date is required.',
            'date.date' => 'Sale date must be a valid date.',
            'discount_type_id.exists' => 'Selected discount type is invalid.',
            'payment_type.required' => 'Payment type is required.',
            'payment_type.in' => 'Payment type must be Cash, GCash, Card, or Credit/Loan.',
            'vat_rate.required' => 'VAT rate is required.',
            'vat_rate.numeric' => 'VAT rate must be a valid number.',
            'vat_rate.max' => 'VAT rate cannot exceed 100%.',
        ];
    }
}
