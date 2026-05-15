<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ __('Create Product') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Add a new item to your bakeshop inventory</p>
            </div>
        </div>
    </x-slot>

    <div class="card-rustic border-sienna max-w-4xl mx-auto mt-8">
        <form method="POST" action="{{ route('products.store') }}" class="space-y-8">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="col-span-2">
                    <x-input-label for="product_name" value="Product Name *" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="product_name" name="product_name" type="text" value="{{ old('product_name') }}" required placeholder="e.g. Pandesal, Ensaymada" class="mt-1 block w-full !text-sm" />
                    <p class="text-[10px] text-sage mt-1 italic font-medium">Note: Product name cannot be changed after creation</p>
                    <x-input-error :messages="$errors->get('product_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="inventory_type" value="Inventory Type *" class="text-[10px] uppercase tracking-widest text-sage" />
                    <select name="inventory_type" id="inventory_type" required class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm">
                        <option value="">Select Type</option>
                        <option value="Finished Product" {{ old('inventory_type') == 'Finished Product' ? 'selected' : '' }}>Finished Product</option>
                        <option value="Raw Material" {{ old('inventory_type') == 'Raw Material' ? 'selected' : '' }}>Raw Material</option>
                    </select>
                    <x-input-error :messages="$errors->get('inventory_type')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="category_id" value="Category *" class="text-[10px] uppercase tracking-widest text-sage" />
                    <select name="category_id" id="category_id" required class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="status" value="Status *" class="text-[10px] uppercase tracking-widest text-sage" />
                    <select name="status" id="status" required class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm">
                        <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="quantity" value="Initial Stock *" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="quantity" name="quantity" type="number" value="{{ old('quantity', 0) }}" min="0" required class="mt-1 block w-full !text-sm" />
                    <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="selling_price" value="Selling Price (₱) *" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="selling_price" name="selling_price" type="number" step="0.01" value="{{ old('selling_price', 0) }}" min="0" required class="mt-1 block w-full !text-sm" />
                    <x-input-error :messages="$errors->get('selling_price')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="expiration_date" value="Expiration Date" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="expiration_date" name="expiration_date" type="date" value="{{ old('expiration_date') }}" class="mt-1 block w-full !text-sm" />
                    <p class="text-[10px] text-sage mt-1 italic font-medium">Optional: Set for perishable items</p>
                    <x-input-error :messages="$errors->get('expiration_date')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="stock_alert_threshold" value="Stock Alert Threshold *" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="stock_alert_threshold" name="stock_alert_threshold" type="number" value="{{ old('stock_alert_threshold', 10) }}" min="0" required class="mt-1 block w-full !text-sm" />
                    <p class="text-[10px] text-sage mt-1 italic font-medium">Notify when stock falls below this level</p>
                    <x-input-error :messages="$errors->get('stock_alert_threshold')" class="mt-2" />
                </div>
            </div>

            <div class="flex justify-between items-center pt-8 border-t border-sienna border-opacity-10">
                <a href="{{ route('products.index') }}" class="text-xs font-black text-sage uppercase tracking-widest hover:text-sienna transition">
                    ← Back to Catalog
                </a>
                <x-primary-button class="!bg-terracotta hover:!bg-terracotta-dark shadow-rustic">
                    Save Product
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
