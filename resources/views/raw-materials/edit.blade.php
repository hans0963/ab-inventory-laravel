<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ __('Edit Raw Material') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Update {{ $rawMaterial->material_name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="card-rustic border-sienna max-w-4xl mx-auto mt-8">
        <form method="POST" action="{{ route('raw-materials.update', $rawMaterial) }}" class="space-y-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="md:col-span-2">
                    <x-input-label for="material_name" value="Material Name *" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="material_name" name="material_name" type="text" value="{{ old('material_name', $rawMaterial->material_name) }}" required class="mt-1 block w-full !text-sm" />
                    <x-input-error :messages="$errors->get('material_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="type" value="Material Type" class="text-[10px] uppercase tracking-widest text-sage" />
                    <select id="type" name="type" class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm">
                        <option value="">Select Type</option>
                        @foreach(['Ingredient', 'Packaging', 'Dairy', 'Dry Goods', 'Other'] as $type)
                            <option value="{{ $type }}" {{ old('type', $rawMaterial->type) === $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('type')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="status" value="Status *" class="text-[10px] uppercase tracking-widest text-sage" />
                    <select id="status" name="status" required class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm">
                        <option value="Active" {{ old('status', $rawMaterial->status) === 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('status', $rawMaterial->status) === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="quantity" value="Current Stock *" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="quantity" name="quantity" type="number" value="{{ old('quantity', $rawMaterial->quantity) }}" min="0" required class="mt-1 block w-full !text-sm" />
                    <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="unit" value="Unit" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="unit" name="unit" type="text" value="{{ old('unit', $rawMaterial->unit ?: 'kg') }}" placeholder="kg, g, L, pcs, box" class="mt-1 block w-full !text-sm" />
                    <x-input-error :messages="$errors->get('unit')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="expiration_date" value="Expiration Date" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="expiration_date" name="expiration_date" type="date" value="{{ old('expiration_date', $rawMaterial->expiration_date?->format('Y-m-d')) }}" class="mt-1 block w-full !text-sm" />
                    <x-input-error :messages="$errors->get('expiration_date')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="expiry_alert_days" value="Expiry Alert Days" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="expiry_alert_days" name="expiry_alert_days" type="number" value="{{ old('expiry_alert_days', $rawMaterial->expiry_alert_days ?? 7) }}" min="0" max="365" class="mt-1 block w-full !text-sm" />
                    <x-input-error :messages="$errors->get('expiry_alert_days')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="stock_alert_threshold" value="Stock Alert Threshold *" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="stock_alert_threshold" name="stock_alert_threshold" type="number" value="{{ old('stock_alert_threshold', $rawMaterial->stock_alert_threshold) }}" min="0" required class="mt-1 block w-full !text-sm" />
                    <x-input-error :messages="$errors->get('stock_alert_threshold')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="reorder_level" value="Reorder Level" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="reorder_level" name="reorder_level" type="number" value="{{ old('reorder_level', $rawMaterial->reorder_level) }}" min="0" class="mt-1 block w-full !text-sm" />
                    <x-input-error :messages="$errors->get('reorder_level')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="reorder_quantity" value="Reorder Quantity" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="reorder_quantity" name="reorder_quantity" type="number" value="{{ old('reorder_quantity', $rawMaterial->reorder_quantity) }}" min="0" class="mt-1 block w-full !text-sm" />
                    <x-input-error :messages="$errors->get('reorder_quantity')" class="mt-2" />
                </div>
            </div>

            <div class="flex justify-between items-center pt-8 border-t border-sienna border-opacity-10">
                <a href="{{ route('raw-materials.index') }}" class="text-xs font-black text-sage uppercase tracking-widest hover:text-sienna transition">
                    Back to Raw Materials
                </a>
                <x-primary-button class="!bg-terracotta hover:!bg-terracotta-dark shadow-rustic">
                    Update Raw Material
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
