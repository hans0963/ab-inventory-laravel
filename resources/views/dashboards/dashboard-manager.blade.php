<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-md shadow-sm border-b border-sienna">
            <h2 class="font-pacifico text-3xl text-sienna leading-tight">
                {{ __('Bakeshop Dashboard') }}
            </h2>
            <p class="font-inter text-sage">Overview of your daily operations</p>
        </div>
    </x-slot>    

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Key Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-6 mb-8">
            <!-- Today's Sales -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-terracotta">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-inter text-gray-600 text-sm uppercase tracking-wide">Today's Sales</p>
                        <p class="font-lora font-semibold text-2xl text-sienna mt-1">₱{{ number_format($todaySales ?? 12450, 0) }}</p>
                    </div>
                    <div class="text-4xl text-terracotta opacity-20">💰</div>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-sage">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-inter text-gray-600 text-sm uppercase tracking-wide">Total Orders</p>
                        <p class="font-lora font-semibold text-2xl text-sienna mt-1">{{ $totalOrders ?? 48 }}</p>
                    </div>
                    <div class="text-4xl text-sage opacity-20">📦</div>
                </div>
            </div>

            <!-- Products -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-sienna">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-inter text-gray-600 text-sm uppercase tracking-wide">Products</p>
                        <p class="font-lora font-semibold text-2xl text-sienna mt-1">{{ $totalProducts ?? 127 }}</p>
                    </div>
                    <div class="text-4xl text-sienna opacity-20">🍞</div>
                </div>
            </div>

            <!-- Customers -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-tan">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-inter text-gray-600 text-sm uppercase tracking-wide">Customers</p>
                        <p class="font-lora font-semibold text-2xl text-sienna mt-1">{{ $totalCustomers ?? 342 }}</p>
                    </div>
                    <div class="text-4xl text-tan opacity-20">👥</div>
                </div>
            </div>
        </div>

        <!-- Recent Sales Orders -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-terracotta">
                <h2 class="text-lg font-lora font-semibold mb-4 text-sienna">Recent Sales Orders</h2>
                <div class="divide-y divide-sienna">
                    @forelse($recentOrders ?? [] as $order)
                        <div class="flex justify-between items-center py-3 hover:bg-cream transition">
                            <div>
                                <p class="font-semibold text-sienna">{{ $order->order_number }} — {{ $order->customer->name }}</p>
                                <p class="text-sm text-gray-600">{{ $order->items_summary }}</p>
                                <p class="text-xs text-sage">{{ $order->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="font-semibold text-terracotta">₱{{ number_format($order->total_amount, 0) }}</div>
                        </div>
                    @empty
                        <div class="flex justify-between items-center py-3">
                            <div>
                                <p class="font-semibold text-sienna">SO-001 — Maria Santos</p>
                                <p class="text-sm text-gray-600">Pandesal (20pcs), Ensaymada (5pcs)</p>
                                <p class="text-xs text-sage">10 mins ago</p>
                            </div>
                            <div class="font-semibold text-terracotta">₱245</div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <div>
            @if($lowStockAlerts->isNotEmpty())
                <div class="bg-terracotta bg-opacity-20 border-l-4 border-terracotta text-sienna p-6 rounded-lg shadow-md">
                    <h2 class="text-lg font-lora font-semibold mb-4 text-sienna">Low Stock Alerts</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-sienna rounded-lg overflow-hidden">
                            <thead>
                                <tr class="bg-sienna text-cream">
                                    <th class="px-4 py-2 text-left">Product</th>
                                    <th class="px-4 py-2 text-left">Category</th>
                                    <th class="px-4 py-2 text-left">Current Stock</th>
                                    <th class="px-4 py-2 text-left">Threshold</th>
                                    <th class="px-4 py-2 text-left">Deficit</th>
                                </tr>
                            </thead>
                            <tbody class="bg-cream divide-y divide-sienna">
                                @foreach($lowStockAlerts as $alert)
                                    <tr class="hover:bg-sage hover:bg-opacity-20 transition">
                                        <td class="px-4 py-3 font-semibold text-sienna">{{ $alert->product_name }}</td>
                                        <td class="px-4 py-3 text-sienna">{{ $alert->category_name }}</td>
                                        <td class="px-4 py-3 font-semibold text-terracotta">{{ $alert->current_stock }}</td>
                                        <td class="px-4 py-3 font-semibold text-sage">{{ $alert->stock_alert_threshold }}</td>
                                        <td class="px-4 py-3 font-semibold text-sienna">{{ $alert->stock_deficit }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="flex flex-col items-center justify-center bg-cream border border-sienna rounded-lg p-8 shadow-md">
                    <img src="/images/empty-bread.png" alt="Empty state bread illustration" class="w-32 h-32 mb-4">
                    <p class="font-lora font-semibold text-sienna text-lg">All stocked up — no alerts today!</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
