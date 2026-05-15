@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900">Edit Purchase Order: {{ $purchase->po_number }}</h1>
                <a href="{{ route('purchases.show', $purchase) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                    Back
                </a>
            </div>
        </div>
    </div>

    @if($purchase->status !== 'Pending')
        <div class="max-w-4xl mx-auto mx-4 mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-yellow-800">
                <strong>Note:</strong> This purchase order is {{ strtolower($purchase->status) }}. Only pending purchase orders can be edited.
            </p>
        </div>
    @endif

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <form action="{{ route('purchases.update', $purchase) }}" method="POST" class="bg-white rounded-lg shadow p-6">
            @csrf @method('PUT')

            <div class="space-y-6">
                <!-- Basic Information -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Purchase Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Date</label>
                            <input type="date" name="purchase_date" value="{{ old('purchase_date', $purchase->purchase_date->format('Y-m-d')) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#E2725B] focus:border-transparent">
                            @error('purchase_date')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                            <select name="supplier_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#E2725B] focus:border-transparent">
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ $purchase->supplier_id == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->supplier_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <input type="text" name="notes" value="{{ old('notes', $purchase->notes) }}" maxlength="1000" placeholder="Optional notes..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#E2725B] focus:border-transparent">
                            @error('notes')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Items Section -->
                <div>
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-900">Purchase Items</h2>
                        <button type="button" onclick="addItem()" class="bg-[#E2725B] hover:bg-[#D97760] text-white px-4 py-2 rounded-lg text-sm">
                            + Add Item
                        </button>
                    </div>

                    <div id="items-container" class="space-y-4">
                        <!-- Items will be added here -->
                    </div>
                </div>

                <!-- Summary -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <p class="text-lg font-semibold text-gray-900">Total Amount:</p>
                        <p class="text-2xl font-bold text-[#E2725B]">₱<span id="total-amount">0.00</span></p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-4">
                    <button type="submit" class="bg-[#E2725B] hover:bg-[#D97760] text-white px-6 py-3 rounded-lg font-medium">
                        Update Purchase Order
                    </button>
                    <a href="{{ route('purchases.show', $purchase) }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded-lg font-medium">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function addItem(productId = null, quantity = null, price = null) {
    const container = document.getElementById('items-container');
    const itemCount = container.children.length;
    const itemHTML = `
        <div class="item-row border rounded-lg p-4 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                    <select name="product_id[]" required onchange="updateTotal()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#E2725B] focus:border-transparent">
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}" ${productId == {{ $product->id }} ? 'selected' : ''}>
                                {{ $product->product_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                    <input type="number" name="quantity[]" required min="1" value="${quantity || 1}" oninput="updateTotal()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#E2725B] focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price</label>
                    <input type="number" name="price[]" required step="0.01" min="0" value="${price || 0}" oninput="updateTotal()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#E2725B] focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total</label>
                    <input type="text" readonly class="w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600" value="₱0.00">
                </div>

                <div class="flex items-end">
                    <button type="button" onclick="removeItem(this)" class="w-full bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-sm">
                        Remove
                    </button>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', itemHTML);
    updateTotal();
}

function removeItem(button) {
    button.closest('.item-row').remove();
    updateTotal();
}

function updateTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const quantity = parseInt(row.querySelector('input[name="quantity[]"]').value) || 0;
        const price = parseFloat(row.querySelector('input[name="price[]"]').value) || 0;
        const itemTotal = quantity * price;
        row.querySelector('input[readonly]').value = '₱' + itemTotal.toFixed(2);
        total += itemTotal;
    });
    document.getElementById('total-amount').textContent = total.toFixed(2);
}

// Load existing items on page load
window.addEventListener('DOMContentLoaded', function() {
    const existingItems = @json($purchase->details);
    if (existingItems.length > 0) {
        existingItems.forEach(item => {
            addItem(item.product_id, item.quantity, item.price);
        });
    } else {
        addItem();
    }
});
</script>
@endsection
