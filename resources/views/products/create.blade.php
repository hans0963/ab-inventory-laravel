<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-3 gap-6">
                <!-- Left: Product Image -->
                <div class="col-span-1 bg-white dark:bg-gray-800 p-6 shadow-sm rounded-lg">
                    <label class="block text-lg font-medium text-gray-700 dark:text-gray-200">Create a product</label>
                    <img src="{{ asset('assets/img/products/default.webp') }}" class="w-full rounded-md mb-4">
                </div>

                <!-- Right: Product Form -->
                <div class="col-span-2 bg-white dark:bg-gray-800 p-6 shadow-sm rounded-lg">
                    <form method="POST" action="{{ route('products.store') }}">
                        @csrf

                        <x-input label="Name" name="product_name" required placeholder="Product name" />

                        <div class="grid grid-cols-1 gap-4">
                            <x-select 
                                label="Product Category" 
                                name="category_id" 
                                :options="$categories->pluck('category_name', 'id')" 
                                required 
                            />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <x-input label="Buying Price" name="buying_price" type="number" required placeholder="0" />
                            <x-input label="Selling Price" name="selling_price" type="number" required placeholder="0" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <x-input label="Quantity" name="quantity" type="number" required placeholder="0" />
                            <x-input label="Quantity Alert" name="stock_alert_threshold" type="number" required placeholder="0" />
                        </div>

                        <x-input label="Notes" name="notes" type="text" placeholder="Product notes" />

                        <!-- Buttons -->
                        <div class="flex justify-end space-x-2 mt-4">
                            <x-button type="submit" color="blue">Save</x-button>
                            <x-button type="button" color="red" onclick="window.history.back();">Cancel</x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
