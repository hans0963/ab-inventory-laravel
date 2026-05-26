<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ __('Create Stock Withdrawal') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Remove stock for damage, expiry, waste, or internal use</p>
            </div>
        </div>
    </x-slot>

    <div class="card-rustic border-sienna">
        <form action="{{ route('stock-withdrawal.store') }}" method="POST">
            @csrf

            <!-- Basic Info -->
            <div class="mb-8">
                <h3 class="text-xl font-bold text-sienna mb-4 border-b border-sienna border-opacity-10 pb-2">Withdrawal Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="date" value="Date" class="text-[10px] uppercase tracking-widest text-sage" />
                        <x-text-input id="date" name="date" type="date" value="{{ old('date', date('Y-m-d')) }}" required class="mt-1 block w-full !text-sm" />
                        <x-input-error :messages="$errors->get('date')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="reason" value="Reason" class="text-[10px] uppercase tracking-widest text-sage" />
                        <select name="reason" required class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm">
                            <option value="">Select Reason</option>
                            <option value="Internal Use" {{ old('reason') === 'Internal Use' ? 'selected' : '' }}>Internal Use</option>
                            <option value="Damaged" {{ old('reason') === 'Damaged' ? 'selected' : '' }}>Damaged</option>
                            <option value="Expired" {{ old('reason') === 'Expired' ? 'selected' : '' }}>Expired</option>
                            <option value="Wastage" {{ old('reason') === 'Wastage' ? 'selected' : '' }}>Wastage</option>
                            <option value="Other" {{ old('reason') === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                    </div>
                </div>
            </div>

            <!-- Items Section -->
            <div class="mb-8">
                <h3 class="text-xl font-bold text-sienna mb-4 border-b border-sienna border-opacity-10 pb-2">Items to Withdraw</h3>
                <div id="items-container" class="space-y-4">
                    <div class="item card-rustic bg-cream bg-opacity-30 border-sage p-4 relative">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <x-input-label value="Raw Material" class="text-[10px] uppercase tracking-widest text-sage" />
                                <select name="items[0][raw_material_id]" required class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm">
                                    <option value="">Select Raw Material</option>
                                    @foreach($rawMaterials as $material)
                                        <option value="{{ $material->id }}">{{ $material->material_name }} (Available: {{ $material->quantity }} {{ $material->unit }})</option>
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
                        </div>
                        <button type="button" class="absolute top-2 right-2 text-red-600 hover:text-red-800 transition remove-item" title="Remove Item">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
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
                <textarea id="notes" name="notes" placeholder="Additional details..." rows="3" class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm">{{ old('notes') }}</textarea>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 pt-4 border-t border-sienna border-opacity-10">
                <x-primary-button class="!bg-sienna hover:!bg-terracotta">
                    Create Withdrawal
                </x-primary-button>
                <a href="{{ route('stock-withdrawal.index') }}" class="inline-flex items-center px-4 py-2 bg-cream border border-sienna border-opacity-20 rounded-md font-bold text-xs text-sienna uppercase tracking-widest hover:bg-opacity-50 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        let itemIndex = 1;
        const rawMaterials = {!! json_encode($rawMaterials) !!};

        document.getElementById('add-item').addEventListener('click', function() {
            const container = document.getElementById('items-container');
            const newItem = document.createElement('div');
            newItem.className = 'item card-rustic bg-cream bg-opacity-30 border-sage p-4 relative';
            
            let options = '<option value="">Select Raw Material</option>';
            rawMaterials.forEach(material => {
                const unit = material.unit ? ` ${material.unit}` : '';
                options += `<option value="${material.id}">${material.material_name} (Available: ${material.quantity}${unit})</option>`;
            });

            newItem.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-sage uppercase tracking-widest mb-1">Raw Material</label>
                        <select name="items[${itemIndex}][raw_material_id]" required class="block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm">
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
                </div>
                <button type="button" class="absolute top-2 right-2 text-red-600 hover:text-red-800 transition remove-item" title="Remove Item">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
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
