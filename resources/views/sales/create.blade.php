<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-xl shadow-sm border border-sienna border-opacity-20">
            <div>
                <h2 class="font-formal text-4xl text-sienna leading-tight">
                    {{ __('New Sales Transaction') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Record one receipt with multiple products</p>
            </div>
            <a href="{{ route('sales.index') }}" class="text-sienna hover:text-terracotta font-bold transition-colors">
                Back to Sales
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-6">
                <div class="card-rustic border-sienna p-8">
                    <form method="POST" action="{{ route('sales.store') }}" class="space-y-6" id="saleForm">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-black text-sienna uppercase tracking-widest mb-2">Transaction Date *</label>
                                <input type="date" name="date" required value="{{ old('date', date('Y-m-d')) }}"
                                       class="w-full rounded-xl border-sienna border-opacity-20 bg-cream bg-opacity-20 py-3 px-4 focus:ring-2 focus:ring-terracotta focus:border-terracotta transition-all">
                                @error('date') <p class="text-red-500 text-[10px] mt-1 font-bold uppercase">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-black text-sienna uppercase tracking-widest mb-2">Customer</label>
                                <select name="customer_id" class="w-full rounded-xl border-sienna border-opacity-20 bg-cream bg-opacity-20 py-3 px-4 focus:ring-2 focus:ring-terracotta focus:border-terracotta transition-all">
                                    <option value="">{{ $walkInCustomer->name }} (Default)</option>
                                    @foreach($customers as $customer)
                                        @if($customer->id != $walkInCustomer->id)
                                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                                @error('customer_id') <p class="text-red-500 text-[10px] mt-1 font-bold uppercase">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-black text-sienna uppercase tracking-widest mb-2">Payment Mode *</label>
                                <select name="payment_type" required class="w-full rounded-xl border-sienna border-opacity-20 bg-cream bg-opacity-20 py-3 px-4 focus:ring-2 focus:ring-terracotta focus:border-terracotta transition-all">
                                    @foreach(['Cash', 'GCash', 'Card', 'Credit/Loan'] as $paymentType)
                                        <option value="{{ $paymentType }}" {{ old('payment_type', 'Cash') === $paymentType ? 'selected' : '' }}>{{ $paymentType }}</option>
                                    @endforeach
                                </select>
                                @error('payment_type') <p class="text-red-500 text-[10px] mt-1 font-bold uppercase">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-black text-sienna uppercase tracking-widest mb-2">VAT Rate (%) *</label>
                                <input type="number" step="0.01" name="vat_rate" required value="{{ old('vat_rate', 12) }}" min="0" max="100"
                                       class="w-full rounded-xl border-sienna border-opacity-20 bg-cream bg-opacity-20 py-3 px-4 focus:ring-2 focus:ring-terracotta focus:border-terracotta transition-all"
                                       id="vatRate" oninput="calculateTotals()">
                                @error('vat_rate') <p class="text-red-500 text-[10px] mt-1 font-bold uppercase">{{ $message }}</p> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-black text-sienna uppercase tracking-widest mb-2">Apply Discount</label>
                                <select name="discount_type_id" class="w-full rounded-xl border-sienna border-opacity-20 bg-cream bg-opacity-20 py-3 px-4 focus:ring-2 focus:ring-terracotta focus:border-terracotta transition-all"
                                        id="discountTypeSelect" onchange="calculateTotals()">
                                    <option value="">No Discount</option>
                                    @foreach($discountTypes as $discount)
                                        <option value="{{ $discount->id }}" data-type="{{ $discount->discount_type }}" data-value="{{ $discount->discount_value }}" {{ old('discount_type_id') == $discount->id ? 'selected' : '' }}>
                                            {{ $discount->discount_name }} ({{ $discount->display_value }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('discount_type_id') <p class="text-red-500 text-[10px] mt-1 font-bold uppercase">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="pt-6 border-t border-sienna border-opacity-10">
                            <div class="flex items-center justify-between mb-3">
                                <label class="block text-xs font-black text-sienna uppercase tracking-widest">Products *</label>
                                <button type="button" onclick="addItemRow()" class="bg-sage hover:bg-sage-dark text-white font-bold px-4 py-2 rounded-xl text-xs uppercase tracking-widest">
                                    Add Product
                                </button>
                            </div>

                            @error('items') <p class="text-red-500 text-[10px] mb-2 font-bold uppercase">{{ $message }}</p> @enderror

                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead>
                                        <tr class="bg-sienna text-cream uppercase text-[10px] tracking-widest">
                                            <th class="px-4 py-3 text-left">Product</th>
                                            <th class="px-4 py-3 text-left w-32">Qty</th>
                                            <th class="px-4 py-3 text-right w-32">Price</th>
                                            <th class="px-4 py-3 text-right w-36">Line Total</th>
                                            <th class="px-4 py-3 text-center w-24">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemsBody" class="divide-y divide-sienna divide-opacity-10">
                                        @php
                                            $oldItems = old('items', [['product_id' => '', 'sold' => 1]]);
                                        @endphp
                                        @foreach($oldItems as $index => $item)
                                            <tr class="sale-item-row">
                                                <td class="px-4 py-3">
                                                    <select name="items[{{ $index }}][product_id]" required class="product-select w-full rounded-xl border-sienna border-opacity-20 bg-cream bg-opacity-20 py-2 px-3 focus:ring-2 focus:ring-terracotta focus:border-terracotta transition-all" onchange="calculateTotals()">
                                                        <option value="">Select product...</option>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}" data-stock="{{ $product->quantity }}" {{ ($item['product_id'] ?? '') == $product->id ? 'selected' : '' }}>
                                                                {{ $product->product_name }} - PHP {{ number_format($product->selling_price, 2) }} (Stock: {{ $product->quantity }})
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error("items.$index.product_id") <p class="text-red-500 text-[10px] mt-1 font-bold uppercase">{{ $message }}</p> @enderror
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="number" name="items[{{ $index }}][sold]" required min="1" value="{{ $item['sold'] ?? 1 }}"
                                                           class="quantity-input w-full rounded-xl border-sienna border-opacity-20 bg-cream bg-opacity-20 py-2 px-3 focus:ring-2 focus:ring-terracotta focus:border-terracotta transition-all font-black"
                                                           oninput="calculateTotals()">
                                                    @error("items.$index.sold") <p class="text-red-500 text-[10px] mt-1 font-bold uppercase">{{ $message }}</p> @enderror
                                                </td>
                                                <td class="px-4 py-3 text-right font-bold text-sage">PHP <span class="line-price">0.00</span></td>
                                                <td class="px-4 py-3 text-right font-black text-sienna">PHP <span class="line-total">0.00</span></td>
                                                <td class="px-4 py-3 text-center">
                                                    <button type="button" onclick="removeItemRow(this)" class="text-red-600 hover:text-red-800 font-black text-xs uppercase tracking-widest">Remove</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <input type="hidden" name="employee_id" value="{{ $currentEmployee->id ?? '' }}">

                        <div class="pt-6 border-t border-sienna border-opacity-10">
                            <button type="submit" class="w-full bg-terracotta hover:bg-terracotta-dark text-cream font-black py-4 rounded-xl shadow-rustic transition-all hover:-translate-y-1 active:translate-y-0 text-lg uppercase tracking-widest">
                                Complete Transaction
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="card-rustic border-terracotta sticky top-8 overflow-hidden">
                    <div class="bg-terracotta p-4">
                        <h3 class="font-formal text-2xl text-cream">Order Summary</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between text-sage font-medium uppercase text-[10px] tracking-widest">
                            <span>Items</span>
                            <span class="text-sienna font-bold text-sm" id="itemCount">0</span>
                        </div>
                        <div class="flex justify-between text-sage font-medium uppercase text-[10px] tracking-widest">
                            <span>Subtotal</span>
                            <span class="text-sienna font-bold text-sm">PHP <span id="subtotal">0.00</span></span>
                        </div>
                        <div class="flex justify-between text-red-600 font-medium uppercase text-[10px] tracking-widest">
                            <span>Discount <span id="discountTag"></span></span>
                            <span class="font-bold text-sm">-PHP <span id="discountDisplay">0.00</span></span>
                        </div>
                        <div class="border-t border-sienna border-opacity-10 pt-2 flex justify-between text-sage font-medium uppercase text-[10px] tracking-widest">
                            <span>Net Amount</span>
                            <span class="text-sienna font-bold text-sm">PHP <span id="afterDiscount">0.00</span></span>
                        </div>
                        <div class="flex justify-between text-green-600 font-medium uppercase text-[10px] tracking-widest">
                            <span>VAT (<span id="vatRateDisplay">12</span>%)</span>
                            <span class="font-bold text-sm">+PHP <span id="vatDisplay">0.00</span></span>
                        </div>

                        <div class="border-t-2 border-sienna pt-4 mt-4">
                            <div class="flex justify-between items-end gap-4">
                                <span class="font-formal text-2xl text-sienna leading-none">Total</span>
                                <span class="font-black text-2xl text-terracotta leading-none">PHP <span id="totalDisplay">0.00</span></span>
                            </div>
                        </div>

                        <div class="bg-cream bg-opacity-30 rounded-xl p-4 mt-6 border border-sienna border-opacity-10">
                            <div class="text-sienna opacity-60 text-[10px] font-black uppercase tracking-widest">Cashier</div>
                            <div class="text-sienna font-bold mt-1">
                                {{ $currentEmployee->user->name ?? $currentEmployee->employee_name ?? 'Not Assigned' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $productOptions = $products->map(fn ($product) => [
            'id' => $product->id,
            'name' => $product->product_name,
            'price' => (float) $product->selling_price,
            'stock' => $product->quantity,
        ])->values();
    @endphp

    <script>
        const productOptions = @json($productOptions);
        let itemIndex = document.querySelectorAll('.sale-item-row').length;

        function money(value) {
            return Number(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        function addItemRow() {
            const body = document.getElementById('itemsBody');
            const row = document.createElement('tr');
            row.className = 'sale-item-row';

            const options = productOptions.map(product => (
                `<option value="${product.id}" data-price="${product.price}" data-stock="${product.stock}">${product.name} - PHP ${money(product.price)} (Stock: ${product.stock})</option>`
            )).join('');

            row.innerHTML = `
                <td class="px-4 py-3">
                    <select name="items[${itemIndex}][product_id]" required class="product-select w-full rounded-xl border-sienna border-opacity-20 bg-cream bg-opacity-20 py-2 px-3 focus:ring-2 focus:ring-terracotta focus:border-terracotta transition-all" onchange="calculateTotals()">
                        <option value="">Select product...</option>
                        ${options}
                    </select>
                </td>
                <td class="px-4 py-3">
                    <input type="number" name="items[${itemIndex}][sold]" required min="1" value="1" class="quantity-input w-full rounded-xl border-sienna border-opacity-20 bg-cream bg-opacity-20 py-2 px-3 focus:ring-2 focus:ring-terracotta focus:border-terracotta transition-all font-black" oninput="calculateTotals()">
                </td>
                <td class="px-4 py-3 text-right font-bold text-sage">PHP <span class="line-price">0.00</span></td>
                <td class="px-4 py-3 text-right font-black text-sienna">PHP <span class="line-total">0.00</span></td>
                <td class="px-4 py-3 text-center">
                    <button type="button" onclick="removeItemRow(this)" class="text-red-600 hover:text-red-800 font-black text-xs uppercase tracking-widest">Remove</button>
                </td>
            `;

            body.appendChild(row);
            itemIndex++;
            calculateTotals();
        }

        function removeItemRow(button) {
            const rows = document.querySelectorAll('.sale-item-row');
            if (rows.length === 1) {
                return;
            }

            button.closest('tr').remove();
            calculateTotals();
        }

        function calculateTotals() {
            const discountTypeSelect = document.getElementById('discountTypeSelect');
            const vatRateInput = document.getElementById('vatRate');
            let subtotal = 0;
            let itemCount = 0;

            document.querySelectorAll('.sale-item-row').forEach(row => {
                const select = row.querySelector('.product-select');
                const quantityInput = row.querySelector('.quantity-input');
                const selected = select.options[select.selectedIndex];
                const price = parseFloat(selected?.dataset.price) || 0;
                const quantity = parseInt(quantityInput.value) || 0;
                const stock = parseInt(selected?.dataset.stock) || 0;
                const lineTotal = price * quantity;

                quantityInput.max = stock || '';
                row.querySelector('.line-price').textContent = money(price);
                row.querySelector('.line-total').textContent = money(lineTotal);

                subtotal += lineTotal;
                itemCount += quantity;
            });

            const discountOption = discountTypeSelect.options[discountTypeSelect.selectedIndex];
            const discountType = discountOption.dataset.type || 'Percentage';
            const discountValue = parseFloat(discountOption.dataset.value) || 0;
            const vatRate = parseFloat(vatRateInput.value) || 0;
            const discount = discountType === 'Fixed Amount'
                ? Math.min(subtotal, discountValue)
                : subtotal * (discountValue / 100);
            const afterDiscount = subtotal - discount;
            const vat = afterDiscount * (vatRate / 100);
            const total = afterDiscount + vat;

            document.getElementById('itemCount').textContent = itemCount;
            document.getElementById('subtotal').textContent = money(subtotal);
            document.getElementById('discountDisplay').textContent = money(discount);
            document.getElementById('discountTag').textContent = discountValue > 0
                ? (discountType === 'Fixed Amount' ? `(PHP ${money(discountValue)})` : `(${Math.round(discountValue)}%)`)
                : '';
            document.getElementById('afterDiscount').textContent = money(afterDiscount);
            document.getElementById('vatDisplay').textContent = money(vat);
            document.getElementById('vatRateDisplay').textContent = vatRate;
            document.getElementById('totalDisplay').textContent = money(total);
        }

        document.addEventListener('DOMContentLoaded', calculateTotals);
    </script>
</x-app-layout>
