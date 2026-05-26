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

            <div id="recipe-section" class="space-y-5 pt-8 border-t border-sienna border-opacity-10">
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-sienna">Recipe Ingredients</h3>
                        <p class="text-[10px] text-sage mt-1 italic font-medium">Optional for finished products. Quantities are per 1 product unit.</p>
                    </div>
                    <label class="inline-flex items-center gap-2 text-xs font-black text-sage uppercase tracking-widest">
                        <input type="checkbox" name="recipe_enabled" value="1" class="rounded border-sienna text-sienna focus:ring-sienna" {{ old('recipe_enabled') ? 'checked' : '' }}>
                        Track Recipe
                    </label>
                </div>

                <div id="recipe-items" class="space-y-4">
                    @php
                        $oldIngredients = old('recipe_ingredients', [['raw_material_id' => '', 'quantity_per_unit' => '']]);
                    @endphp
                    @foreach($oldIngredients as $index => $ingredient)
                        <div class="recipe-item card-rustic bg-cream bg-opacity-30 border-sage p-4 relative">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label value="Raw Material" class="text-[10px] uppercase tracking-widest text-sage" />
                                    <select name="recipe_ingredients[{{ $index }}][raw_material_id]" class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm">
                                        <option value="">Select Raw Material</option>
                                        @foreach($rawMaterials as $material)
                                            <option value="{{ $material->id }}" {{ ($ingredient['raw_material_id'] ?? '') == $material->id ? 'selected' : '' }}>
                                                {{ $material->material_name }}{{ $material->unit ? ' (' . $material->unit . ')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <x-input-label value="Quantity Per Unit" class="text-[10px] uppercase tracking-widest text-sage" />
                                    <x-text-input name="recipe_ingredients[{{ $index }}][quantity_per_unit]" type="number" step="0.001" min="0.001" value="{{ $ingredient['quantity_per_unit'] ?? '' }}" placeholder="0.000" class="mt-1 block w-full !text-sm" />
                                </div>
                            </div>
                            <button type="button" class="absolute top-2 right-2 text-red-600 hover:text-red-800 transition remove-recipe-item" title="Remove Ingredient">
                                Remove
                            </button>
                        </div>
                    @endforeach
                </div>

                <button type="button" id="add-recipe-item" class="inline-flex items-center px-4 py-2 bg-sage border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:bg-sage-dark transition">
                    + Add Ingredient
                </button>

                <div>
                    <x-input-label for="recipe_notes" value="Recipe Notes" class="text-[10px] uppercase tracking-widest text-sage" />
                    <textarea id="recipe_notes" name="recipe_notes" rows="2" class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm">{{ old('recipe_notes') }}</textarea>
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

    @push('scripts')
    <script>
        let recipeIndex = {{ count($oldIngredients) }};
        const rawMaterials = {!! json_encode($rawMaterials) !!};

        document.getElementById('add-recipe-item').addEventListener('click', function() {
            const container = document.getElementById('recipe-items');
            const newItem = document.createElement('div');
            newItem.className = 'recipe-item card-rustic bg-cream bg-opacity-30 border-sage p-4 relative';

            let options = '<option value="">Select Raw Material</option>';
            rawMaterials.forEach(material => {
                const unit = material.unit ? ` (${material.unit})` : '';
                options += `<option value="${material.id}">${material.material_name}${unit}</option>`;
            });

            newItem.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-sage uppercase tracking-widest mb-1">Raw Material</label>
                        <select name="recipe_ingredients[${recipeIndex}][raw_material_id]" class="block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm">
                            ${options}
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-sage uppercase tracking-widest mb-1">Quantity Per Unit</label>
                        <input name="recipe_ingredients[${recipeIndex}][quantity_per_unit]" type="number" step="0.001" min="0.001" placeholder="0.000" class="block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm" />
                    </div>
                </div>
                <button type="button" class="absolute top-2 right-2 text-red-600 hover:text-red-800 transition remove-recipe-item" title="Remove Ingredient">
                    Remove
                </button>
            `;
            container.appendChild(newItem);
            recipeIndex++;
            attachRecipeRemoveHandler();
        });

        function attachRecipeRemoveHandler() {
            document.querySelectorAll('.remove-recipe-item').forEach(btn => {
                btn.onclick = function() {
                    const items = document.querySelectorAll('.recipe-item');
                    if (items.length > 1) {
                        this.closest('.recipe-item').remove();
                    }
                };
            });
        }

        attachRecipeRemoveHandler();
    </script>
    @endpush
</x-app-layout>
