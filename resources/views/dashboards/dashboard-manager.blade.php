<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-pacifico text-4xl text-sienna">
                    {{ __('Operations Dashboard') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Manage your bakeshop's daily production</p>
            </div>
        </div>
    </x-slot>    

    <div class="space-y-10">
        <!-- Key Metrics -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <x-stat-card title="Today's Sales" :value="'₱' . number_format($todaySales ?? 12450, 0)" icon="💰" border="terracotta" />
            <x-stat-card title="Total Orders" :value="$totalOrders ?? 48" icon="📦" border="sage" />
            <x-stat-card title="Products" :value="$totalProducts ?? 127" icon="🍞" border="sienna" />
            <x-stat-card title="Customers" :value="$totalCustomers ?? 342" icon="👥" border="cream" />
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
                        <p class="text-sage italic">No recent orders recorded.</p>
                    </div>
                @endforelse
            </div>
        </div>

        @if($lowStockAlerts->isNotEmpty())
            <div class="card-rustic border-sienna">
                <h2 class="text-xl font-bold text-sienna mb-6">Critical Inventory Alerts</h2>
                <x-alert-table :alerts="$lowStockAlerts" />
            </div>
        @endif
    </div>
</x-app-layout>
