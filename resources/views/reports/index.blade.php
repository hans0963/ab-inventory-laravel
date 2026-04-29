<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-md shadow-sm border-b border-sienna">
            <h2 class="font-pacifico text-3xl text-sienna leading-tight">
                {{ __('Admin Reports') }}
            </h2>
            <p class="font-inter text-sage">System-wide business intelligence overview</p>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
        <!-- Summary Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-stat-card title="Inventory Value" :value="'₱' . number_format($totalInventoryValue ?? 0, 2)" icon="📦" border="terracotta" />
            <x-stat-card title="Monthly Sales" :value="'₱' . number_format($monthlySales ?? 0, 2)" icon="💰" border="sage" />
            <x-stat-card title="Total Customers" :value="$totalCustomers ?? 0" icon="👥" border="sienna" />
            <x-stat-card title="Total Employees" :value="$totalEmployees ?? 0" icon="👥" border="tan" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Top Customers -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-sage">
                <h3 class="text-xl font-lora font-semibold text-sienna mb-4 flex items-center">
                    <span class="mr-2">🏆</span> Top Customers
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                                <th class="px-4 py-3 text-left">Customer</th>
                                <th class="px-4 py-3 text-center">Orders</th>
                                <th class="px-4 py-3 text-right">Total Spent</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($topCustomers ?? [] as $customer)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-4 font-semibold text-sienna">{{ $customer->name }}</td>
                                    <td class="px-4 py-4 text-center text-gray-600">{{ $customer->order_count }}</td>
                                    <td class="px-4 py-4 text-right font-bold text-terracotta">₱{{ number_format($customer->total_spent, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-500">No customers yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-terracotta">
                <h3 class="text-xl font-lora font-semibold text-sienna mb-4 flex items-center">
                    <span class="mr-2">📜</span> Recent System Orders
                </h3>
                <div class="space-y-4 max-h-96 overflow-y-auto">
                    @forelse($recentOrders ?? [] as $order)
                        <div class="flex items-center justify-between p-3 bg-cream bg-opacity-30 rounded-lg hover:bg-opacity-50 transition border border-tan border-opacity-20">
                            <div>
                                <p class="font-semibold text-sienna">#{{ $order->id }} — {{ $order->customer_name }}</p>
                                <p class="text-xs text-sage">{{ \Carbon\Carbon::parse($order->order_date)->format('M d, Y h:i A') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-terracotta">₱{{ number_format($order->total, 2) }}</p>
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-sage bg-opacity-20 text-sage uppercase font-bold tracking-tighter">
                                    {{ $order->order_status ?? 'Completed' }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-500">No recent orders</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- NEW: Top Selling Products & Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Top Selling Products -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-sienna">
                <h3 class="text-xl font-lora font-semibold text-sienna mb-4 flex items-center">
                    <span class="mr-2">🔥</span> Top Selling Products
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 bg-gray-50 text-gray-600 uppercase text-xs tracking-wider">
                                <th class="px-4 py-3 text-left">Product</th>
                                <th class="px-4 py-3 text-center">Units Sold</th>
                                <th class="px-4 py-3 text-right">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($topProducts ?? [] as $product)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-4 font-semibold text-sienna max-w-48 truncate">{{ $product->name }}</td>
                                    <td class="px-4 py-4 text-center text-gray-600 font-medium">{{ $product->quantity_sold }}</td>
                                    <td class="px-4 py-4 text-right font-bold text-terracotta">₱{{ number_format($product->revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-8 text-center text-gray-500">No products sold yet</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Sales Charts -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-tan space-y-6">
                <h3 class="text-xl font-lora font-semibold text-sienna flex items-center">
                    <span class="mr-2">📊</span> Sales Analytics
                </h3>
                
                <!-- Bar Chart - Monthly Sales -->
                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                    <p class="text-sm font-medium text-sage mb-3">Monthly Sales Trend</p>
                    <div class="h-48 w-full">
                        <canvas id="salesBarChart"></canvas>
                    </div>
                </div>

                <!-- Pie Chart - Sales by Category -->
                <div class="border border-gray-200 rounded-lg p-4 bg-cream bg-opacity-50">
                    <p class="text-sm font-medium text-sage mb-3">Revenue by Category</p>
                    <div class="h-40 w-full">
                        <canvas id="categoryPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Bar Chart - Monthly Sales
            const salesCtx = document.getElementById('salesBarChart').getContext('2d');
            new Chart(salesCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($monthlySalesLabels ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']) !!},
                    datasets: [{
                        label: 'Sales (₱)',
                        data: {!! json_encode($monthlySalesData ?? [12000, 19000, 15000, 25000, 22000, 30000]) !!},
                        backgroundColor: ['#D4A574', '#A8B5A2', '#E8B4B8', '#F4E4BC', '#C7A587', '#B7C3B8'],
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } }
                    }
                }
            });

            // Pie Chart - Category Sales
            const pieCtx = document.getElementById('categoryPieChart').getContext('2d');
            new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($categoryLabels ?? ['Electronics', 'Clothing', 'Books', 'Home', 'Others']) !!},
                    datasets: [{
                        data: {!! json_encode($categorySalesData ?? [30000, 15000, 10000, 20000, 5000]) !!},
                        backgroundColor: ['#D4A574', '#A8B5A2', '#E8B4B8', '#F4E4BC', '#C7A587'],
                        borderWidth: 0,
                        cutout: '60%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } } }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
