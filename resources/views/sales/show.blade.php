<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-md shadow-sm border-b border-sienna">
            <h2 class="font-pacifico text-3xl text-sienna leading-tight">
                {{ __('Sale Details') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('sales.print', $sale->id) }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    🖨️ Print Receipt
                </a>
                <a href="{{ route('sales.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md p-8 border-l-4 border-sienna">
            <div class="grid grid-cols-2 gap-6 mb-8">
                <div>
                    <p class="text-sm text-gray-600">Receipt Number</p>
                    <p class="font-mono text-lg font-bold text-sienna">{{ $sale->receipt_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Date</p>
                    <p class="text-lg font-semibold">{{ $sale->date->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Cashier/Employee</p>
                    <p class="text-lg font-semibold">{{ $sale->employee->user->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Payment Type</p>
                    <p class="text-lg font-semibold">{{ $sale->payment_type }}</p>
                </div>
            </div>

            <hr class="my-6 border-sienna">

            <div class="mb-8">
                <h3 class="text-lg font-bold text-sienna mb-4">Product Details</h3>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-600">Product Name</p>
                        <p class="text-lg font-semibold">{{ $sale->product->product_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Category</p>
                        <p class="text-lg font-semibold">{{ $sale->product->category->category_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Quantity Sold</p>
                        <p class="text-lg font-semibold">{{ $sale->sold }} units</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Unit Price</p>
                        <p class="text-lg font-semibold">₱{{ number_format($sale->product->selling_price, 2) }}</p>
                    </div>
                </div>
            </div>

            <hr class="my-6 border-sienna">

            <div class="mb-8">
                <h3 class="text-lg font-bold text-sienna mb-4">Customer Information</h3>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-600">Customer Name</p>
                        <p class="text-lg font-semibold">{{ $sale->customer->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Customer Phone</p>
                        <p class="text-lg font-semibold">{{ $sale->customer->phone ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <hr class="my-6 border-sienna">

            <div class="bg-cream bg-opacity-20 p-6 rounded-lg border-l-4 border-terracotta">
                <h3 class="text-lg font-bold text-sienna mb-4">Transaction Summary</h3>
                <div class="space-y-3">
                    <div class="flex justify-between text-base">
                        <span>Subtotal:</span>
                        <span>₱{{ number_format($sale->product->selling_price * $sale->sold, 2) }}</span>
                    </div>
                    @if($sale->discount_amount > 0)
                        <div class="flex justify-between text-base text-red-600">
                            <span>Discount @if($sale->discountType) ({{ $sale->discountType->discount_name }}) @endif:</span>
                            <span>-₱{{ number_format($sale->discount_amount, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-base border-t border-sienna pt-3">
                        <span>After Discount:</span>
                        <span>₱{{ number_format($sale->product->selling_price * $sale->sold - $sale->discount_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-base text-green-600">
                        <span>VAT ({{ $sale->vat_rate }}%):</span>
                        <span>+₱{{ number_format($sale->vat_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-xl font-bold text-sienna border-t-2 border-sienna pt-3">
                        <span>TOTAL AMOUNT:</span>
                        <span>₱{{ number_format($sale->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
