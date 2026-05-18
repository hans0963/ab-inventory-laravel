<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-xl shadow-sm border border-sienna border-opacity-20">
            <div>
                <h2 class="font-formal text-4xl text-sienna leading-tight">
                    {{ __('Sales Orders') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Track and manage bakeshop orders</p>
            </div>
            @if(auth()->user()->hasRole(['cashier', 'manager', 'admin']))
                <a href="{{ route('orders.create') }}" 
                   class="bg-terracotta hover:bg-terracotta-dark text-cream font-bold px-8 py-3 rounded-xl shadow-rustic transition-all hover:-translate-y-0.5 active:translate-y-0 flex items-center">
                    <span class="mr-2 text-xl">+</span> New Order
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
        {{-- Summary Metrics --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <x-stat-card title="Today's Sales" :value="'₱' . number_format($todaysSales, 2)" icon="💰" border="terracotta" />
            <x-stat-card title="Total Orders" :value="$totalOrders" icon="📦" border="sage" />
            <x-stat-card title="SC/PWD Discounts" :value="'₱' . number_format($discounts, 2)" icon="🎟️" border="tan" />
        </div>

        <div class="card-rustic border-sienna overflow-hidden">
            {{-- Header with search --}}
            <div class="p-6 border-b border-sienna border-opacity-10">
                <form action="{{ route('orders.index') }}" method="GET" class="flex gap-4">
                    <div class="flex-1 relative">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Search by order ID or customer name..." 
                               class="w-full rounded-xl border-sienna border-opacity-20 bg-cream bg-opacity-20 py-3 pl-12 pr-4 focus:ring-2 focus:ring-terracotta focus:border-terracotta transition-all shadow-inner">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-sienna opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                    <button type="submit" class="bg-sienna hover:bg-sienna-dark text-cream font-bold px-6 py-3 rounded-xl shadow-md transition-all">
                        Search
                    </button>
                </form>
            </div>

            {{-- Orders Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-sienna text-cream uppercase text-[10px] tracking-widest font-black">
                            <th class="px-6 py-4 text-left">ID</th>
                            <th class="px-6 py-4 text-left">Customer</th>
                            <th class="px-6 py-4 text-center">Items</th>
                            <th class="px-6 py-4 text-right">Total</th>
                            <th class="px-6 py-4 text-left">Date</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                        @forelse ($orders as $order)
                            <tr class="hover:bg-cream hover:bg-opacity-20 transition-colors">
                                <td class="px-6 py-4 font-mono text-xs text-sienna font-bold">#{{ $order->id }}</td>
                                <td class="px-6 py-4 font-bold text-sienna">{{ $order->customer->name ?? 'Walk-in' }}</td>
                                <td class="px-6 py-4 text-center font-black">{{ $order->total_products }}</td>
                                <td class="px-6 py-4 text-right font-black text-terracotta text-base">₱{{ number_format($order->total, 2) }}</td>
                                <td class="px-6 py-4 text-sage font-medium">
                                    {{ \Carbon\Carbon::parse($order->order_date)->format('M d, Y h:i A') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider
                                        {{ $order->order_status == 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $order->order_status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-3">
                                        <a href="{{ route('orders.show', $order->id) }}" class="p-2 text-sage hover:text-sienna hover:bg-sage hover:bg-opacity-10 rounded-lg transition-all" title="View Details">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-sage italic">No orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="p-6 border-t border-sienna border-opacity-10">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
