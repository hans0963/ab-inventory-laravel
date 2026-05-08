<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-md shadow-sm border-b border-sienna">
            <h2 class="font-formal text-3xl text-sienna leading-tight">
                {{ __('Manager Sales Report') }}
            </h2>
            <p class="font-inter text-sage">Detailed sales analytics and performance metrics</p>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
        <!-- Summary Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-stat-card title="Total Revenue" :value="'₱' . number_format($totalSales ?? 0, 2)" icon="💰" border="terracotta" />
            <x-stat-card title="Avg Order Value" :value="'₱' . number_format($averageOrder ?? 0, 2)" icon="🧾" border="sage" />
            <x-stat-card title="Total Orders" :value="$totalOrders ?? 0" icon="📦" border="sienna" />
            <x-stat-card title="Discounts" :value="'₱' . number_format($discounts ?? 0, 2)" icon="🎟️" border="tan" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Top Products -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-terracotta">
                <h3 class="text-xl font-lora font-semibold text-sienna mb-4 flex items-center">
                    <span class="mr-2">📈</span> Top Products by Revenue
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                                <th class="px-4 py-3 text-left">Product</th>
                                <th class="px-4 py-3 text-center">Sold</th>
                                <th class="px-4 py-3 text-right">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($topProducts ?? [] as $product)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-4 font-semibold text-sienna">{{ $product->name }}</td>
                                    <td class="px-4 py-4 text-center text-gray-600">{{ number_format($product->units_sold) }}</td>
                                    <td class="px-4 py-4 text-right font-bold text-terracotta">₱{{ number_format($product->revenue, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Performance Overview -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-sage">
                <h3 class="text-xl font-lora font-semibold text-sienna mb-4 flex items-center">
                    <span class="mr-2">📊</span> Product Performance
                </h3>
                <div class="space-y-6">
                    @foreach($topProducts ?? [] as $product)
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm font-medium text-sienna">{{ $product->name }}</span>
                                <span class="text-xs font-semibold text-sage">{{ round($product->performance) }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-2">
                                <div class="bg-sage h-2 rounded-full transition-all duration-500" style="width: {{ $product->performance }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-sienna">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Daily Sales (Last 7 Days)</h3>
                <canvas id="dailySalesChart"></canvas>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-sage">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Sales by Category</h3>
                <canvas id="salesByCategoryChart"></canvas>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Daily Sales Chart
            const dailySalesCtx = document.getElementById('dailySalesChart').getContext('2d');
            new Chart(dailySalesCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($dailySalesLabels) !!},
                    datasets: [{
                        label: 'Daily Sales (₱)',
                        data: {!! json_encode($dailySalesData) !!},
                        borderColor: '#E2725B',
                        backgroundColor: 'rgba(226, 114, 91, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });

            // Sales by Category Chart
            const categoryCtx = document.getElementById('salesByCategoryChart').getContext('2d');
            new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($categoryLabels) !!},
                    datasets: [{
                        data: {!! json_encode($categorySalesData) !!},
                        backgroundColor: ['#E2725B', '#8A9A5B', '#A0522D', '#D2B48C', '#F5F5DC']
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
