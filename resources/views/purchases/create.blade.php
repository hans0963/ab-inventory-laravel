<x-app-layout>
    <div class="max-w-4xl mx-auto mt-10 p-6 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">🛒 Create Purchase</h2>

        <form id="purchase-form" action="{{ route('purchases.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 gap-4 mb-4">
                <div>
                    <label class="block text-gray-600">Supplier *</label>
                    <select name="supplier_id" required 
                            class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-300">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->suppliers_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-gray-600">Recorded By (Employee) *</label>
                    <select name="employee_id" required 
                            class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-300">
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->employee_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-gray-600">Reference *</label>
                    <input type="text" name="reference" required
                           class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-300">
                </div>
            </div>

            <div class="bg-gray-100 p-4 rounded-lg shadow-inner mb-4">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">📦 Purchase Items</h3>

                <table class="w-full border">
                    <thead class="bg-blue-500 text-white">
                        <tr>
                            <th class="px-4 py-2 text-left">Product</th>
                            <th class="px-4 py-2 text-left">Quantity</th>
                            <th class="px-4 py-2 text-left">Price</th>
                            <th class="px-4 py-2 text-left">Total</th>
                            <th class="px-4 py-2 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="purchase-items">
                        <tr class="purchase-item">
                            <td class="px-4 py-2">
                                <select name="product_id[]" required class="product-select w-full px-2 py-1 border rounded">
                                    <option value="">-- Choose Product --</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->buying_price }}">
                                            {{ $product->product_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-4 py-2">
                                <input type="number" name="quantity[]" value="1" min="1" required 
                                       class="quantity-input w-full px-2 py-1 border rounded">
                            </td>
                            <td class="px-4 py-2">
                                <input type="text" name="price[]" value="0" readonly 
                                       class="price-input w-full px-2 py-1 border rounded bg-gray-200">
                            </td>
                            <td class="px-4 py-2">
                                <input type="text" name="total[]" value="0" readonly 
                                       class="total-input w-full px-2 py-1 border rounded bg-gray-200">
                            </td>
                            <td class="px-4 py-2 text-center">
                                <button type="button" class="remove-item px-2 py-1 bg-red-500 text-white rounded">🗑</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <button type="button" id="add-item" class="mt-3 px-4 py-2 bg-green-500 text-white rounded">➕ Add Product</button>
            </div>

            <div class="text-right text-lg font-semibold text-gray-700 mb-4">
                Subtotal: <span id="subtotal">₱0.00</span>
            </div>

            <div class="text-right">
                <button type="submit" class="px-6 py-2 bg-blue-500 text-white rounded-lg">💰 Purchase</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const purchaseItems = document.getElementById('purchase-items');
            const addItemButton = document.getElementById('add-item');
            const subtotalElement = document.getElementById('subtotal');
    
            function updateTotals() {
                let subtotal = 0;
                document.querySelectorAll('.purchase-item').forEach(row => {
                    const price = parseFloat(row.querySelector('.price-input').value) || 0;
                    const quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
                    const total = price * quantity;
                    row.querySelector('.total-input').value = total.toFixed(2);
                    subtotal += total;
                });
                subtotalElement.textContent = `₱${subtotal.toFixed(2)}`;
            }
    
            
            function createNewRow() {
                const newRow = document.createElement('tr');
                newRow.classList.add('purchase-item');
    
                newRow.innerHTML = `
                    <td class="px-4 py-2">
                        <select name="product_id[]" required class="product-select w-full px-2 py-1 border rounded">
                            <option value="">-- Choose Product --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->buying_price }}">
                                    {{ $product->product_name }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                    <td class="px-4 py-2">
                        <input type="number" name="quantity[]" value="1" min="1" required 
                               class="quantity-input w-full px-2 py-1 border rounded">
                    </td>
                    <td class="px-4 py-2">
                        <input type="text" name="price[]" value="0" readonly 
                               class="price-input w-full px-2 py-1 border rounded bg-gray-200">
                    </td>
                    <td class="px-4 py-2">
                        <input type="text" name="total[]" value="0" readonly 
                               class="total-input w-full px-2 py-1 border rounded bg-gray-200">
                    </td>
                    <td class="px-4 py-2 text-center">
                        <button type="button" class="remove-item px-2 py-1 bg-red-500 text-white rounded">🗑</button>
                    </td>
                `;
    
                purchaseItems.appendChild(newRow);
            }
    
           
            addItemButton.addEventListener('click', function() {
                createNewRow();
            });
    
            
            purchaseItems.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-item')) {
                    e.target.closest('.purchase-item').remove();
                    updateTotals();
                }
            });
    
            // Update total when product or quantity changes
            purchaseItems.addEventListener('change', function(e) {
                if (e.target.classList.contains('product-select')) {
                    const selectedOption = e.target.selectedOptions[0];
                    const price = selectedOption.dataset.price || 0;
                    const row = e.target.closest('.purchase-item');
                    
                    row.querySelector('.price-input').value = price;
                    row.querySelector('.total-input').value = (price * row.querySelector('.quantity-input').value).toFixed(2);
                    
                    updateTotals();
                } else if (e.target.classList.contains('quantity-input')) {
                    updateTotals();
                }
            });
    
            updateTotals(); // Initialize total calculation
        });
    </script>
    
           
</x-app-layout>
