<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ __('New Procurement') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Record artisan ingredient acquisitions</p>
            </div>
            <a href="{{ route('purchases.index') }}" class="btn-sienna">
                Back to Records
            </a>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto">
        <form id="purchase-form" action="{{ route('purchases.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
                <div class="card-rustic border-sienna md:col-span-2">
                    <h3 class="text-xl font-lora font-bold text-sienna mb-6 flex items-center">
                        <span class="mr-2">📝</span> General Information
                    </h3>
                    
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-black text-sage uppercase tracking-widest mb-2">Artisan Supplier *</label>
                                <select name="supplier_id" required class="input-artisan">
                                    <option value="">Select Supplier</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->suppliers_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-black text-sage uppercase tracking-widest mb-2">Purchase Date *</label>
                                <input type="date" name="purchase_date" value="{{ date('Y-m-d') }}" required class="input-artisan">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-black text-sage uppercase tracking-widest mb-2">Recorded By *</label>
                                <select name="employee_id" required class="input-artisan">
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->id }}" {{ (auth()->user()->employee && auth()->user()->employee->id == $employee->id) ? 'selected' : '' }}>
                                            {{ $employee->employee_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-black text-sage uppercase tracking-widest mb-2">Reference / Invoice #</label>
                                <input type="text" name="reference" placeholder="e.g. INV-12345" class="input-artisan">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-rustic border-terracotta flex flex-col justify-between">
                    <div>
                        <h3 class="text-xl font-lora font-bold text-sienna mb-6">Summary</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center text-sage">
                                <span class="text-xs font-black uppercase tracking-widest">Total Items</span>
                                <span id="item-count" class="font-bold text-sienna">0</span>
                            </div>
                            <div class="pt-4 border-t border-sienna border-opacity-10">
                                <span class="text-xs font-black text-sage uppercase tracking-widest block mb-1">Estimated Total</span>
                                <span id="subtotal" class="text-3xl font-formal font-bold text-terracotta">₱0.00</span>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn-terracotta w-full mt-8 py-4 text-lg shadow-rustic-lg">
                        Complete Order
                    </button>
                </div>
            </div>

            <div class="card-rustic border-sage overflow-hidden">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-lora font-bold text-sienna flex items-center">
                        <span class="mr-2">📦</span> Ingredient List
                    </h3>
                    <button type="button" id="add-item" class="btn-sage py-2 px-4 text-sm">
                        + Add Ingredient
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-sage text-white uppercase text-[10px] tracking-[0.2em] font-black">
                                <th class="px-6 py-4 text-left">Ingredient / Raw Material</th>
                                <th class="px-6 py-4 text-center w-32">Quantity</th>
                                <th class="px-6 py-4 text-right w-48">Unit Price</th>
                                <th class="px-6 py-4 text-right w-48">Line Total</th>
                                <th class="px-6 py-4 text-center w-20"></th>
                            </tr>
                        </thead>
                        <tbody id="purchase-items" class="divide-y divide-sienna divide-opacity-10">
                            <tr class="purchase-item group hover:bg-cream hover:bg-opacity-20 transition-colors">
                                <td class="px-6 py-4">
                                    <select name="product_id[]" required class="product-select input-artisan py-1.5">
                                        <option value="">-- Choose Ingredient --</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->buying_price }}">
                                                {{ $product->product_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <input type="number" name="quantity[]" value="1" min="1" required 
                                           class="quantity-input input-artisan py-1.5 text-center">
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sage text-xs">₱</span>
                                        <input type="number" step="0.01" name="price[]" value="0" required
                                               class="price-input input-artisan py-1.5 pl-7 text-right">
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sienna font-bold line-total">₱0.00</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button type="button" class="remove-item text-terracotta hover:text-red-700 transition-colors p-2 opacity-0 group-hover:opacity-100">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-10">
                <div class="card-rustic border-sienna border-opacity-30">
                    <label class="block text-xs font-black text-sage uppercase tracking-widest mb-3">Order Notes & Special Instructions</label>
                    <textarea name="notes" rows="3" class="input-artisan" placeholder="Add any details about the quality or delivery instructions..."></textarea>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const purchaseItems = document.getElementById('purchase-items');
            const addItemButton = document.getElementById('add-item');
            const subtotalElement = document.getElementById('subtotal');
            const itemCountElement = document.getElementById('item-count');
    
            function updateTotals() {
                let subtotal = 0;
                let itemCount = 0;
                document.querySelectorAll('.purchase-item').forEach(row => {
                    const priceInput = row.querySelector('.price-input');
                    const quantityInput = row.querySelector('.quantity-input');
                    const lineTotalElement = row.querySelector('.line-total');
                    
                    const price = parseFloat(priceInput.value) || 0;
                    const quantity = parseInt(quantityInput.value) || 0;
                    const total = price * quantity;
                    
                    lineTotalElement.textContent = `₱${total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                    subtotal += total;
                    itemCount++;
                });
                subtotalElement.textContent = `₱${subtotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                itemCountElement.textContent = itemCount;
            }
    
            function createNewRow() {
                const newRow = document.createElement('tr');
                newRow.classList.add('purchase-item', 'group', 'hover:bg-cream', 'hover:bg-opacity-20', 'transition-colors');
    
                newRow.innerHTML = `
                    <td class="px-6 py-4">
                        <select name="product_id[]" required class="product-select input-artisan py-1.5">
                            <option value="">-- Choose Ingredient --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->buying_price }}">
                                    {{ $product->product_name }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <input type="number" name="quantity[]" value="1" min="1" required 
                               class="quantity-input input-artisan py-1.5 text-center">
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sage text-xs">₱</span>
                            <input type="number" step="0.01" name="price[]" value="0" required
                                   class="price-input input-artisan py-1.5 pl-7 text-right">
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="text-sienna font-bold line-total">₱0.00</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button type="button" class="remove-item text-terracotta hover:text-red-700 transition-colors p-2">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </td>
                `;
    
                purchaseItems.appendChild(newRow);
                updateTotals();
            }
    
            addItemButton.addEventListener('click', createNewRow);
    
            purchaseItems.addEventListener('click', function(e) {
                const removeBtn = e.target.closest('.remove-item');
                if (removeBtn) {
                    const rows = document.querySelectorAll('.purchase-item');
                    if (rows.length > 1) {
                        removeBtn.closest('.purchase-item').remove();
                        updateTotals();
                    } else {
                        alert('At least one ingredient is required for an order.');
                    }
                }
            });
    
            purchaseItems.addEventListener('input', function(e) {
                if (e.target.classList.contains('product-select')) {
                    const selectedOption = e.target.selectedOptions[0];
                    const price = selectedOption.dataset.price || 0;
                    const row = e.target.closest('.purchase-item');
                    row.querySelector('.price-input').value = price;
                    updateTotals();
                } else if (e.target.classList.contains('quantity-input') || e.target.classList.contains('price-input')) {
                    updateTotals();
                }
            });
    
            updateTotals();
        });
    </script>
</x-app-layout>
