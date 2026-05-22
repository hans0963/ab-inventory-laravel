<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Category Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('categories.update', $category->id) }}">
                    @csrf
                    @method('PUT')

                    <!-- Category Name -->
                    <div class="mb-4">
                        <x-input-label for="category_name" value="Category Name *" class="text-[10px] uppercase tracking-widest text-sage" />
                        <x-text-input id="category_name" type="text" name="category_name" class="block mt-1 w-full" value="{{ old('category_name', $category->category_name) }}" required />
                        <x-input-error :messages="$errors->get('category_name')" class="mt-2" />
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <x-input-label for="description" value="Description" class="text-[10px] uppercase tracking-widest text-sage" />
                        <textarea id="description" name="description" class="block mt-1 w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm bg-cream bg-opacity-10" rows="3">{{ old('description', $category->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <input type="hidden" name="status" value="{{ old('status', $category->status) }}" />

                    <!-- Buttons -->
                    <div class="flex justify-end gap-3">
                        <x-secondary-button type="button" onclick="window.location='{{ route('categories.index') }}'">Cancel</x-secondary-button>
                        <x-primary-button type="submit">Save</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
