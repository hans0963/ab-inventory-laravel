<x-app-layout>
    @if ($products->isEmpty())
        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="text-center p-6 bg-white dark:bg-gray-800 overflow-hidden sm:rounded-lg text-gray-800 dark:text-gray-200 rounded-lg shadow">
                    <h3 class="text-lg font-semibold">{{ __('No products found') }}</h3>
                    <p class="text-sm mt-2">{{ __('Don\'t have one? Create one!') }}</p>
                    <a href="{{ route('products.create') }}" class="mt-4 inline-block bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">
                        {{ __('Add your first product') }}
                    </a>
                </div>
            </div>
        </div>
    @else
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Products') }}
            </h2>
        </x-slot>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="overflow-x-auto">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-semibold">Product List</h2>
                            <a href="{{ route('products.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">
                                + Add Product
                            </a>
                        </div>
                        <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-lg">
                            <thead class="bg-gray-200 text-black dark:bg-gray-700 dark:text-white">
                                <tr>
                                    <th class="px-4 py-3 border-b text-left">ID</th>
                                    <th class="px-4 py-3 border-b text-left">Name</th>
                                    <th class="px-4 py-3 border-b text-left">Category</th>
                                    <th class="px-4 py-3 border-b text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-white">
                                @foreach ($products as $product)
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <td class="px-4 py-3">{{ $product->id }}</td>
                                    <td class="px-4 py-3">{{ $product->product_name }}</td>
                                    <td class="px-4 py-3">{{  $product->category->category_name ?? 'Uncategorized' }}</td>
                                    <td class="px-4 py-3 text-center col-auto">
                                        <div class="flex gap-2 justify-center">
                                            <a href="{{ route('products.edit', $product->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white py-1 px-3 rounded text-sm">
                                                {{ __('Edit') }}
                                            </a>
                                            <x-alert-delete route="{{ route('products.destroy', $product->id) }}" message="Are you sure you want to delete this product?" />
                                        </div>
                                    </td>
                                    
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

</x-app-layout>
