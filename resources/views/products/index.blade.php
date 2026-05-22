<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ __('Products') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Manage bakeshop product inventory</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="card-rustic border-sienna">
            <div class="flex flex-col gap-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full">
                        <div>
                            <form action="{{ route('products.index') }}" method="GET" class="space-y-4">
                                <input type="text" name="search" value="{{ request('search') }}"
                                       placeholder="Search products..."
                                       class="w-full rounded-xl border-sienna border-opacity-20 bg-cream bg-opacity-20 py-3 px-4 focus:ring-2 focus:ring-terracotta focus:border-terracotta transition-all shadow-inner" />
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <select name="category"
                                            class="w-full rounded-xl border-sienna border-opacity-20 bg-cream bg-opacity-20 py-3 px-4 focus:ring-2 focus:ring-terracotta focus:border-terracotta transition-all shadow-sm text-sienna font-medium">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                                {{ $cat->category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <select name="stock_status"
                                            class="w-full rounded-xl border-sienna border-opacity-20 bg-cream bg-opacity-20 py-3 px-4 focus:ring-2 focus:ring-terracotta focus:border-terracotta transition-all shadow-sm text-sienna font-medium">
                                        <option value="">All Stock Status</option>
                                        <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                                        <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                                        <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                                        <option value="inactive" {{ request('stock_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                                <div class="flex flex-wrap gap-3">
                                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-sienna px-5 py-3 text-sm font-semibold text-cream hover:bg-sienna-dark transition-all">
                                        Filter
                                    </button>
                                    <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-xl border border-sienna border-opacity-20 bg-white bg-opacity-80 px-5 py-3 text-sm font-semibold text-sienna hover:bg-cream transition-all">
                                        Reset
                                    </a>
                                </div>
                            </form>
                        </div>
                        <div class="col-span-2 flex flex-col gap-4">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="rounded-3xl border border-sienna border-opacity-10 bg-cream p-5 shadow-sm">
                                    <p class="text-xs uppercase tracking-widest text-sage font-bold">Total Products</p>
                                    <p class="mt-4 text-3xl font-black text-sienna">{{ $totalProducts }}</p>
                                </div>
                                <div class="rounded-3xl border border-sienna border-opacity-10 bg-cream p-5 shadow-sm">
                                    <p class="text-xs uppercase tracking-widest text-sage font-bold">Out of Stock</p>
                                    <p class="mt-4 text-3xl font-black text-red-700">{{ $outOfStockCount }}</p>
                                </div>
                                <div class="rounded-3xl border border-sienna border-opacity-10 bg-cream p-5 shadow-sm">
                                    <p class="text-xs uppercase tracking-widest text-sage font-bold">Low Stock</p>
                                    <p class="mt-4 text-3xl font-black text-terracotta">{{ $lowStockCount }}</p>
                                </div>
                            </div>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div class="text-sm text-sage">Showing {{ $products->count() }} of {{ $products->total() }} products</div>
                                <div class="flex flex-wrap gap-3">
                                    <a href="{{ route('products.export', request()->query()) }}" class="inline-flex items-center justify-center rounded-xl bg-sage px-5 py-3 text-sm font-semibold text-cream hover:bg-sage-dark transition-all">
                                        Export CSV
                                    </a>
                                    @if(auth()->user()->hasRole(['admin', 'manager']))
                                        <a href="{{ route('products.create') }}" class="inline-flex items-center justify-center rounded-xl bg-terracotta px-5 py-3 text-sm font-semibold text-cream hover:bg-terracotta-dark transition-all">
                                            Add Product
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-xl border border-sienna border-opacity-10 shadow-sm">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-sienna text-cream uppercase text-xs tracking-widest font-bold">
                                <th class="px-6 py-4 text-left">ID</th>
                                <th class="px-6 py-4 text-left">Product Name</th>
                                <th class="px-6 py-4 text-left">Category</th>
                                <th class="px-6 py-4 text-right">Selling Price</th>
                                <th class="px-6 py-4 text-center">Stock</th>
                                <th class="px-6 py-4 text-center">Min Stock</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                            @forelse ($products as $product)
                                <tr class="hover:bg-cream hover:bg-opacity-20 transition-colors">
                                    <td class="px-6 py-5 font-bold text-sienna opacity-60">#{{ $product->id }}</td>
                                    <td class="px-6 py-5 text-sienna font-bold text-base">{{ $product->product_name }}</td>
                                    <td class="px-6 py-5">
                                        <span class="bg-sage bg-opacity-10 text-sage-dark px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                                            {{ $product->category->category_name ?? 'Uncategorized' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-right font-black text-terracotta text-base">₱{{ number_format($product->selling_price, 2) }}</td>
                                    <td class="px-6 py-5 text-right text-sienna font-semibold">₱{{ number_format($product->buying_price, 2) }}</td>
                                    <td class="px-6 py-5 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[3rem] px-2 py-1 rounded-lg font-black {{ $product->quantity <= $product->stock_alert_threshold ? 'bg-red-100 text-red-600' : 'bg-sage bg-opacity-10 text-sage-dark' }}">
                                            {{ $product->quantity }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-center text-sienna font-semibold">{{ $product->stock_alert_threshold }}</td>
                                    <td class="px-6 py-5 text-center font-black {{ $product->status === 'Inactive' ? 'text-red-600' : 'text-sage-dark' }}">{{ $product->status }}</td>
                                    <td class="px-6 py-5 text-center">
                                        <div class="flex flex-wrap items-center justify-center gap-3">
                                            <a href="{{ route('products.show', $product->id) }}" class="px-3 py-2 rounded-xl bg-sienna bg-opacity-10 text-sienna text-xs uppercase tracking-wider font-semibold">View</a>
                                            @if(auth()->user()->hasRole(['admin', 'manager']))
                                                <a href="{{ route('products.edit', $product->id) }}" class="px-3 py-2 rounded-xl bg-sage bg-opacity-10 text-sage text-xs uppercase tracking-wider font-semibold">Edit</a>
                                                <x-alert-delete route="{{ route('products.destroy', $product->id) }}" message="Are you sure you want to delete this product?" />
                                            @else
                                                <span class="text-gray-400 text-xs italic">Read-only</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-12 text-center text-sage italic font-medium">No products found in the bakery.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
