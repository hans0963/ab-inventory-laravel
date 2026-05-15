<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ __('Operations Dashboard') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Manage your bakeshop's daily production and inventory</p>
            </div>
        </div>
    </x-slot>    

    <div class="space-y-10">
        <!-- Key Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <a href="{{ route('inventory.sales') }}" class="block transform hover:scale-105 transition">
                <x-stat-card title="Today's Sales" :value="'₱' . number_format($todaySales, 2)" icon="💰" border="sage" />
            </a>
            <a href="{{ route('inventory.sales') }}" class="block transform hover:scale-105 transition">
                <x-stat-card title="Monthly Sales" :value="'₱' . number_format($monthlySales, 2)" icon="📅" border="terracotta" />
            </a>
            <a href="{{ route('sales.index') }}" class="block transform hover:scale-105 transition">
                <x-stat-card title="Total Orders" :value="$totalOrders" icon="📦" border="sienna" />
            </a>
            <a href="{{ route('customers.index') }}" class="block transform hover:scale-105 transition">
                <x-stat-card title="Customers" :value="$totalCustomers" icon="👥" border="cream" />
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Fast Moving Products -->
            <a href="{{ route('reports.index') }}" class="card-rustic border-sage hover:bg-cream hover:bg-opacity-20 transition">
                <h2 class="text-xl font-bold text-sienna mb-6 flex items-center">
                    <span class="mr-2 text-2xl">🚀</span> Fast Moving Products
                </h2>
                <div class="space-y-4">
                    @forelse($fastMovingProducts as $product)
                        <div class="flex items-center justify-between p-3 bg-cream bg-opacity-30 rounded-lg">
                            <span class="font-medium text-sienna">{{ $product->product_name }}</span>
                            <span class="bg-sage text-white px-2 py-1 rounded text-xs font-bold">{{ $product->total_sold }} sold</span>
                        </div>
                    @empty
                        <p class="text-sage italic text-center py-4">No data available</p>
                    @endforelse
                </div>
            </a>

            <!-- Slow Moving Products -->
            <a href="{{ route('reports.index') }}" class="card-rustic border-terracotta hover:bg-cream hover:bg-opacity-20 transition">
                <h2 class="text-xl font-bold text-sienna mb-6 flex items-center">
                    <span class="mr-2 text-2xl">🐌</span> Slow Moving Products
                </h2>
                <div class="space-y-4">
                    @forelse($slowMovingProducts as $product)
                        <div class="flex items-center justify-between p-3 bg-cream bg-opacity-30 rounded-lg">
                            <span class="font-medium text-sienna">{{ $product->product_name }}</span>
                            <span class="bg-terracotta text-white px-2 py-1 rounded text-xs font-bold">{{ (int)$product->total_sold }} sold</span>
                        </div>
                    @empty
                        <p class="text-sage italic text-center py-4">No data available</p>
                    @endforelse
                </div>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Sales by Category (Pie Chart) -->
            <div class="card-rustic border-sienna lg:col-span-1">
                <h2 class="text-xl font-bold text-sienna mb-6">Sales by Category</h2>
                <div class="relative h-64">
                    <canvas id="salesByCategoryChart"></canvas>
                </div>
            </div>

            <!-- Recent Sales -->
            <div class="lg:col-span-2">
                <div class="card-rustic border-terracotta h-full">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-sienna">Recent Sales</h2>
                    </div>
                    <div class="divide-y divide-sienna divide-opacity-10">
                        @forelse($recentOrders ?? [] as $sale)
                            <div class="flex justify-between items-center py-4 hover:bg-cream hover:bg-opacity-50 transition rounded-lg px-2 -mx-2">
                                <div>
                                    <p class="font-bold text-sienna">{{ $sale->product->product_name }} — {{ $sale->customer->name ?? 'Walk-in' }}</p>
                                    <p class="text-sm text-sage font-medium">{{ $sale->sold }} unit(s) @ ₱{{ number_format($sale->total_amount, 2) }}</p>
                                    <p class="text-xs text-sienna opacity-60">{{ $sale->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="text-xl font-bold text-terracotta">₱{{ number_format($sale->total_amount, 2) }}</div>
                            </div>
                        @empty
                            <div class="py-10 text-center">
                                <p class="text-sage italic">No recent sales found.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="card-rustic border-sienna">
            <h2 class="text-xl font-bold text-sienna mb-6 font-lora flex items-center">
                <span class="mr-2 text-2xl">⚠️</span> Critical Inventory Alerts
            </h2>
            <div class="overflow-x-auto rounded-xl border border-sienna border-opacity-10">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-sienna text-cream uppercase text-[10px] tracking-widest font-black">
                            <th class="px-6 py-4 text-left">Product</th>
                            <th class="px-6 py-4 text-center">Current Qty</th>
                            <th class="px-6 py-4 text-center">Threshold</th>
                            <th class="px-6 py-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                        @forelse($lowStockAlerts as $alert)
                            <tr>
                                <td class="px-6 py-4 font-bold text-sienna">{{ $alert->product_name }}</td>
                                <td class="px-6 py-4 text-center font-black text-terracotta">{{ $alert->quantity }}</td>
                                <td class="px-6 py-4 text-center text-sage">{{ $alert->stock_alert_threshold }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-red-100 text-red-600 uppercase font-black tracking-widest">
                                        Low Stock
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sage italic">All inventory levels are healthy.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('salesByCategoryChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($salesByCategory->pluck('category_name')) !!},
                    datasets: [{
                        data: {!! json_encode($salesByCategory->pluck('revenue')) !!},
                        backgroundColor: [
                            '#E2725B', // terracotta
                            '#8A9A5B', // sage
                            '#4B3621', // sienna
                            '#F5F5DC', // beige/cream
                            '#D2B48C', // tan
                            '#BC8F8F', // rosy brown
                            '#A0522D', // sienna dark
                            '#556B2F'  // olive dark
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
