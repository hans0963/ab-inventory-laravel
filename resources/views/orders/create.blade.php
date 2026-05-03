<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-md shadow-sm border-b border-sienna">
            <h2 class="font-pacifico text-3xl text-sienna leading-tight">
                {{ __('Create New Order') }}
            </h2>
            <p class="font-inter text-sage">Process a new bakeshop sale</p>
        </div>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md p-8 border-l-4 border-sienna">
            <form id="order-form" action="{{ route('orders.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-sienna mb-2">Customer *</label>
                        <select name="customer_id" required 
                                class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10">
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-sienna mb-2">Payment Type *</label>
                        <select name="payment_type" required 
                                class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10">
                            <option value="Cash">Cash</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Online Payment">Online Payment</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-sienna mb-2">Recorded By (Employee) *</label>
                    <select name="employee_id" required 
                            class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10">
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->employee_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="bg-cream bg-opacity-20 p-6 rounded-lg border border-tan border-opacity-30 space-y-4">
                    <h3 class="text-lg font-lora font-semibold text-sienna flex items-center">
                        <span class="mr-2">🍞</span> Order Items
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-sienna text-cream uppercase text-[10px] tracking-widest">
                                    <th class="px-4 py-2 text-left">Product</th>
                                    <th class="px-4 py-2 text-center w-24">Qty</th>
                                    <th class="px-4 py-2 text-right w-32">Price</th>
                                    <th class="px-4 py-2 text-right w-32">Total</th>
                                    <th class="px-4 py-2 text-center w-16"></th>
                                </tr>
                            </thead>
                            <tbody id="order-items" class="divide-y divide-tan divide-opacity-20">
                                <tr class="order-item">
                                    <td class="px-2 py-3">
                                        <select name="product_id[]" required 
                                                class="product-select w-full border-tan focus:ring-terracotta focus:border-terracotta rounded-md text-xs bg-white">
                                            <option value="">-- Choose Product --</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" 
                                                        data-price="{{ $product->selling_price }}" 
                                                        data-stock="{{ $product->quantity }}">
                                                    {{ $product->product_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-2 py-3 relative">
                                        <input type="number" name="quantity[]" value="1" min="1" required 
                                               class="quantity-input w-full border-tan focus:ring-terracotta focus:border-terracotta rounded-md text-xs text-center">
                                        <p class="text-[10px] text-red-500 hidden error-message absolute -bottom-1 left-0 right-0 text-center">Low stock!</p>
                                    </td>
                                    <td class="px-2 py-3">
                                        <input type="text" name="price[]" value="0" readonly 
                                               class="price-input w-full border-none bg-transparent text-right font-semibold text-sienna">
                                    </td>
                                    <td class="px-2 py-3">
                                        <input type="text" name="total[]" value="0" readonly 
                                               class="total-input w-full border-none bg-transparent text-right font-bold text-terracotta">
                                    </td>
                                    <td class="px-2 py-3 text-center">
                                        <button type="button" class="remove-item text-red-400 hover:text-red-600 transition">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <button type="button" id="add-item" 
                            class="text-sage font-semibold text-sm hover:text-sienna transition flex items-center">
                        <span class="mr-1 text-lg">+</span> Add another product
                    </button>
                </div>

                <div class="flex justify-between items-center pt-4 border-t border-tan border-opacity-30">
                    <div class="text-left">
                        <button type="button" onclick="window.location='{{ route('orders.index') }}'" 
                                class="text-gray-500 hover:text-sienna font-semibold transition">
                            Cancel
                        </button>
                    </div>
                    <div class="text-right space-y-2">
                        <p class="text-sm text-gray-600">Grand Total:</p>
                        <p class="text-3xl font-lora font-bold text-terracotta" id="subtotal">₱0.00</p>
                        <button type="submit" 
                                class="bg-sienna hover:bg-opacity-90 text-cream font-inter px-10 py-3 rounded-lg shadow-md transition font-bold uppercase tracking-widest">
                            Finalize Order
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const orderItems = document.getElementById('order-items');
            const addItemBtn = document.getElementById('add-item');
            const subtotalDisplay = document.getElementById('subtotal');

            function calculateTotals() {
                let grandTotal = 0;
                document.querySelectorAll('.order-item').forEach(row => {
                    const price = parseFloat(row.querySelector('.price-input').value) || 0;
                    const qty = parseInt(row.querySelector('.quantity-input').value) || 0;
                    const total = price * qty;
                    row.querySelector('.total-input').value = total.toFixed(2);
                    grandTotal += total;
                });
                subtotalDisplay.textContent = '₱' + grandTotal.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }

            addItemBtn.addEventListener('click', () => {
                const firstRow = document.querySelector('.order-item');
                const newRow = firstRow.cloneNode(true);
                newRow.querySelectorAll('input').forEach(input => input.value = input.name.includes('quantity') ? 1 : 0);
                newRow.querySelector('select').value = '';
                newRow.querySelector('.error-message').classList.add('hidden');
                orderItems.appendChild(newRow);
            });

            orderItems.addEventListener('change', (e) => {
                if (e.target.classList.contains('product-select')) {
                    const row = e.target.closest('.order-item');
                    const selectedOption = e.target.options[e.target.selectedIndex];
                    const price = selectedOption.dataset.price || 0;
                    row.querySelector('.price-input').value = price;
                    calculateTotals();
                }
            });

            orderItems.addEventListener('input', (e) => {
                if (e.target.classList.contains('quantity-input')) {
                    const row = e.target.closest('.order-item');
                    const selectedOption = row.querySelector('.product-select').options[row.querySelector('.product-select').selectedIndex];
                    const stock = parseInt(selectedOption?.dataset?.stock || 0);
                    const qty = parseInt(e.target.value) || 0;
                    
                    if (qty > stock) {
                        row.querySelector('.error-message').classList.remove('hidden');
                    } else {
                        row.querySelector('.error-message').classList.add('hidden');
                    }
                    calculateTotals();
                }
            });

            orderItems.addEventListener('click', (e) => {
                if (e.target.closest('.remove-item')) {
                    const rows = document.querySelectorAll('.order-item');
                    if (rows.length > 1) {
                        e.target.closest('.order-item').remove();
                        calculateTotals();
                    } else {
                        alert('At least one item is required.');
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
