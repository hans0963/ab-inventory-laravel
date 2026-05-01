<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-md shadow-sm border-b border-sienna">
            <h2 class="font-pacifico text-3xl text-sienna leading-tight">
                {{ __('Sales Report Overview') }}
            </h2>
            <p class="font-inter text-sage">Detailed sales analytics and insights</p>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
        {{-- Summary Metrics --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <x-stat-card title="Total Revenue" :value="'₱' . number_format($totalSales, 2)" icon="💰" border="terracotta" />
            <x-stat-card title="Total Orders" :value="$totalOrders" icon="📦" border="sage" />
            <x-stat-card title="Products Sold" :value="$totalProductsSold" icon="📤" border="sienna" />
        </div>

        {{-- Filters Section --}}
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-sienna">
            <h3 class="text-lg font-semibold text-sienna mb-4 flex items-center">
                <span class="mr-2">🔍</span> Filter Sales Data
            </h3>
            <form method="GET" action="{{ route('inventory.sales') }}" class="flex flex-wrap gap-4">
                <div class="flex flex-col">
                    <label class="text-xs font-bold text-sage uppercase tracking-widest mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" 
                           class="border-sienna focus:ring-terracotta focus:border-terracotta rounded-md bg-cream bg-opacity-10 text-sm">
                </div>
                <div class="flex flex-col">
                    <label class="text-xs font-bold text-sage uppercase tracking-widest mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}" 
                           class="border-sienna focus:ring-terracotta focus:border-terracotta rounded-md bg-cream bg-opacity-10 text-sm">
                </div>
                <div class="flex flex-col">
                    <label class="text-xs font-bold text-sage uppercase tracking-widest mb-1">Product</label>
                    <select name="product" class="border-sienna focus:ring-terracotta focus:border-terracotta rounded-md bg-cream bg-opacity-10 text-sm">
                        <option value="">All Products</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ request('product') == $product->id ? 'selected' : '' }}>
                                {{ $product->product_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-sienna hover:bg-opacity-90 text-cream px-6 py-2 rounded-md transition font-semibold text-sm">
                        Apply Filter
                    </button>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Best-Selling Products --}}
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-sage">
                <h3 class="text-lg font-lora font-semibold text-sienna mb-4">Best-Selling Products</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-sienna text-cream uppercase text-[10px] tracking-widest">
                                <th class="px-4 py-2 text-left">Product</th>
                                <th class="px-4 py-2 text-left">Category</th>
                                <th class="px-4 py-2 text-center">Sold</th>
                                <th class="px-4 py-2 text-right">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-tan divide-opacity-20">
                            @foreach($bestSellingProducts as $product)
                                <tr class="hover:bg-cream hover:bg-opacity-30 transition">
                                    <td class="px-4 py-3 font-semibold text-sienna">{{ $product->product_name }}</td>
                                    <td class="px-4 py-3 text-gray-600 text-xs">{{ $product->category_name ?? 'Uncategorized' }}</td>
                                    <td class="px-4 py-3 text-center font-bold text-sage">{{ $product->total_sold }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-terracotta">₱{{ number_format($product->total_revenue, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $bestSellingProducts->links() }}
                </div>
            </div>           

            {{-- Recent Sales Transactions --}}
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-terracotta">
                <h3 class="text-lg font-lora font-semibold text-sienna mb-4">Recent Sales Transactions</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-sienna text-cream uppercase text-[10px] tracking-widest">
                                <th class="px-4 py-2 text-left">Order Date</th>
                                <th class="px-4 py-2 text-left">Customer</th>
                                <th class="px-4 py-2 text-center">Qty</th>
                                <th class="px-4 py-2 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-tan divide-opacity-20">
                            @forelse($salesQuery as $sale)
                                <tr class="hover:bg-cream hover:bg-opacity-30 transition">
                                    <td class="px-4 py-3 text-gray-600 text-xs">{{ \Carbon\Carbon::parse($sale->order_date)->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 font-semibold text-sienna">{{ $sale->customer_name }}</td>
                                    <td class="px-4 py-3 text-center text-sage font-bold">{{ $sale->total_quantity_sold }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-terracotta">₱{{ number_format($sale->total_sales_value, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-8 text-gray-500 italic">No sales data found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $salesQuery->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
