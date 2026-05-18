<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-md shadow-sm border-b border-sienna">
            <div>
                <h2 class="font-formal text-3xl text-sienna leading-tight">
                    {{ __('Sales Report') }}
                </h2>
                <p class="font-inter text-sage">Comprehensive sales analytics and insights</p>
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
            <x-stat-card title="Total Sales" :value="number_format($totalSales ?? 0, 2)" icon="📊" border="terracotta" />
            <x-stat-card title="Average Order" :value="number_format($averageOrder ?? 0, 2)" icon="🧾" border="sage" />
            <x-stat-card title="Total Orders" :value="$totalOrders ?? 0" icon="📦" border="sienna" />
            <x-stat-card title="SC/PWD Discount" :value="number_format($discounts ?? 0, 2)" icon="🎟️" border="tan" />
        </div>

        <!-- Daily Sales Chart -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-terracotta">
            <h2 class="text-lg font-lora font-semibold mb-4 text-sienna">
                @if($period === 'today') Sales Performance (Today)
                @elseif($period === 'month') Sales Performance (This Month)
                @elseif($period === 'year') Sales Performance (This Year)
                @else Sales Performance (Last 7 Days)
                @endif
            </h2>
            <div class="h-80 flex items-center justify-center">
                @if(count($chartLabels) > 0)
                    <canvas id="dailySalesChart" class="w-full"></canvas>
                @else
                    <div class="text-center">
                        <p class="text-sage italic">No sales data recorded for this period.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sales by Category Chart -->
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-sage">
            <h2 class="text-lg font-lora font-semibold mb-4 text-sienna">Sales by Category</h2>
            <div class="flex justify-center h-80 items-center">
                @if(count($categoryLabels) > 0)
                    <div class="w-full max-w-[300px]">
                        <canvas id="salesByCategoryChart"></canvas>
                    </div>
                @else
                    <div class="text-center">
                        <p class="text-sage italic">No category data for this period.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="card-rustic border-sienna overflow-hidden p-0">
            <div class="p-6 border-b border-sienna border-opacity-10">
                <h2 class="text-xl font-lora font-bold text-sienna">Top Selling Products</h2>
            </div>
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-sienna text-cream uppercase text-[10px] tracking-widest font-black">
                        <th class="px-6 py-4 text-left">Product</th>
                        <th class="px-6 py-4 text-center">Units Sold</th>
                        <th class="px-6 py-4 text-right">Revenue</th>
                        <th class="px-6 py-4 text-left">Performance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                    @forelse($topProducts ?? [] as $product)
                        <tr class="hover:bg-cream hover:bg-opacity-20 transition-colors">
                            <td class="px-6 py-4 font-bold text-sienna">{{ $product->name }}</td>
                            <td class="px-6 py-4 text-center font-black text-sage">{{ $product->units_sold }}</td>
                            <td class="px-6 py-4 text-right font-black text-terracotta">₱{{ number_format($product->revenue, 2) }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex-1 bg-cream bg-opacity-50 h-2 rounded-full overflow-hidden border border-sienna border-opacity-10">
                                        <div class="bg-terracotta h-full rounded-full" style="width: {{ $product->performance }}%"></div>
                                    </div>
                                    <span class="text-[10px] font-black text-sienna">{{ round($product->performance) }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-sage italic">No product data available for this period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Daily Sales Chart
            const dailySalesCanvas = document.getElementById('dailySalesChart');
            if (dailySalesCanvas) {
                const dailySalesCtx = dailySalesCanvas.getContext('2d');
                new Chart(dailySalesCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($chartLabels) !!},
                        datasets: [{
                            label: 'Sales (₱)',
                            data: {!! json_encode($chartValues) !!},
                            borderColor: '#E2725B', // terracotta
                            backgroundColor: 'rgba(226, 114, 91, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });
            }

            // Sales by Category Chart
            const categoryCanvas = document.getElementById('salesByCategoryChart');
            if (categoryCanvas) {
                const categoryCtx = categoryCanvas.getContext('2d');
                new Chart(categoryCtx, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($categoryLabels) !!},
                        datasets: [{
                            data: {!! json_encode($categorySalesData) !!},
                            backgroundColor: [
                                '#E2725B', // terracotta
                                '#8A9A5B', // sage
                                '#A0522D', // sienna
                                '#D2B48C', // tan
                                '#F5F5DC'  // beige
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
