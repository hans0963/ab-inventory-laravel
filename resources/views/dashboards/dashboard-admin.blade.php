<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ __('Admin Dashboard') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Comprehensive overview of your bakeshop performance</p>
            </div>
        </div>
    </x-slot>    

    <div class="space-y-10">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <a href="{{ route('inventory.sales') }}" class="block transform hover:scale-105 transition">
                <x-stat-card title="Today's Sales" :value="'₱' . number_format($todaySales, 2)" icon="💰" border="sage" />
            </a>
            <a href="{{ route('inventory.sales') }}" class="block transform hover:scale-105 transition">
                <x-stat-card title="Monthly Sales" :value="'₱' . number_format($monthlySales, 2)" icon="📅" border="terracotta" />
            </a>
            <a href="{{ route('inventory.sales') }}" class="block transform hover:scale-105 transition">
                <x-stat-card title="Yearly Sales" :value="'₱' . number_format($yearlySales, 2)" icon="📈" border="sienna" />
            </a>
            <a href="{{ route('reports.inventory') }}" class="block transform hover:scale-105 transition">
                <x-stat-card title="Total Revenue" :value="'₱' . number_format($totalRevenue, 2)" icon="🏛️" border="cream" />
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
                <div class="mt-4 space-y-2">
                    @foreach($salesByCategory as $category)
                        <div class="flex justify-between text-[10px] uppercase tracking-widest font-bold">
                            <span class="text-sienna">{{ $category->category_name }}</span>
                            <span class="text-sage">₱{{ number_format($category->revenue, 2) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Sales Orders -->
            <div class="lg:col-span-2">
                <div class="card-rustic border-terracotta h-full">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-sienna">Recent Sales</h2>
                        <span class="bg-terracotta bg-opacity-10 text-terracotta px-3 py-1 rounded-full text-xs font-bold uppercase">Live Updates</span>
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

        <!-- Low Stock Alerts -->
        <div class="card-rustic border-sienna">
            <h2 class="text-xl font-bold text-sienna mb-6">Inventory Alerts (Low Stock)</h2>
            <div class="overflow-x-auto">
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
                                <td colspan="4" class="px-6 py-8 text-center text-sage italic">No inventory alerts.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
