<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">{{ __('Product Details') }}</h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Review the selected product record</p>
            </div>
            <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-xl border border-sienna border-opacity-20 bg-cream px-5 py-3 text-sm font-semibold text-sienna hover:bg-white transition-all">
                Back to Products
            </a>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="card-rustic border-sienna max-w-6xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 p-8">
                <div class="space-y-6 rounded-3xl border border-sienna border-opacity-10 bg-cream p-6 shadow-sm">
                    <div>
                        <p class="text-xs uppercase tracking-widest text-sage font-bold">Product Name</p>
                        <p class="mt-3 text-2xl font-black text-sienna">{{ $product->product_name }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-widest text-sage font-bold">Category</p>
                        <p class="mt-3 text-base text-sienna">{{ $product->category->category_name ?? 'Uncategorized' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-widest text-sage font-bold">Status</p>
                        <span class="mt-3 inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $product->status === 'Inactive' ? 'bg-red-100 text-red-700' : 'bg-sage bg-opacity-20 text-sage-dark' }}">{{ $product->status }}</span>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="rounded-3xl border border-sienna border-opacity-10 bg-white p-6 shadow-sm">
                            <p class="text-xs uppercase tracking-widest text-sage font-bold">Selling Price</p>
                            <p class="mt-3 text-3xl font-black text-terracotta">₱{{ number_format($product->selling_price, 2) }}</p>
                        </div>
                        <div class="rounded-3xl border border-sienna border-opacity-10 bg-white p-6 shadow-sm">
                            <p class="text-xs uppercase tracking-widest text-sage font-bold">Buying Price</p>
                            <p class="mt-3 text-3xl font-black text-sienna">₱{{ number_format($product->buying_price, 2) }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="rounded-3xl border border-sienna border-opacity-10 bg-white p-6 shadow-sm">
                            <p class="text-xs uppercase tracking-widest text-sage font-bold">Current Stock</p>
                            <p class="mt-3 text-3xl font-black text-sienna">{{ $product->quantity }}</p>
                        </div>
                        <div class="rounded-3xl border border-sienna border-opacity-10 bg-white p-6 shadow-sm">
                            <p class="text-xs uppercase tracking-widest text-sage font-bold">Restock Threshold</p>
                            <p class="mt-3 text-3xl font-black text-sienna">{{ $product->stock_alert_threshold }}</p>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-sienna border-opacity-10 bg-white p-6 shadow-sm">
                        <p class="text-xs uppercase tracking-widest text-sage font-bold">Description</p>
                        <p class="mt-3 text-sm leading-7 text-sienna">{{ $product->description ?: 'No description provided.' }}</p>
                    </div>

                    <div class="rounded-3xl border border-sienna border-opacity-10 bg-white p-6 shadow-sm">
                        <p class="text-xs uppercase tracking-widest text-sage font-bold">Recipe Ingredients</p>
                        @if($product->recipe && $product->recipe->ingredients->isNotEmpty())
                            <div class="mt-4 overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead>
                                        <tr class="border-b border-sienna border-opacity-10 text-left text-[10px] uppercase tracking-widest text-sage">
                                            <th class="py-2 pr-4">Raw Material</th>
                                            <th class="py-2 pr-4">Per Unit</th>
                                            <th class="py-2">Current Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-sienna divide-opacity-10 text-sienna">
                                        @foreach($product->recipe->ingredients as $ingredient)
                                            <tr>
                                                <td class="py-3 pr-4 font-semibold">{{ $ingredient->rawMaterial?->material_name }}</td>
                                                <td class="py-3 pr-4">{{ number_format($ingredient->quantity_per_unit) }} {{ $ingredient->rawMaterial?->unit }}</td>
                                                <td class="py-3">{{ $ingredient->rawMaterial?->quantity ?? 0 }} {{ $ingredient->rawMaterial?->unit }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @if($product->recipe->notes)
                                <p class="mt-4 text-sm leading-7 text-sienna">{{ $product->recipe->notes }}</p>
                            @endif
                        @else
                            <p class="mt-3 text-sm leading-7 text-sienna">No recipe configured.</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="rounded-3xl border border-sienna border-opacity-10 bg-white p-6 shadow-sm">
                            <p class="text-xs uppercase tracking-widest text-sage font-bold">Expiration Date</p>
                            <p class="mt-3 text-base text-sienna">{{ $product->expiration_date?->format('F j, Y') ?? 'N/A' }}</p>
                        </div>
                        <div class="rounded-3xl border border-sienna border-opacity-10 bg-white p-6 shadow-sm">
                            <p class="text-xs uppercase tracking-widest text-sage font-bold">Created At</p>
                            <p class="mt-3 text-base text-sienna">{{ $product->created_at->format('F j, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
