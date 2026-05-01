<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-md shadow-sm border-b border-sienna">
            <h2 class="font-pacifico text-3xl text-sienna leading-tight">
                {{ __('Products') }}
            </h2>
            <p class="font-inter text-sage">Manage bakeshop product inventory</p>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-sienna">
            
            {{-- Header with search + add product --}}
            <div class="flex justify-between items-center mb-6">
                <div class="w-1/3">
                    <form action="{{ route('products.index') }}" method="GET">
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Search products..." 
                                   class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10 pl-10">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-sienna opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </form>
                </div>
                @if(auth()->user()->hasRole(['admin', 'manager']))
                <a href="{{ route('products.create') }}" 
                   class="bg-terracotta hover:bg-opacity-90 text-cream font-inter px-6 py-2 rounded-lg shadow-md transition flex items-center">
                    <span class="mr-2">+</span> Add Product
                </a>
                @endif
            </div>

            {{-- Products Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-sienna text-cream uppercase text-xs tracking-wider">
                            <th class="px-4 py-3 text-left">ID</th>
                            <th class="px-4 py-3 text-left">Product Name</th>
                            <th class="px-4 py-3 text-left">Category</th>
                            <th class="px-4 py-3 text-right">Selling Price</th>
                            <th class="px-4 py-3 text-center">Stock</th>
                            <th class="px-4 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sienna divide-opacity-20">
                        @forelse ($products as $product)
                            <tr class="hover:bg-cream hover:bg-opacity-30 transition">
                                <td class="px-4 py-4 font-semibold text-sienna">#{{ $product->id }}</td>
                                <td class="px-4 py-4 text-sienna font-medium">{{ $product->product_name }}</td>
                                <td class="px-4 py-4">
                                    <span class="bg-sage bg-opacity-10 text-sage px-2 py-1 rounded text-xs font-bold uppercase">
                                        {{ $product->category->category_name ?? 'Uncategorized' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-right font-bold text-terracotta">
                                    ₱{{ number_format($product->selling_price, 2) }}
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="font-bold {{ $product->quantity <= $product->stock_alert_threshold ? 'text-red-600' : 'text-sage' }}">
                                        {{ $product->quantity }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center space-x-2">
                                    @if(auth()->user()->hasRole(['admin', 'manager']))
                                    <a href="{{ route('products.edit', $product->id) }}" class="text-sage hover:text-sienna transition inline-block">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <x-alert-delete 
                                        route="{{ route('products.destroy', $product->id) }}" 
                                        message="Are you sure you want to delete this product?" />
                                    @else
                                    <span class="text-gray-400 text-xs italic">No actions available</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500 italic">No products found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
