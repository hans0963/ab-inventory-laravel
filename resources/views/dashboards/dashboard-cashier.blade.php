<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ __('Sales Terminal') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Register and track bakeshop sales</p>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-black text-sage uppercase tracking-widest">Shift Performance</p>
                <p class="text-2xl font-formal text-sienna font-bold">₱{{ number_format($todaysSales, 2) }}</p>
            </div>
        </div>
    </x-slot>    

    <div class="space-y-10">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-8">
            <a href="{{ route('sales.index') }}" class="block transform hover:scale-105 transition">
                <x-stat-card title="Today's Sales" :value="'₱' . number_format($todaysSales, 2)" icon="💰" border="terracotta" />
            </a>
            <a href="{{ route('sales.index') }}" class="block transform hover:scale-105 transition">
                <x-stat-card title="Transactions Today" :value="$transactionsCount" icon="🧾" border="sage" />
            </a>
        </div>

        <!-- Recent Sales -->
        <div class="card-rustic border-terracotta">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-sienna">My Recent Sales (Today)</h2>
                <a href="{{ route('sales.create') }}" class="bg-sienna text-cream px-4 py-2 rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-opacity-90 transition">
                    + New Sale
                </a>
            </div>
            <div class="divide-y divide-sienna divide-opacity-10">
                @forelse($recentOrders ?? [] as $sale)
                    <div class="flex justify-between items-center py-4 hover:bg-cream hover:bg-opacity-50 transition rounded-lg px-2 -mx-2">
                        <div>
                            <p class="font-bold text-sienna">{{ $sale->product->product_name }} — {{ $sale->customer->name ?? 'Walk-in' }}</p>
                            <p class="text-sm text-sage font-medium">{{ $sale->sold }} unit(s) @ ₱{{ number_format($sale->total_amount, 2) }}</p>
                            <p class="text-xs text-sienna opacity-60">{{ $sale->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="text-xl font-bold text-terracotta">₱{{ number_format($sale->total_amount, 2) }}</div>
                            <a href="{{ route('sales.show', $sale->id) }}" class="text-sage hover:text-sienna transition">
                                👁️
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="py-10 text-center">
                        <p class="text-sage italic">No sales recorded during your shift today.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Low Stock Alerts -->
        <div class="card-rustic border-sienna">
            <h2 class="text-xl font-bold text-sienna mb-6 font-lora flex items-center">
                <span class="mr-2 text-2xl">⚠️</span> Inventory Status Alerts
            </h2>
            <div class="overflow-x-auto rounded-xl border border-sienna border-opacity-10">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-sienna text-cream uppercase text-[10px] tracking-widest font-black">
                            <th class="px-6 py-4 text-left">Product</th>
                            <th class="px-6 py-4 text-center">Current Qty</th>
                            <th class="px-6 py-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                        @forelse($lowStockAlerts as $alert)
                            <tr>
                                <td class="px-6 py-4 font-bold text-sienna">{{ $alert->product_name }}</td>
                                <td class="px-6 py-4 text-center font-black text-terracotta">{{ $alert->quantity }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-red-100 text-red-600 uppercase font-black tracking-widest">
                                        Low Stock
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-sage italic">No critical stock alerts.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
