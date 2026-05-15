@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold" style="color: #E2725B;">New Inventory Receiving</h1>
        </div>

        <form action="{{ route('inventory-receiving.store') }}" method="POST" class="bg-white rounded-lg shadow-md p-8">
            @csrf

            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Receiving Date *</label>
                    <input type="date" name="date" value="{{ old('date', now()->toDateString()) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                    @error('date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Supplier *</label>
                    <select name="supplier_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->supplier_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Order (Optional)</label>
                    <select name="purchase_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="">Link to PO (Optional)</option>
                        @foreach($purchases as $purchase)
                            <option value="{{ $purchase->id }}" {{ old('purchase_id') == $purchase->id ? 'selected' : '' }}>
                                {{ $purchase->purchase_number }} - {{ $purchase->supplier->supplier_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">{{ old('notes') }}</textarea>
                </div>
            </div>

            <!-- Items Section -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold" style="color: #E2725B;">Received Items</h2>
                    <button type="button" onclick="addItem()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm">
                        + Add Item
                    </button>
                </div>

                <div id="items-container" class="space-y-4">
                    <!-- Items will be added here -->
                </div>
                @error('items') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Buttons -->
            <div class="flex gap-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold">Save Receiving</button>
                <a href="{{ route('inventory-receiving.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-6 py-3 rounded-lg font-semibold">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
function addItem() {
    const container = document.getElementById('items-container');
    const itemCount = container.children.length;
    
    const itemHTML = `
        <div class="border border-gray-300 rounded-lg p-4 item-row">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Raw Material *</label>
                    <select name="items[${itemCount}][product_id]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">Select Item</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Batch No.</label>
                    <input type="text" name="items[${itemCount}][batch_number]" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="e.g. B-123">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Qty Ordered</label>
                    <input type="number" name="items[${itemCount}][quantity_ordered]" min="0" value="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Qty Received *</label>
                    <input type="number" name="items[${itemCount}][quantity_received]" required min="0" value="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit Cost *</label>
                    <input type="number" step="0.01" name="items[${itemCount}][unit_cost]" required min="0" value="0.00" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Expiration Date</label>
                    <input type="date" name="items[${itemCount}][expiration_date]" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Condition *</label>
                    <select name="items[${itemCount}][condition]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="Good">Good</option>
                        <option value="Damaged">Damaged</option>
                        <option value="Expired">Expired</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>
            
            <div class="mt-4 flex justify-end">
                <button type="button" onclick="this.closest('.item-row').remove()" class="text-red-600 text-sm hover:underline">Remove Item</button>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', itemHTML);
}

function removeItem(button) {
    button.closest('.item-row').remove();
}

// Add first item on load
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('items-container');
    if (container.children.length === 0) {
        addItem();
    }
});
</script>
@endsection
