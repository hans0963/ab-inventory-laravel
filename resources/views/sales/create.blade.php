<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-xl shadow-sm border border-sienna border-opacity-20">
            <div>
                <h2 class="font-formal text-4xl text-sienna leading-tight">
                    {{ __('New Sales Transaction') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Record a new sale with discount and VAT</p>
            </div>
            <a href="{{ route('sales.index') }}" class="text-sienna hover:text-terracotta font-bold transition-colors">
                Back to Sales
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Form -->
            <div class="lg:col-span-2 space-y-6">
                <div class="card-rustic border-sienna p-8">
                    <form method="POST" action="{{ route('sales.store') }}" class="space-y-6" id="saleForm">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Product Selection -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-sienna uppercase tracking-widest mb-2">Product Selection *</label>
                                <select name="product_id" required class="w-full rounded-xl border-sienna border-opacity-20 bg-cream bg-opacity-20 py-3 px-4 focus:ring-2 focus:ring-terracotta focus:border-terracotta transition-all"
                                        id="productSelect" onchange="updateProductPrice()">
                                    <option value="">Select a product to sell...</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}">
                                            {{ $product->product_name }} — ₱{{ number_format($product->selling_price, 2) }} (Stock: {{ $product->quantity }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_id') <p class="text-red-500 text-[10px] mt-1 font-bold uppercase">{{ $message }}</p> @enderror
                            </div>

                            <!-- Quantity -->
                            <div>
                                <label class="block text-xs font-black text-sienna uppercase tracking-widest mb-2">Quantity *</label>
                                <input type="number" name="sold" required min="1"
                                       class="w-full rounded-xl border-sienna border-opacity-20 bg-cream bg-opacity-20 py-3 px-4 focus:ring-2 focus:ring-terracotta focus:border-terracotta transition-all font-black text-lg"
                                       placeholder="0" id="quantitySold" oninput="calculateTotals()">
                                @error('sold') <p class="text-red-500 text-[10px] mt-1 font-bold uppercase">{{ $message }}</p> @enderror
                            </div>

                            <!-- Date -->
                            <div>
                                <label class="block text-xs font-black text-sienna uppercase tracking-widest mb-2">Transaction Date *</label>
                                <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                                       class="w-full rounded-xl border-sienna border-opacity-20 bg-cream bg-opacity-20 py-3 px-4 focus:ring-2 focus:ring-terracotta focus:border-terracotta transition-all">
                                @error('date') <p class="text-red-500 text-[10px] mt-1 font-bold uppercase">{{ $message }}</p> @enderror
                            </div>

                            <!-- Customer -->
                            <div>
                                <label class="block text-xs font-black text-sienna uppercase tracking-widest mb-2">Customer</label>
                                <select name="customer_id" class="w-full rounded-xl border-sienna border-opacity-20 bg-cream bg-opacity-20 py-3 px-4 focus:ring-2 focus:ring-terracotta focus:border-terracotta transition-all">
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
                                <label class="block text-xs font-black text-sienna uppercase tracking-widest mb-2">Payment Mode *</label>
                                <select name="payment_type" required class="w-full rounded-xl border-sienna border-opacity-20 bg-cream bg-opacity-20 py-3 px-4 focus:ring-2 focus:ring-terracotta focus:border-terracotta transition-all">
                                    <option value="Cash" selected>Cash</option>
                                    <option value="GCash">GCash</option>
                                    <option value="Card">Card</option>
                                </select>
                                @error('payment_type') <p class="text-red-500 text-[10px] mt-1 font-bold uppercase">{{ $message }}</p> @enderror
                            </div>

                            <!-- Discount Type -->
                            <div>
                                <label class="block text-xs font-black text-sienna uppercase tracking-widest mb-2">Apply Discount</label>
                                <select name="discount_type_id" class="w-full rounded-xl border-sienna border-opacity-20 bg-cream bg-opacity-20 py-3 px-4 focus:ring-2 focus:ring-terracotta focus:border-terracotta transition-all"
                                        id="discountTypeSelect" onchange="calculateTotals()">
                                    <option value="">No Discount</option>
                                    @foreach($discountTypes as $discount)
                                        <option value="{{ $discount->id }}" data-type="{{ $discount->discount_type }}" data-value="{{ $discount->discount_value }}" data-percentage="{{ $discount->discount_percentage }}">
                                            {{ $discount->discount_name }} ({{ $discount->display_value }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- VAT Rate -->
                            <div>
                                <label class="block text-xs font-black text-sienna uppercase tracking-widest mb-2">VAT Rate (%) *</label>
                                <input type="number" step="0.01" name="vat_rate" required value="12" min="0" max="100"
                                       class="w-full rounded-xl border-sienna border-opacity-20 bg-cream bg-opacity-20 py-3 px-4 focus:ring-2 focus:ring-terracotta focus:border-terracotta transition-all"
                                       id="vatRate" oninput="calculateTotals()">
                                @error('vat_rate') <p class="text-red-500 text-[10px] mt-1 font-bold uppercase">{{ $message }}</p> @enderror
                            </div>
                        </div>
                        
                        <!-- Hidden Cashier Field -->
                        <input type="hidden" name="employee_id" value="{{ $currentEmployee->id ?? '' }}">

                        <div class="pt-6 border-t border-sienna border-opacity-10">
                            <button type="submit" class="w-full bg-terracotta hover:bg-terracotta-dark text-cream font-black py-4 rounded-xl shadow-rustic transition-all hover:-translate-y-1 active:translate-y-0 text-lg uppercase tracking-widest">
                                Complete Transaction
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column: Summary Card -->
            <div class="lg:col-span-1">
                <div class="card-rustic border-terracotta sticky top-8 overflow-hidden">
                    <div class="bg-terracotta p-4">
                        <h3 class="font-formal text-2xl text-cream">Order Summary</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between text-sage font-medium uppercase text-[10px] tracking-widest">
                            <span>Subtotal</span>
                            <span class="text-sienna font-bold text-sm">₱<span id="subtotal">0.00</span></span>
                        </div>
                        <div class="flex justify-between text-red-600 font-medium uppercase text-[10px] tracking-widest">
                            <span>Discount <span id="discountTag"></span></span>
                            <span class="font-bold text-sm">-₱<span id="discountDisplay">0.00</span></span>
                        </div>
                        <div class="border-t border-sienna border-opacity-10 pt-2 flex justify-between text-sage font-medium uppercase text-[10px] tracking-widest">
                            <span>Net Amount</span>
                            <span class="text-sienna font-bold text-sm">₱<span id="afterDiscount">0.00</span></span>
                        </div>
                        <div class="flex justify-between text-green-600 font-medium uppercase text-[10px] tracking-widest">
                            <span>VAT (<span id="vatRateDisplay">12</span>%)</span>
                            <span class="font-bold text-sm">+₱<span id="vatDisplay">0.00</span></span>
                        </div>
                        
                        <div class="border-t-2 border-sienna pt-4 mt-4">
                            <div class="flex justify-between items-end">
                                <span class="font-formal text-2xl text-sienna leading-none">Total</span>
                                <span class="font-black text-3xl text-terracotta leading-none">₱<span id="totalDisplay">0.00</span></span>
                            </div>
                        </div>

                        <div class="bg-cream bg-opacity-30 rounded-xl p-4 mt-6 border border-sienna border-opacity-10">
                            <div class="flex items-center text-sienna opacity-60 text-[10px] font-black uppercase tracking-widest">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                Cashier
                            </div>
                            <div class="text-sienna font-bold mt-1">
                                {{ $currentEmployee->user->name ?? $currentEmployee->employee_name ?? 'Not Assigned' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
            const discountOption = discountTypeSelect.options[discountTypeSelect.selectedIndex];
            const discountType = discountOption.dataset.type || 'Percentage';
            const discountValue = parseFloat(discountOption.dataset.value || discountOption.dataset.percentage) || 0;
            const vatRate = parseFloat(vatRateInput.value) || 0;

            // Calculate subtotal
            const subtotal = price * quantity;
            document.getElementById('subtotal').textContent = subtotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});

            // Calculate discount
            const discount = discountType === 'Fixed Amount'
                ? Math.min(subtotal, discountValue)
                : subtotal * (discountValue / 100);
            document.getElementById('discountDisplay').textContent = discount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('discountTag').textContent = discountValue > 0
                ? (discountType === 'Fixed Amount' ? `(PHP ${discountValue.toFixed(2)})` : `(${Math.round(discountValue)}%)`)
                : '';

            // After discount
            const afterDiscount = subtotal - discount;
            document.getElementById('afterDiscount').textContent = afterDiscount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});

            // Calculate VAT
            const vat = afterDiscount * (vatRate / 100);
            document.getElementById('vatDisplay').textContent = vat.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
            document.getElementById('vatRateDisplay').textContent = vatRate;

            // Total
            const total = afterDiscount + vat;
            document.getElementById('totalDisplay').textContent = total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', calculateTotals);
    </script>
</x-app-layout>
