<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-md shadow-sm border-b border-sienna">
            <h2 class="font-pacifico text-3xl text-sienna leading-tight">
                {{ __('Sales Report') }}
            </h2>
            <p class="font-inter text-sage">Comprehensive sales analytics and insights</p>
        </div>
    </x-slot>    

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
        <!-- Summary Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-stat-card title="Total Sales" :value="number_format($totalSales ?? 0, 2)" icon="📊" border="terracotta" />
            <x-stat-card title="Average Order" :value="number_format($averageOrder ?? 0, 2)" icon="🧾" border="sage" />
            <x-stat-card title="Total Orders" :value="$totalOrders ?? 0" icon="📦" border="sienna" />
            <x-stat-card title="SC/PWD Discount" :value="number_format($discounts ?? 0, 2)" icon="🎟️" border="tan" />
        </div>

        <!-- Daily Sales Chart -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-terracotta">
            <h2 class="text-lg font-lora font-semibold mb-4 text-sienna">Daily Sales (Last 7 Days)</h2>
            <canvas id="dailySalesChart"></canvas>
        </div>

        <!-- Sales by Category -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-sage">
            <h2 class="text-lg font-lora font-semibold mb-4 text-sienna">Sales by Category</h2>
            <canvas id="salesByCategoryChart"></canvas>
        </div>

        <!-- Top Selling Products -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-sienna">
            <h2 class="text-lg font-lora font-semibold mb-4 text-sienna">Top Selling Products</h2>
            <table class="min-w-full border border-sienna rounded-lg overflow-hidden">
                <thead>
                    <tr class="bg-sienna text-cream">
                        <th class="px-4 py-2 text-left">Product</th>
                        <th class="px-4 py-2 text-left">Units Sold</th>
                        <th class="px-4 py-2 text-left">Revenue</th>
                        <th class="px-4 py-2 text-left">Performance</th>
                    </tr>
                </thead>
                <tbody class="bg-cream divide-y divide-sienna">
                    @foreach($topProducts ?? [] as $product)
                        <tr>
                            <td class="px-4 py-3 font-semibold text-sienna">{{ $product->name }}</td>
                            <td class="px-4 py-3 text-sienna">{{ $product->units_sold }}</td>
                            <td class="px-4 py-3 font-semibold text-terracotta">₱{{ number_format($product->revenue, 2) }}</td>
                            <td class="px-4 py-3">
                                <div class="bg-terracotta bg-opacity-30 h-2 rounded-full">
                                    <div class="bg-terracotta h-2 rounded-full" style="width: {{ $product->performance }}%"></div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
