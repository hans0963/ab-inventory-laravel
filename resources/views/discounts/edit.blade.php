<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-md shadow-sm border-b border-sienna">
            <h2 class="font-pacifico text-3xl text-sienna leading-tight">
                {{ __('Edit Discount Type') }}
            </h2>
            <p class="font-inter text-sage">Update discount information</p>
        </div>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md p-8 border-l-4 border-sienna">
            <form method="POST" action="{{ route('discounts.update', $discountType->id) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-sienna mb-2">Discount Name *</label>
                    <input type="text" name="discount_name" required value="{{ $discountType->discount_name }}"
                           class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10"
                           placeholder="e.g., PWD, Senior Citizen, Store Discount">
                    @error('discount_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-sienna mb-2">Discount Percentage (%) *</label>
                    <input type="number" step="0.01" name="discount_percentage" required min="0" max="100"
                           value="{{ $discountType->discount_percentage }}"
                           class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10"
                           placeholder="e.g., 10 for 10%">
                    @error('discount_percentage') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-sienna mb-2">Description</label>
                    <textarea name="description" rows="3"
                           class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10"
                           placeholder="e.g., For persons with disability">{{ $discountType->description }}</textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-sienna mb-2">Status *</label>
                    <select name="status" required 
                            class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10">
                        <option value="Active" {{ $discountType->status === 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ $discountType->status === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-4 pt-6">
                    <button type="submit" class="px-6 py-2 bg-sienna text-white rounded-md hover:bg-opacity-80 font-semibold">
                        Update Discount Type
                    </button>
                    <a href="{{ route('discounts.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
