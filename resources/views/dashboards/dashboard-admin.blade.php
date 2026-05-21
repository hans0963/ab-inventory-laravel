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
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="font-formal text-4xl text-sienna">Admin Dashboard</h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Comprehensive overview of your bakeshop performance</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('sales.create') }}" class="inline-flex items-center justify-center rounded-full bg-sienna px-5 py-3 text-xs font-bold uppercase tracking-widest text-cream hover:bg-opacity-90 transition">
                    New Sale
                </a>
                <a href="{{ route('products.create') }}" class="inline-flex items-center justify-center rounded-full bg-terracotta px-5 py-3 text-xs font-bold uppercase tracking-widest text-cream hover:bg-opacity-90 transition">
                    Add Product
                </a>
                <a href="{{ route('purchases.create') }}" class="inline-flex items-center justify-center rounded-full bg-sage px-5 py-3 text-xs font-bold uppercase tracking-widest text-cream hover:bg-opacity-90 transition">
                    Create Purchase Order
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-6">
            <x-stat-card title="Sales Today" :value="'₱' . number_format($todaySales, 2)" icon="💰" border="sage" />
            <x-stat-card title="Orders Today" :value="$totalOrdersToday" icon="🧾" border="terracotta" />
            <x-stat-card title="Total Products" :value="$totalProducts" icon="📦" border="sienna" />
            <x-stat-card title="Low Stock Items" :value="$lowStockItemCount" icon="⚠️" border="cream" />
            <x-stat-card title="Total Customers" :value="$totalCustomers" icon="👥" border="sage" />
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="card-rustic border-sage p-6">
                <h2 class="text-xl font-bold text-sienna mb-4">Daily Sales (Last 7 Days)</h2>
                <div class="relative h-72">
                    <canvas id="dailySalesChart"></canvas>
                </div>
            </div>

            <div class="card-rustic border-terracotta p-6">
                <h2 class="text-xl font-bold text-sienna mb-4">Sales by Category</h2>
                <div class="relative h-72">
                    <canvas id="salesByCategoryChart"></canvas>
                </div>
            </div>

            <div class="card-rustic border-sienna p-6">
                <h2 class="text-xl font-bold text-sienna mb-4">Revenue Trend This Month</h2>
                <div class="relative h-72">
                    <canvas id="revenueTrendChart"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="card-rustic border-sage lg:col-span-2 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-sienna">Top Selling Products</h2>
                    <span class="text-xs uppercase tracking-widest text-sage font-black">Last 30 days</span>
                </div>
                <div class="space-y-4">
                    @forelse($fastMovingProducts as $product)
                        <div class="grid grid-cols-[1fr_auto] gap-4 items-center bg-cream bg-opacity-20 rounded-xl p-4">
                            <div>
                                <p class="font-semibold text-sienna">{{ $product->product_name }}</p>
                                <p class="text-xs uppercase tracking-widest text-sage">Best seller</p>
                            </div>
                            <span class="rounded-full bg-sienna px-3 py-2 text-cream text-sm font-black">{{ $product->total_sold }}</span>
                        </div>
                    @empty
                        <p class="text-sage italic">No sales data available.</p>
                    @endforelse
                </div>
            </div>

            <div class="card-rustic border-terracotta p-6">
                <h2 class="text-xl font-bold text-sienna mb-6">Recent Activity</h2>
                <div class="space-y-6">
                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-widest text-sienna mb-3">Latest Transactions</h3>
                        <div class="space-y-3">
                            @forelse($recentOrders as $sale)
                                <div class="rounded-xl border border-sienna/10 bg-cream p-3">
                                    <p class="font-medium text-sienna">{{ $sale->product->product_name }} — {{ $sale->customer->name ?? 'Walk-in' }}</p>
                                    <p class="text-xs text-sage">₱{{ number_format($sale->total_amount, 2) }} • {{ $sale->sold }} unit(s)</p>
                                    <p class="text-[11px] uppercase tracking-widest text-sienna/80">{{ $sale->created_at->diffForHumans() }}</p>
                                </div>
                            @empty
                                <p class="text-sage italic">No recent transactions.</p>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold uppercase tracking-widest text-sienna mb-3">Recent Stock Updates</h3>
                        <div class="space-y-3">
                            @forelse($recentStockUpdates as $movement)
                                <div class="rounded-xl border border-terracotta/10 bg-cream p-3">
                                    <p class="font-medium text-sienna">{{ $movement->product->product_name ?? 'Inventory item' }}</p>
                                    <p class="text-xs text-sage">{{ ucfirst($movement->transaction_type ?? 'update') }} • {{ $movement->created_at->format('M d, Y') }}</p>
                                    <p class="text-[11px] uppercase tracking-widest text-sienna/80">Balance: {{ $movement->new_balance ?? $movement->total_inventory ?? '—' }}</p>
                                </div>
                            @empty
                                <p class="text-sage italic">No recent stock updates.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dailyCtx = document.getElementById('dailySalesChart').getContext('2d');
            new Chart(dailyCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($dailySalesLabels) !!},
                    datasets: [{
                        label: 'Sales',
                        data: {!! json_encode($dailySalesData) !!},
                        backgroundColor: '#E2725B',
                        borderRadius: 8,
                        maxBarThickness: 24
                    }]
                },
                options: {
                    plugins: { legend: { display: false } },
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { ticks: { callback: function(value) { return '₱' + value.toLocaleString(); } } }
                    }
                }
            });

            const categoryCtx = document.getElementById('salesByCategoryChart').getContext('2d');
            new Chart(categoryCtx, {
                type: 'pie',
                data: {
                    labels: {!! json_encode($salesByCategory->pluck('category_name')) !!},
                    datasets: [{
                        data: {!! json_encode($salesByCategory->pluck('revenue')) !!},
                        backgroundColor: ['#E2725B', '#8A9A5B', '#4B3621', '#F5F5DC', '#D2B48C', '#BC8F8F', '#A0522D', '#556B2F'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } }
                }
            });

            const trendCtx = document.getElementById('revenueTrendChart').getContext('2d');
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($revenueTrendLabels) !!},
                    datasets: [{
                        label: 'Revenue',
                        data: {!! json_encode($revenueTrendData) !!},
                        fill: true,
                        backgroundColor: 'rgba(226, 114, 91, 0.18)',
                        borderColor: '#E2725B',
                        tension: 0.35,
                        pointRadius: 4,
                        pointBackgroundColor: '#E2725B'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { ticks: { callback: function(value) { return '₱' + value.toLocaleString(); } } }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
