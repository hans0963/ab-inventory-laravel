<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ __('Business Intelligence') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Comprehensive bakeshop performance overview</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-10">
        <!-- Summary Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <x-stat-card title="Today's Sales" :value="'₱' . number_format($todaySales ?? 0, 2)" icon="💰" border="sage" />
            <x-stat-card title="Monthly Sales" :value="'₱' . number_format($monthlySales ?? 0, 2)" icon="📅" border="terracotta" />
            <x-stat-card title="Yearly Sales" :value="'₱' . number_format($yearlySales ?? 0, 2)" icon="📈" border="sienna" />
            <x-stat-card title="Inventory Value" :value="'₱' . number_format($totalInventoryValue ?? 0, 2)" icon="📦" border="cream" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Fast Moving Products -->
            <div class="card-rustic border-sage">
                <h3 class="text-xl font-lora font-bold text-sienna mb-6 flex items-center">
                    <span class="mr-2 text-2xl">🚀</span> Fast Moving Products
                </h3>
                <div class="overflow-hidden rounded-xl border border-sienna border-opacity-10">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-sienna text-cream uppercase text-[10px] tracking-widest font-black">
                                <th class="px-6 py-4 text-left">Product</th>
                                <th class="px-6 py-4 text-center">Units Sold</th>
                                <th class="px-6 py-4 text-right">Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                            @forelse($fastMoving ?? [] as $product)
                                <tr class="hover:bg-cream hover:bg-opacity-20 transition">
                                    <td class="px-6 py-4 font-bold text-sienna">{{ $product->product_name }}</td>
                                    <td class="px-6 py-4 text-center text-sage font-black">{{ $product->total_sold }}</td>
                                    <td class="px-6 py-4 text-right font-black text-terracotta">₱{{ number_format($product->revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-sage italic">No data available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Slow Moving Products -->
            <div class="card-rustic border-terracotta">
                <h3 class="text-xl font-lora font-bold text-sienna mb-6 flex items-center">
                    <span class="mr-2 text-2xl">🐌</span> Slow Moving Products
                </h3>
                <div class="overflow-hidden rounded-xl border border-sienna border-opacity-10">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-sienna text-cream uppercase text-[10px] tracking-widest font-black">
                                <th class="px-6 py-4 text-left">Product</th>
                                <th class="px-6 py-4 text-center">Units Sold</th>
                                <th class="px-6 py-4 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                            @forelse($slowMoving ?? [] as $product)
                                <tr class="hover:bg-cream hover:bg-opacity-20 transition">
                                    <td class="px-6 py-4 font-bold text-sienna">{{ $product->product_name }}</td>
                                    <td class="px-6 py-4 text-center text-sage font-black">{{ (int)$product->total_sold }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-[9px] px-2 py-0.5 rounded-full bg-orange-100 text-orange-600 uppercase font-black tracking-widest">
                                            Low Velocity
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-8 text-center text-sage italic">No data available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Top Customers -->
            <div class="card-rustic border-sienna">
                <h3 class="text-xl font-lora font-bold text-sienna mb-6 flex items-center">
                    <span class="mr-2 text-2xl">🏆</span> Top Customers
                </h3>
                <div class="overflow-hidden rounded-xl border border-sienna border-opacity-10">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-sienna text-cream uppercase text-[10px] tracking-widest font-black">
                                <th class="px-6 py-4 text-left">Customer</th>
                                <th class="px-6 py-4 text-center">Orders</th>
                                <th class="px-6 py-4 text-right">Total Spent</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                            @forelse($topCustomers ?? [] as $customer)
                                <tr class="hover:bg-cream hover:bg-opacity-20 transition">
                                    <td class="px-6 py-4 font-bold text-sienna">{{ $customer->name }}</td>
                                    <td class="px-6 py-4 text-center text-sage font-black">{{ $customer->order_count }}</td>
                                    <td class="px-6 py-4 text-right font-black text-terracotta">₱{{ number_format($customer->total_spent, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-sage italic">No customer data available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="card-rustic border-cream">
                <h3 class="text-xl font-lora font-bold text-sienna mb-6 flex items-center">
                    <span class="mr-2 text-2xl">📜</span> Recent Transactions
                </h3>
                <div class="space-y-4 max-h-[28rem] overflow-y-auto pr-2 custom-scrollbar">
                    @forelse($recentOrders ?? [] as $order)
                        <div class="flex items-center justify-between p-4 bg-cream bg-opacity-30 rounded-xl hover:bg-opacity-60 transition border border-sienna border-opacity-10">
                            <div>
                                <p class="font-bold text-sienna">{{ $order->product_name }} — {{ $order->customer_name }}</p>
                                <p class="text-xs text-sage font-medium">{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y h:i A') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-black text-terracotta text-lg">₱{{ number_format($order->total_amount, 2) }}</p>
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-sage bg-opacity-20 text-sage-dark uppercase font-black tracking-widest">
                                    {{ $order->payment_type }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 text-sage italic">No recent transactions processed.</div>
                    @endforelse
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
