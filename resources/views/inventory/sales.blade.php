<x-app-layout>
    <div class="bg-gray-100 p-6">
        {{-- Sales Report Header --}}
        <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-semibold text-gray-700">📈 Sales Report Overview</h2>
        </div>

        {{-- Filters Section --}}
        {{-- <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">🔍 Filter Sales Data</h3>
            <form method="GET" action="{{ route('inventory.sales') }}" class="flex flex-wrap gap-4">
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="border rounded-lg p-2 w-48">
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="border rounded-lg p-2 w-48">
                <select name="product" class="border rounded-lg p-2 w-48">
                    <option value="">All Products</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ request('product') == $product->id ? 'selected' : '' }}>
                            {{ $product->product_name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Filter</button>
            </form>
        </div> --}


         {{-- Future Sales Insights (Expandable Sections) --}}
         <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
            {{-- Best-Selling Products --}}
            <div class="bg-white shadow-lg rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-700">🏆 Best-Selling Products</h3>
                <p class="text-gray-500">Top products based on sales volume.</p>
                <table class="min-w-full border border-gray-300 mt-4">
                    <thead>
                        <tr class="bg-gradient-to-r from-green-500 to-green-700 text-white">
                            <th class="px-4 py-2 text-left">Product</th>
                            <th class="px-4 py-2 text-left">Category</th>
                            <th class="px-4 py-2 text-left">Total Sold</th>
                            <th class="px-4 py-2 text-left">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($bestSellingProducts as $product)
                            <tr class="hover:bg-gray-100 transition">
                                <td class="px-4 py-3">{{ $product->product_name }}</td>
                                <td class="px-4 py-3">{{ $product->category_name ?? 'Uncategorized' }}</td>
                                <td class="px-4 py-3 font-semibold">{{ $product->total_sold }}</td>
                                <td class="px-4 py-3 font-semibold text-green-600">₱{{ number_format($product->total_revenue, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $bestSellingProducts->links() }}
                </div>
            </div>           

            {{-- 📊 Sales Summary --}}
            <div class="bg-gradient-to-r from-purple-500 to-indigo-600 shadow-lg rounded-lg p-6 text-white">
                <h3 class="text-lg font-semibold flex items-center gap-2">
                    📊 Sales Summary
                </h3>
                <p class="text-sm opacity-80">Overview of sales performance.</p>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-white bg-opacity-20 p-4 rounded-lg flex items-center gap-4">
                        <div class="bg-green-600 p-3 rounded-full">
                            💰
                        </div>
                        <div>
                            <p class="text-sm opacity-80">Total Revenue</p>
                            <p class="text-xl font-bold">₱{{ number_format($salesSummary->total_revenue ?? 0, 2) }}</p>
                        </div>
                    </div>
                    <div class="bg-white bg-opacity-20 p-4 rounded-lg flex items-center gap-4">
                        <div class="bg-blue-600 p-3 rounded-full">
                            📦
                        </div>
                        <div>
                            <p class="text-sm opacity-80">Total Orders</p>
                            <p class="text-xl font-bold">{{ $salesSummary->total_orders ?? 0 }}</p>
                        </div>
                    </div>
                    <div class="bg-white bg-opacity-20 p-4 rounded-lg flex items-center gap-4">
                        <div class="bg-yellow-500 p-3 rounded-full">
                            📤
                        </div>
                        <div>
                            <p class="text-sm opacity-80">Total Products Sold</p>
                            <p class="text-xl font-bold">{{ $salesSummary->total_products_sold ?? 0 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sales Data Table --}}
        <div class="bg-white shadow-lg rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">🛒 Recent Sales Transactions</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden">
                    <thead>
                        <tr class="bg-gradient-to-r from-blue-500 to-blue-700 text-white">
                            <th class="px-4 py-2 text-left">Order Date</th>
                            <th class="px-4 py-2 text-left">Customer</th>
                            <th class="px-4 py-2 text-left">Sold By</th>
                            <th class="px-4 py-2 text-left">Total Quantity</th>
                            <th class="px-4 py-2 text-left">Total Sales</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($salesQuery as $sale)
                            <tr class="hover:bg-gray-100 transition">
                                <td class="px-4 py-3">{{ $sale->order_date }}</td>
                                <td class="px-4 py-3">{{ $sale->customer_name }}</td>
                                <td class="px-4 py-3">{{ $sale->sold_by }}</td>
                                <td class="px-4 py-3">{{ $sale->total_quantity_sold }}</td>
                                <td class="px-4 py-3 font-semibold text-green-600">{{ number_format($sale->total_sales_value, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $salesQuery->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
