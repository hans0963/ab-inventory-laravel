<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-md shadow-sm border-b border-sienna">
            <h2 class="font-pacifico text-3xl text-sienna leading-tight">
                {{ __('Create New Product') }}
            </h2>
            <p class="font-inter text-sage">Add a new item to your bakeshop inventory</p>
        </div>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md p-8 border-l-4 border-sienna">
            <form method="POST" action="{{ route('products.store') }}" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-sienna mb-2">Product Name *</label>
                        <input type="text" name="product_name" required 
                               class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10"
                               placeholder="e.g. Pandesal, Ensaymada">
                        <p class="text-[10px] text-sage mt-1 italic">Note: Product name cannot be changed after creation</p>
                        @error('product_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-sienna mb-2">Inventory Type *</label>
                        <select name="inventory_type" required 
                                class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10">
                            <option value="">Select Type</option>
                            <option value="Finished Product">Finished Product</option>
                            <option value="Raw Material">Raw Material</option>
                        </select>
                        @error('inventory_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-sienna mb-2">Category *</label>
                        <select name="category_id" required 
                                class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10">
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-sienna mb-2">Status *</label>
                        <select name="status" required 
                                class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                        @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-sienna mb-2">Initial Stock *</label>
                        <input type="number" name="quantity" required min="0"
                               class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10"
                               placeholder="0">
                        @error('quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-sienna mb-2">Selling Price (₱) *</label>
                        <input type="number" step="0.01" name="selling_price" required min="0"
                               class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10"
                               placeholder="0.00">
                        @error('selling_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-sienna mb-2">Expiration Date</label>
                        <input type="date" name="expiration_date"
                               class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10">
                        <p class="text-[10px] text-sage mt-1 italic">Optional: Set for perishable items</p>
                        @error('expiration_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-sienna mb-2">Stock Alert Threshold *</label>
                        <input type="number" name="stock_alert_threshold" required min="0"
                               class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10"
                               placeholder="e.g. 10">
                        <p class="text-[10px] text-sage mt-1 italic">Notify when stock falls below this level</p>
                        @error('stock_alert_threshold') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-between items-center pt-4 border-t border-tan border-opacity-30">
                    <button type="button" onclick="window.location='{{ route('products.index') }}'" 
                            class="text-gray-500 hover:text-sienna font-semibold transition">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-sienna hover:bg-opacity-90 text-cream font-inter px-10 py-2 rounded-lg shadow-md transition font-bold uppercase tracking-widest">
                        Save Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
