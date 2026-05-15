<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-md shadow-sm border-b border-sienna">
            <div>
                <h2 class="font-formal text-3xl text-sienna leading-tight">
                    {{ __('Manager Sales Report') }}
                </h2>
                <p class="font-inter text-sage">Detailed sales analytics and performance metrics</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('inventory.sales', ['period' => 'today']) }}" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest transition {{ $period === 'today' ? 'bg-sienna text-cream' : 'bg-white text-sienna border border-sienna hover:bg-cream' }}">Today</a>
                <a href="{{ route('inventory.sales', ['period' => 'month']) }}" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest transition {{ $period === 'month' ? 'bg-sienna text-cream' : 'bg-white text-sienna border border-sienna hover:bg-cream' }}">This Month</a>
                <a href="{{ route('inventory.sales', ['period' => 'year']) }}" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest transition {{ $period === 'year' ? 'bg-sienna text-cream' : 'bg-white text-sienna border border-sienna hover:bg-cream' }}">This Year</a>
                <a href="{{ route('inventory.sales', ['period' => 'all']) }}" class="px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest transition {{ $period === 'all' ? 'bg-sienna text-cream' : 'bg-white text-sienna border border-sienna hover:bg-cream' }}">All Time</a>
            </div>
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
            <div class="card-rustic border-sienna overflow-hidden p-0">
                <div class="p-6 border-b border-sienna border-opacity-10">
                    <h3 class="text-xl font-lora font-bold text-sienna flex items-center">
                        <span class="mr-2">📈</span> Top Products by Revenue
                    </h3>
                </div>
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-sienna text-cream uppercase text-[10px] tracking-widest font-black">
                            <th class="px-6 py-4 text-left">Product</th>
                            <th class="px-6 py-4 text-center">Sold</th>
                            <th class="px-6 py-4 text-right">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                        @forelse($topProducts ?? [] as $product)
                            <tr class="hover:bg-cream hover:bg-opacity-20 transition-colors">
                                <td class="px-6 py-4 font-bold text-sienna">{{ $product->name }}</td>
                                <td class="px-6 py-4 text-center font-black text-sage">{{ number_format($product->units_sold) }}</td>
                                <td class="px-6 py-4 text-right font-black text-terracotta">₱{{ number_format($product->revenue, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-sage italic">No data available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Performance Overview -->
            <div class="card-rustic border-sage">
                <h3 class="text-xl font-lora font-bold text-sienna mb-6 border-b border-sienna border-opacity-10 pb-2 flex items-center">
                    <span class="mr-2">📊</span> Product Performance
                </h3>
                <div class="space-y-6 pt-4">
                    @forelse($topProducts ?? [] as $product)
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-[10px] font-black text-sienna uppercase tracking-widest">{{ $product->name }}</span>
                                <span class="text-[10px] font-black text-sage">{{ round($product->performance) }}%</span>
                            </div>
                            <div class="w-full bg-cream bg-opacity-50 h-2 rounded-full border border-sienna border-opacity-10 overflow-hidden shadow-inner">
                                <div class="bg-sage h-full rounded-full transition-all duration-1000 shadow-md" style="width: {{ $product->performance }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-sage italic py-12">No performance data available.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="card-rustic border-sienna lg:col-span-2">
                <h3 class="text-xl font-lora font-bold text-sienna mb-6 border-b border-sienna border-opacity-10 pb-2">
                    @if($period === 'today') Sales Performance (Today)
                    @elseif($period === 'month') Sales Performance (This Month)
                    @elseif($period === 'year') Sales Performance (This Year)
                    @else Sales Performance (Last 7 Days)
                    @endif
                </h3>
                <div class="h-80">
                    <canvas id="dailySalesChart"></canvas>
                </div>
            </div>
            <div class="card-rustic border-sage lg:col-span-1">
                <h3 class="text-xl font-lora font-bold text-sienna mb-6 border-b border-sienna border-opacity-10 pb-2">Sales by Category</h3>
                <div class="h-80">
                    <canvas id="salesByCategoryChart"></canvas>
                </div>
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
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [{
                        label: 'Sales (₱)',
                        data: {!! json_encode($chartValues) !!},
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
