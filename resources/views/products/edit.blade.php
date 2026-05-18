<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ __('Edit Product') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Update details for {{ $product->product_name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="card-rustic border-sienna max-w-4xl mx-auto mt-8">
        <form method="POST" action="{{ route('products.update', $product->id) }}" class="space-y-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="col-span-2">
                    <x-input-label for="product_name" value="Product Name (Non-editable)" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="product_name" type="text" value="{{ $product->product_name }}" readonly class="mt-1 block w-full bg-gray-100 !text-sm cursor-not-allowed opacity-60" />
                    <p class="text-[10px] text-sage mt-1 italic font-medium">Product name cannot be changed once created.</p>
                </div>

                <div>
                    <x-input-label for="inventory_type" value="Inventory Type (Non-editable)" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="inventory_type" type="text" value="{{ $product->inventory_type }}" readonly class="mt-1 block w-full bg-gray-100 !text-sm cursor-not-allowed opacity-60" />
                    <p class="text-[10px] text-sage mt-1 italic font-medium">Type cannot be changed after creation</p>
                </div>

                <div>
                    <x-input-label for="category_id" value="Category *" class="text-[10px] uppercase tracking-widest text-sage" />
                    <select name="category_id" id="category_id" required class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm bg-cream bg-opacity-10">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="status" value="Status *" class="text-[10px] uppercase tracking-widest text-sage" />
                    <select name="status" id="status" required class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm bg-cream bg-opacity-10">
                        <option value="Active" {{ old('status', $product->status) == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('status', $product->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="quantity" value="Current Stock *" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="quantity" name="quantity" type="number" value="{{ old('quantity', $product->quantity) }}" min="0" required class="mt-1 block w-full !text-sm" />
                    <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="selling_price" value="Selling Price (₱) *" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="selling_price" name="selling_price" type="number" step="0.01" value="{{ old('selling_price', $product->selling_price) }}" min="0" required class="mt-1 block w-full !text-sm" />
                    <x-input-error :messages="$errors->get('selling_price')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="expiration_date" value="Expiration Date" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="expiration_date" name="expiration_date" type="date" value="{{ old('expiration_date', $product->expiration_date?->format('Y-m-d')) }}" class="mt-1 block w-full !text-sm" />
                    <x-input-error :messages="$errors->get('expiration_date')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="stock_alert_threshold" value="Stock Alert Threshold *" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="stock_alert_threshold" name="stock_alert_threshold" type="number" value="{{ old('stock_alert_threshold', $product->stock_alert_threshold) }}" min="0" required class="mt-1 block w-full !text-sm" />
                    <x-input-error :messages="$errors->get('stock_alert_threshold')" class="mt-2" />
                </div>
            </div>

            <div class="flex justify-between items-center pt-8 border-t border-sienna border-opacity-10">
                <a href="{{ route('products.index') }}" class="text-xs font-black text-sage uppercase tracking-widest hover:text-sienna transition">
                    ← Back to Catalog
                </a>
                <x-primary-button class="!bg-terracotta hover:!bg-terracotta-dark shadow-rustic">
                    Update Product
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
