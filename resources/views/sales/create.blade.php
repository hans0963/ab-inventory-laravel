<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-md shadow-sm border-b border-sienna">
            <h2 class="font-pacifico text-3xl text-sienna leading-tight">
                {{ __('New Sales Transaction') }}
            </h2>
            <p class="font-inter text-sage">Record a new sale with discount and VAT</p>
        </div>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md p-8 border-l-4 border-sienna">
            <form method="POST" action="{{ route('sales.store') }}" class="space-y-6" id="saleForm">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Product Selection -->
                    <div>
                        <label class="block text-sm font-semibold text-sienna mb-2">Product *</label>
                        <select name="product_id" required class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10"
                                id="productSelect" onchange="updateProductPrice()">
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}">
                                    {{ $product->product_name }} (Stock: {{ $product->quantity }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label class="block text-sm font-semibold text-sienna mb-2">Quantity Sold *</label>
                        <input type="number" name="sold" required min="1"
                               class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10"
                               placeholder="0" id="quantitySold" onchange="calculateTotals()">
                        @error('sold') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Date -->
                    <div>
                        <label class="block text-sm font-semibold text-sienna mb-2">Date *</label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                               class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10">
                        @error('date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Employee (Auto-filled) -->
                    <div>
                        <label class="block text-sm font-semibold text-sienna mb-2">Cashier/Employee *</label>
                        <select name="employee_id" required class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10">
                            @if($currentEmployee)
                                <option value="{{ $currentEmployee->id }}" selected>{{ $currentEmployee->user->name ?? $currentEmployee->employee_name }} (Current)</option>
                            @endif
                            @foreach($employees as $employee)
                                @if(!$currentEmployee || $employee->id != $currentEmployee->id)
                                    <option value="{{ $employee->id }}">{{ $employee->user->name ?? $employee->employee_name }}</option>
                                @endif
                            @endforeach
                        </select>
                        @error('employee_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Customer -->
                    <div>
                        <label class="block text-sm font-semibold text-sienna mb-2">Customer</label>
                        <select name="customer_id" class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10">
                            <option value="">{{ $walkInCustomer->name }} (Default)</option>
                            @foreach($customers as $customer)
                                @if($customer->id != $walkInCustomer->id)
                                    <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <!-- Payment Type -->
                    <div>
                        <label class="block text-sm font-semibold text-sienna mb-2">Payment Type *</label>
                        <select name="payment_type" required class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10">
                            <option value="Cash" selected>Cash</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Check">Check</option>
                            <option value="E-Wallet">E-Wallet</option>
                        </select>
                        @error('payment_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Discount Type -->
                    <div>
                        <label class="block text-sm font-semibold text-sienna mb-2">Discount Type</label>
                        <select name="discount_type_id" class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10"
                                id="discountTypeSelect" onchange="calculateTotals()">
                            <option value="">None</option>
                            @foreach($discountTypes as $discount)
                                <option value="{{ $discount->id }}" data-percentage="{{ $discount->discount_percentage }}">
                                    {{ $discount->discount_name }} ({{ $discount->discount_percentage }}%)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- VAT Rate -->
                    <div>
                        <label class="block text-sm font-semibold text-sienna mb-2">VAT Rate (%) *</label>
                        <input type="number" step="0.01" name="vat_rate" required value="12" min="0" max="100"
                               class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10"
                               id="vatRate" onchange="calculateTotals()">
                        @error('vat_rate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Calculation Summary -->
                <div class="bg-cream bg-opacity-20 p-6 rounded-lg border-l-4 border-terracotta mt-8">
                    <h3 class="font-semibold text-sienna mb-4">Sale Summary</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Subtotal:</span>
                            <span class="font-semibold">₱<span id="subtotal">0.00</span></span>
                        </div>
                        <div class="flex justify-between text-red-600">
                            <span>Discount (-):</span>
                            <span class="font-semibold">₱<span id="discountDisplay">0.00</span></span>
                        </div>
                        <div class="flex justify-between border-t border-sienna pt-2">
                            <span>After Discount:</span>
                            <span class="font-semibold">₱<span id="afterDiscount">0.00</span></span>
                        </div>
                        <div class="flex justify-between text-green-600">
                            <span>VAT (₱<span id="vatRateDisplay">12</span>%):</span>
                            <span class="font-semibold">₱<span id="vatDisplay">0.00</span></span>
                        </div>
                        <div class="flex justify-between text-lg font-bold text-sienna border-t-2 border-sienna pt-2 mt-2">
                            <span>TOTAL:</span>
                            <span>₱<span id="totalDisplay">0.00</span></span>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex gap-4 pt-6">
                    <button type="submit" class="px-6 py-2 bg-sienna text-white rounded-md hover:bg-opacity-80 font-semibold">
                        Record Sale
                    </button>
                    <a href="{{ route('sales.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateProductPrice() {
            calculateTotals();
        }

        function calculateTotals() {
            const productSelect = document.getElementById('productSelect');
            const quantityInput = document.getElementById('quantitySold');
            const discountTypeSelect = document.getElementById('discountTypeSelect');
            const vatRateInput = document.getElementById('vatRate');

            const price = parseFloat(productSelect.options[productSelect.selectedIndex].dataset.price) || 0;
            const quantity = parseInt(quantityInput.value) || 0;
            const discountPercentage = parseFloat(discountTypeSelect.options[discountTypeSelect.selectedIndex].dataset.percentage) || 0;
            const vatRate = parseFloat(vatRateInput.value) || 0;

            // Calculate subtotal
            const subtotal = price * quantity;
            document.getElementById('subtotal').textContent = subtotal.toFixed(2);

            // Calculate discount
            const discount = subtotal * (discountPercentage / 100);
            document.getElementById('discountDisplay').textContent = discount.toFixed(2);

            // After discount
            const afterDiscount = subtotal - discount;
            document.getElementById('afterDiscount').textContent = afterDiscount.toFixed(2);

            // Calculate VAT
            const vat = afterDiscount * (vatRate / 100);
            document.getElementById('vatDisplay').textContent = vat.toFixed(2);
            document.getElementById('vatRateDisplay').textContent = vatRate.toFixed(2);

            // Total
            const total = afterDiscount + vat;
            document.getElementById('totalDisplay').textContent = total.toFixed(2);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', calculateTotals);
    </script>
</x-app-layout>
