@php
    $selectedIds = old('applicable_ids', $discount?->applicable_ids ?? []);
    $selectedIds = is_array($selectedIds) ? array_map('strval', $selectedIds) : [];
    $applicableTo = old('applicable_to', $discount->applicable_to ?? 'All');
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <div>
        <x-input-label for="discount_name" value="Discount Name *" class="text-[10px] uppercase tracking-widest text-sage" />
        <x-text-input id="discount_name" name="discount_name" type="text" value="{{ old('discount_name', $discount->discount_name ?? '') }}" required class="mt-1 block w-full !text-sm" placeholder="Senior Citizen, PWD, Bulk Order" />
        <x-input-error :messages="$errors->get('discount_name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="discount_type" value="Discount Type *" class="text-[10px] uppercase tracking-widest text-sage" />
        <select id="discount_type" name="discount_type" required class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm">
            <option value="Percentage" {{ old('discount_type', $discount->discount_type ?? 'Percentage') === 'Percentage' ? 'selected' : '' }}>Percentage</option>
            <option value="Fixed Amount" {{ old('discount_type', $discount->discount_type ?? '') === 'Fixed Amount' ? 'selected' : '' }}>Fixed Amount</option>
        </select>
        <x-input-error :messages="$errors->get('discount_type')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="discount_value" value="Discount Value *" class="text-[10px] uppercase tracking-widest text-sage" />
        <x-text-input id="discount_value" name="discount_value" type="number" step="0.01" min="0" value="{{ old('discount_value', $discount->discount_value ?? $discount->discount_percentage ?? '') }}" required class="mt-1 block w-full !text-sm" />
        <x-input-error :messages="$errors->get('discount_value')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="minimum_purchase_amount" value="Minimum Purchase Amount" class="text-[10px] uppercase tracking-widest text-sage" />
        <x-text-input id="minimum_purchase_amount" name="minimum_purchase_amount" type="number" step="0.01" min="0" value="{{ old('minimum_purchase_amount', $discount->minimum_purchase_amount ?? 0) }}" class="mt-1 block w-full !text-sm" />
        <x-input-error :messages="$errors->get('minimum_purchase_amount')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="applicable_to" value="Applicable To *" class="text-[10px] uppercase tracking-widest text-sage" />
        <select id="applicable_to" name="applicable_to" required class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm">
            <option value="All" {{ $applicableTo === 'All' ? 'selected' : '' }}>All</option>
            <option value="Category" {{ $applicableTo === 'Category' ? 'selected' : '' }}>Category</option>
            <option value="Product" {{ $applicableTo === 'Product' ? 'selected' : '' }}>Product</option>
        </select>
        <x-input-error :messages="$errors->get('applicable_to')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="status" value="Status Toggle *" class="text-[10px] uppercase tracking-widest text-sage" />
        <select id="status" name="status" required class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm">
            <option value="Active" {{ old('status', $discount->status ?? 'Active') === 'Active' ? 'selected' : '' }}>Active</option>
            <option value="Inactive" {{ old('status', $discount->status ?? '') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
            <option value="Expired" {{ old('status', $discount->status ?? '') === 'Expired' ? 'selected' : '' }}>Expired</option>
        </select>
        <x-input-error :messages="$errors->get('status')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="start_date" value="Start Date" class="text-[10px] uppercase tracking-widest text-sage" />
        <x-text-input id="start_date" name="start_date" type="date" value="{{ old('start_date', $discount?->start_date?->format('Y-m-d')) }}" class="mt-1 block w-full !text-sm" />
        <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="end_date" value="End Date" class="text-[10px] uppercase tracking-widest text-sage" />
        <x-text-input id="end_date" name="end_date" type="date" value="{{ old('end_date', $discount?->end_date?->format('Y-m-d')) }}" class="mt-1 block w-full !text-sm" />
        <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="applicable_ids" value="Select Applicable Products/Categories" class="text-[10px] uppercase tracking-widest text-sage" />
        <select id="applicable_ids" name="applicable_ids[]" multiple size="8" class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm">
            <optgroup label="Categories">
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $applicableTo === 'Category' && in_array((string) $category->id, $selectedIds, true) ? 'selected' : '' }}>
                        {{ $category->category_name }}
                    </option>
                @endforeach
            </optgroup>
            <optgroup label="Products">
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ $applicableTo === 'Product' && in_array((string) $product->id, $selectedIds, true) ? 'selected' : '' }}>
                        {{ $product->product_name }}
                    </option>
                @endforeach
            </optgroup>
        </select>
        <p class="text-[10px] text-sage mt-1 italic font-medium">Leave blank when Applicable To is All.</p>
        <x-input-error :messages="$errors->get('applicable_ids')" class="mt-2" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="description" value="Description" class="text-[10px] uppercase tracking-widest text-sage" />
        <textarea id="description" name="description" rows="3" class="input-artisan mt-1">{{ old('description', $discount->description ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>
</div>
