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
            'product_id' => 'required|integer|exists:products,id',
            'employee_id' => 'required|integer|exists:employees,id',
            'customer_id' => 'nullable|integer|exists:customers,id',
            'date' => 'required|date',
            'sold' => 'required|integer|min:1|max:999999',
            'discount_type_id' => 'nullable|integer|exists:discount_types,id',
            'discount_amount' => 'nullable|numeric|min:0|max:999999.99',
            'payment_type' => 'required|in:Cash,Credit Card,Bank Transfer,Check,E-Wallet',
            'vat_rate' => 'required|numeric|min:0|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Product selection is required.',
            'product_id.exists' => 'Selected product is invalid.',
            'employee_id.required' => 'Employee selection is required.',
            'employee_id.exists' => 'Selected employee is invalid.',
            'customer_id.exists' => 'Selected customer is invalid.',
            'date.required' => 'Sale date is required.',
            'date.date' => 'Sale date must be a valid date.',
            'sold.required' => 'Quantity sold is required.',
            'sold.integer' => 'Quantity sold must be a number.',
            'sold.min' => 'Quantity sold must be at least 1.',
            'discount_type_id.exists' => 'Selected discount type is invalid.',
            'discount_amount.numeric' => 'Discount amount must be a valid number.',
            'payment_type.required' => 'Payment type is required.',
            'payment_type.in' => 'Payment type must be Cash, Credit Card, Bank Transfer, Check, or E-Wallet.',
            'vat_rate.required' => 'VAT rate is required.',
            'vat_rate.numeric' => 'VAT rate must be a valid number.',
            'vat_rate.max' => 'VAT rate cannot exceed 100%.',
        ];
    }
}
