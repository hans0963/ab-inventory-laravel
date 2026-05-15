<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-md shadow-sm border-b border-sienna">
            <div>
                <h2 class="font-formal text-3xl text-sienna leading-tight">
                    {{ $category->category_name }}
                </h2>
                <p class="font-inter text-sage">Category Details and Products</p>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('categories.edit', $category->id) }}" 
                   class="bg-sage hover:bg-sage-dark text-white font-bold px-6 py-2 rounded-lg shadow-md transition flex items-center">
                    ✎ Edit
                </a>
                <a href="{{ route('categories.index') }}" 
                   class="bg-sienna hover:bg-opacity-90 text-white font-bold px-6 py-2 rounded-lg shadow-md transition flex items-center">
                    ← Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
        <!-- Category Info Card -->
        <div class="bg-white rounded-lg shadow-md p-8 border-l-4 border-terracotta">
            <div class="flex items-start gap-6">
                <div class="bg-terracotta bg-opacity-10 p-4 rounded-2xl">
                    <span class="text-4xl">📂</span>
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-lora font-bold text-sienna mb-2">{{ $category->category_name }}</h3>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        {{ $category->description ?? 'No description available for this artisan group.' }}
                    </p>
                    <div class="flex gap-8">
                        <div class="bg-cream bg-opacity-40 px-4 py-2 rounded-lg border border-tan border-opacity-20">
                            <p class="text-[10px] uppercase tracking-widest text-sage font-bold">Total Products</p>
                            <p class="text-xl font-black text-sienna">{{ $category->products_count }}</p>
                        </div>
                        <div class="bg-cream bg-opacity-40 px-4 py-2 rounded-lg border border-tan border-opacity-20">
                            <p class="text-[10px] uppercase tracking-widest text-sage font-bold">Created Date</p>
                            <p class="text-xl font-black text-sienna">{{ $category->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products in this Category -->
        <div class="card-rustic border-sienna">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-lora font-bold text-sienna">Products in this Category</h3>
                <a href="{{ route('products.create', ['category_id' => $category->id]) }}" 
                   class="text-terracotta font-black text-sm hover:underline">+ Add Product to this Category</a>
            </div>

            <div class="overflow-hidden rounded-xl border border-sienna border-opacity-10 shadow-sm">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-sienna text-cream uppercase text-xs tracking-widest font-bold">
                            <th class="px-6 py-4 text-left">Product Name</th>
                            <th class="px-6 py-4 text-right">Price</th>
                            <th class="px-6 py-4 text-center">Stock</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                        @forelse ($products as $product)
                            <tr class="hover:bg-cream hover:bg-opacity-20 transition-colors">
                                <td class="px-6 py-5 text-sienna font-bold text-base">{{ $product->product_name }}</td>
                                <td class="px-6 py-5 text-right font-black text-terracotta text-base">
                                    ₱{{ number_format($product->selling_price, 2) }}
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[3rem] px-2 py-1 rounded-lg font-black {{ $product->quantity <= $product->stock_alert_threshold ? 'bg-red-100 text-red-600' : 'bg-sage bg-opacity-10 text-sage-dark' }}">
                                        {{ $product->quantity }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <a href="{{ route('products.show', $product->id) }}" class="text-sage hover:text-sienna font-black mr-3">View</a>
                                    <a href="{{ route('products.edit', $product->id) }}" class="text-terracotta hover:text-terracotta-dark font-black">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-sage italic font-medium">No products found in this category.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
