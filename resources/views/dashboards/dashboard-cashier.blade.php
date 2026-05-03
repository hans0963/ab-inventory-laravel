<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-pacifico text-4xl text-sienna">
                    {{ __('Sales Terminal') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Register and track bakeshop sales</p>
            </div>
        </div>
    </x-slot>    

    <div class="space-y-10">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-8">
            <x-stat-card title="Today's Sales" :value="'₱' . number_format($todaysSales, 2)" icon="💰" border="terracotta" />
            <x-stat-card title="Transactions Today" :value="$transactionsCount" icon="🧾" border="sage" />
        </div>

        <!-- Recent Sales Orders -->
        <div class="card-rustic border-terracotta">
            <h2 class="text-xl font-bold text-sienna mb-6">Recent Sales Orders</h2>
            <div class="divide-y divide-sienna divide-opacity-10">
                @forelse($recentOrders ?? [] as $order)
                    <div class="flex justify-between items-center py-4 hover:bg-cream hover:bg-opacity-50 transition rounded-lg px-2 -mx-2">
                        <div>
                            <p class="font-bold text-sienna">#{{ $order->id }} — {{ $order->customer_name ?? 'Walk-in' }}</p>
                            <p class="text-sm text-sage font-medium">{{ $order->total_products }} item(s)</p>
                            <p class="text-xs text-sienna opacity-60">{{ \Carbon\Carbon::parse($order->order_date)->diffForHumans() }}</p>
                        </div>
                        <div class="text-xl font-bold text-terracotta">₱{{ number_format($order->total, 2) }}</div>
                    </div>
                @empty
                    <div class="py-10 text-center">
                        <p class="text-sage italic">No recent sales processed today.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="card-rustic border-sienna">
            <h2 class="text-xl font-bold text-sienna mb-6">Inventory Status Alerts</h2>
            <x-alert-table :alerts="$lowStockAlerts" />
        </div>
    </div>
</x-app-layout>
