@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 to-orange-50 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800">Create Production IN</h1>
            <p class="text-gray-600 mt-2">Record incoming production batches to inventory</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-md p-8">
            <form action="{{ route('production-in.store') }}" method="POST">
                @csrf

                <!-- Basic Info -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Batch Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                            <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="w-full border border-gray-300 rounded-lg px-4 py-2 @error('date') border-red-500 @enderror">
                            @error('date')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Items Section -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Batch Items</h2>
                    <div id="items-container">
                        <div class="item mb-4 p-4 border border-gray-200 rounded-lg bg-gray-50">
                            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Product</label>
                                    <select name="items[0][product_id]" required class="w-full border border-gray-300 rounded-lg px-4 py-2 @error('items.0.product_id') border-red-500 @enderror">
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}">{{ $product->product_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                                    <input type="number" name="items[0][quantity]" placeholder="0" min="1" required class="w-full border border-gray-300 rounded-lg px-4 py-2 qty-input">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Unit Price</label>
                                    <input type="number" name="items[0][unit_price]" placeholder="0.00" min="0" step="0.01" required class="w-full border border-gray-300 rounded-lg px-4 py-2 price-input">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Expiration</label>
                                    <input type="date" name="items[0][expiration_date]" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                                </div>
                                <div class="flex items-end">
                                    <button type="button" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 remove-item">Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="add-item" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 mt-4">
                        + Add Item
                    </button>
                </div>

                <!-- Notes -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" placeholder="Additional notes..." rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2">{{ old('notes') }}</textarea>
                </div>

                <!-- Buttons -->
                <div class="flex gap-4">
                    <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700">
                        Create Production IN
                    </button>
                    <a href="{{ route('production-in.index') }}" class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let itemIndex = 1;

document.getElementById('add-item').addEventListener('click', function() {
    const container = document.getElementById('items-container');
    const newItem = document.createElement('div');
    newItem.className = 'item mb-4 p-4 border border-gray-200 rounded-lg bg-gray-50';
    newItem.innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Product</label>
                <select name="items[${itemIndex}][product_id]" required class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <option value="">Select Product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}">{{ $product->product_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                <input type="number" name="items[${itemIndex}][quantity]" placeholder="0" min="1" required class="w-full border border-gray-300 rounded-lg px-4 py-2 qty-input">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Unit Price</label>
                <input type="number" name="items[${itemIndex}][unit_price]" placeholder="0.00" min="0" step="0.01" required class="w-full border border-gray-300 rounded-lg px-4 py-2 price-input">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Expiration</label>
                <input type="date" name="items[${itemIndex}][expiration_date]" class="w-full border border-gray-300 rounded-lg px-4 py-2">
            </div>
            <div class="flex items-end">
                <button type="button" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 remove-item">Remove</button>
            </div>
        </div>
    `;
    container.appendChild(newItem);
    itemIndex++;
    attachRemoveHandler();
});

function attachRemoveHandler() {
    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.onclick = function() {
            const items = document.querySelectorAll('.item');
            if(items.length > 1) {
                this.closest('.item').remove();
            } else {
                alert('At least one item is required');
            }
        };
    });
}

attachRemoveHandler();
</script>
@endsection
