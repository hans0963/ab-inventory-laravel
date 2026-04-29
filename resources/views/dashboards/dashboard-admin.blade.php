<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-md shadow-sm border-b border-sienna">
            <h2 class="font-pacifico text-3xl text-sienna leading-tight">
                {{ __('Admin Dashboard') }}
            </h2>
            <p class="font-inter text-sage">Comprehensive overview of your bakeshop</p>
        </div>
    </x-slot>    

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <x-stat-card title="Total Sales" :value="$totalSales" icon="📊" border="terracotta" />
            <x-stat-card title="Total Revenue" :value="number_format($totalRevenue, 2)" icon="💰" border="sage" />
            <x-stat-card title="Employees" :value="$totalEmployees" icon="👥" border="cream" />
            <x-stat-card title="Products" :value="$totalProducts" icon="📦" border="sienna" />
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
        <x-alert-table :alerts="$lowStockAlerts" />
    </div>
</x-app-layout>
