<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ __('Create Category') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Define a new group for your products</p>
            </div>
        </div>
    </x-slot>

    <div class="card-rustic border-sienna max-w-2xl mx-auto mt-8">
        <form method="POST" action="{{ route('categories.store') }}" class="space-y-8">
            @csrf

            <!-- Category Name -->
            <div>
                <x-input-label for="category_name" value="Category Name *" class="text-[10px] uppercase tracking-widest text-sage" />
                <x-text-input id="category_name" name="category_name" type="text" value="{{ old('category_name') }}" required placeholder="e.g. Breads, Pastries, Raw Ingredients" class="mt-1 block w-full !text-sm" />
                <x-input-error :messages="$errors->get('category_name')" class="mt-2" />
            </div>

            <!-- Description -->
            <div>
                <x-input-label for="description" value="Description" class="text-[10px] uppercase tracking-widest text-sage" />
                <textarea id="description" name="description" placeholder="Describe the items in this category..." rows="4" class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm bg-cream bg-opacity-10">{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>

            <!-- Status -->
            <div>
                <x-input-label for="status" value="Status *" class="text-[10px] uppercase tracking-widest text-sage" />
                <select id="status" name="status" required class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm bg-cream bg-opacity-10">
                    <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <x-input-error :messages="$errors->get('status')" class="mt-2" />
            </div>

            <!-- Buttons -->
            <div class="flex justify-between items-center pt-8 border-t border-sienna border-opacity-10">
                <a href="{{ route('categories.index') }}" class="text-xs font-black text-sage uppercase tracking-widest hover:text-sienna transition">
                    ← Back to Categories
                </a>
                <x-primary-button class="!bg-terracotta hover:!bg-terracotta-dark shadow-rustic">
                    Save Category
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
