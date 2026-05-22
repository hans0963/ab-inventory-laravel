<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ __('Create Production IN') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Record incoming production batches to inventory</p>
            </div>
        </div>
    </x-slot>

    <div class="card-rustic border-sienna">
        <form action="{{ route('production-in.store') }}" method="POST">
            @csrf

            <!-- Basic Info -->
            <div class="mb-8">
                <h3 class="text-xl font-bold text-sienna mb-4 border-b border-sienna border-opacity-10 pb-2">Batch Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="date" value="Date" class="text-[10px] uppercase tracking-widest text-sage" />
                        <x-text-input id="date" name="date" type="date" value="{{ old('date', date('Y-m-d')) }}" required class="mt-1 block w-full !text-sm" />
                        <x-input-error :messages="$errors->get('date')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Items Section -->
            <div class="mb-8">
                <h3 class="text-xl font-bold text-sienna mb-4 border-b border-sienna border-opacity-10 pb-2">Batch Items</h3>
                <div id="items-container" class="space-y-4">
                    <div class="item card-rustic bg-cream bg-opacity-30 border-sage p-4 relative">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <x-input-label value="Product" class="text-[10px] uppercase tracking-widest text-sage" />
                                <select name="items[0][product_id]" required class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm">
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label value="Quantity" class="text-[10px] uppercase tracking-widest text-sage" />
                                <x-text-input name="items[0][quantity]" type="number" placeholder="0" min="1" required class="mt-1 block w-full !text-sm" />
                            </div>
                            <div>
                                <x-input-label value="Unit Price" class="text-[10px] uppercase tracking-widest text-sage" />
                                <x-text-input name="items[0][unit_price]" type="number" placeholder="0.00" min="0" step="0.01" required class="mt-1 block w-full !text-sm" />
                            </div>
                            <div>
                                <x-input-label value="Expiration" class="text-[10px] uppercase tracking-widest text-sage" />
                                <x-text-input name="items[0][expiration_date]" type="date" class="mt-1 block w-full !text-sm" />
                            </div>
                        </div>
                        <button type="button" class="absolute top-2 right-2 text-red-600 hover:text-red-800 transition remove-item" title="Remove Item">
                            Remove
                        </button>
                    </div>
                </div>

                <button type="button" id="add-item" class="mt-4 inline-flex items-center px-4 py-2 bg-sage border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-sage-dark transition">
                    + Add Item
                </button>
            </div>

            <!-- Notes -->
            <div class="mb-8">
                <x-input-label for="notes" value="Notes" class="text-[10px] uppercase tracking-widest text-sage" />
                <textarea id="notes" name="notes" placeholder="Additional notes..." rows="3" class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm">{{ old('notes') }}</textarea>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-4 border-t border-sienna border-opacity-10">
                <x-primary-button class="!bg-sienna hover:!bg-terracotta">
                    Create Production IN
                </x-primary-button>
                <a href="{{ route('production-in.index') }}" class="inline-flex items-center px-4 py-2 bg-cream border border-sienna border-opacity-20 rounded-md font-bold text-xs text-sienna uppercase tracking-widest hover:bg-opacity-50 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        let itemIndex = 1;
        const products = {!! json_encode($products) !!};

        document.getElementById('add-item').addEventListener('click', function() {
            const container = document.getElementById('items-container');
            const newItem = document.createElement('div');
            newItem.className = 'item card-rustic bg-cream bg-opacity-30 border-sage p-4 relative';
            
            let options = '<option value="">Select Product</option>';
            products.forEach(p => {
                options += `<option value="${p.id}">${p.product_name}</option>`;
            });

            newItem.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-sage uppercase tracking-widest mb-1">Product</label>
                        <select name="items[${itemIndex}][product_id]" required class="block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm">
                            ${options}
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-sage uppercase tracking-widest mb-1">Quantity</label>
                        <input name="items[${itemIndex}][quantity]" type="number" placeholder="0" min="1" required class="block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm" />
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-sage uppercase tracking-widest mb-1">Unit Price</label>
                        <input name="items[${itemIndex}][unit_price]" type="number" placeholder="0.00" min="0" step="0.01" required class="block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm" />
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-sage uppercase tracking-widest mb-1">Expiration</label>
                        <input name="items[${itemIndex}][expiration_date]" type="date" class="block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm" />
                    </div>
                </div>
                <button type="button" class="absolute top-2 right-2 text-red-600 hover:text-red-800 transition remove-item" title="Remove Item">
                    Remove
                </button>
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
    @endpush
</x-app-layout>
