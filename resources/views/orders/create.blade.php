<x-app-layout>
    <div class="max-w-4xl mx-auto mt-10 p-6 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">🛍️ Create Order</h2>

        <form id="order-form" action="{{ route('orders.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 gap-4 mb-4">

                <div>
                    <label class="block text-gray-600">Customer *</label>
                    <select name="customer_id" required 
                            class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-300">
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
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
                    <label class="block text-gray-600">Payment Type *</label>
                    <select name="payment_type" required 
                            class="w-full px-3 py-2 border rounded-lg focus:ring focus:ring-blue-300">
                        <option value="Cash">Cash</option>
                        <option value="Card">Card</option>
                        <option value="Online">Online</option>
                    </select>
                </div>
            </div>

            <div class="bg-gray-100 p-4 rounded-lg shadow-inner mb-4">
                <h3 class="text-lg font-semibold text-gray-700 mb-2">🛒 Order Items</h3>

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
                    <tbody id="order-items">
                        <tr class="order-item">
                            <td class="px-4 py-2">
                                <select name="product_id[]" required 
                                        class="product-select w-full px-2 py-1 border rounded">
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
                            <td class="px-4 py-2 relative">
                                <input type="number" name="quantity[]" value="1" min="1" required 
                                       class="quantity-input w-full px-2 py-1 border rounded">
                                <p class="text-sm text-red-500 hidden error-message">❌ Not enough stock!</p>
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
                <button type="submit" id="submit-order" class="px-6 py-2 bg-blue-500 text-white rounded-lg">🛍 Place Order</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const orderItems = document.getElementById('order-items');
            const addItemButton = document.getElementById('add-item');
            const subtotalElement = document.getElementById('subtotal');
            const submitButton = document.getElementById('submit-order');
    
            function updateTotals() {
                let subtotal = 0;
                let isValid = true;
    
                document.querySelectorAll('.order-item').forEach(row => {
                    const productSelect = row.querySelector('.product-select');
                    const selectedOption = productSelect.selectedOptions[0];
                    const priceInput = row.querySelector('.price-input');
                    const quantityInput = row.querySelector('.quantity-input');
                    const totalInput = row.querySelector('.total-input');
                    const errorMessage = row.querySelector('.error-message');
    
                    if (!selectedOption || selectedOption.value === "") {
                        priceInput.value = "0";
                        totalInput.value = "0";
                        errorMessage.classList.add('hidden');
                        return;
                    }
    
                    const price = parseFloat(selectedOption.dataset.price) || 0;
                    const stock = parseInt(selectedOption.dataset.stock) || 0;
                    const quantity = parseInt(quantityInput.value) || 0;
                    const total = price * quantity;
    
                    priceInput.value = price;
                    totalInput.value = total.toFixed(2);
                    subtotal += total;
    
                    if (quantity > stock) {
                        errorMessage.classList.remove('hidden');
                        isValid = false;
                    } else {
                        errorMessage.classList.add('hidden');
                    }
                });
    
                subtotalElement.textContent = `₱${subtotal.toFixed(2)}`;
                submitButton.disabled = !isValid;
            }
    
            addItemButton.addEventListener('click', function() {
                const newRow = document.createElement('tr');
                newRow.classList.add('order-item');
    
                newRow.innerHTML = `
                    <td class="px-4 py-2">
                        <select name="product_id[]" required 
                                class="product-select w-full px-2 py-1 border rounded">
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
                    <td class="px-4 py-2 relative">
                        <input type="number" name="quantity[]" value="1" min="1" required 
                               class="quantity-input w-full px-2 py-1 border rounded">
                        <p class="text-sm text-red-500 hidden error-message">❌ Not enough stock!</p>
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
    
                orderItems.appendChild(newRow);
                updateTotals();
            });
            orderItems.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-item')) {
                    e.target.closest('.order-item').remove();
                    updateTotals();
                }
            });
    
            orderItems.addEventListener('change', function(e) {
                if (e.target.classList.contains('product-select') || e.target.classList.contains('quantity-input')) {
                    updateTotals();
                }
            });
    
            updateTotals();
        });
    </script>
</x-app-layout>
