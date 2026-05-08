<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-md shadow-sm border-b border-sienna">
            <h2 class="font-formal text-3xl text-sienna leading-tight">
                {{ __('Sales Orders') }}
            </h2>
            <p class="font-inter text-sage">Track and manage bakeshop orders</p>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
        {{-- Summary Metrics --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <x-stat-card title="Today's Sales" :value="'₱' . number_format($todaysSales, 2)" icon="💰" border="terracotta" />
            <x-stat-card title="Total Orders" :value="$totalOrders" icon="📦" border="sage" />
            <x-stat-card title="SC/PWD Discounts" :value="'₱' . number_format($discounts, 2)" icon="🎟️" border="tan" />
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-sienna">
            {{-- Header with search + new order --}}
            <div class="flex justify-between items-center mb-6">
                <div class="w-1/3">
                    <form action="{{ route('orders.index') }}" method="GET">
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Search orders..." 
                                   class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-20 pl-10">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-sienna opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </form>
                </div>
                @if(auth()->user()->hasRole(['cashier', 'manager', 'admin']))
                    <a href="{{ route('orders.create') }}" 
                       class="bg-terracotta hover:bg-opacity-90 text-cream font-inter px-6 py-2 rounded-lg shadow-md transition flex items-center">
                        <span class="mr-2">+</span> New Order
                    </a>
                @endif
            </div>

            {{-- Orders Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-sienna text-cream uppercase text-xs tracking-wider">
                            <th class="px-4 py-3 text-left">ID</th>
                            <th class="px-4 py-3 text-left">Customer</th>
                            <th class="px-4 py-3 text-center">Items</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-4 py-3 text-left">Date</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sienna divide-opacity-20">
                        @forelse ($orders as $order)
                            <tr class="hover:bg-cream hover:bg-opacity-30 transition">
                                <td class="px-4 py-4 font-semibold text-sienna">#{{ $order->id }}</td>
                                <td class="px-4 py-4 text-sienna">{{ $order->customer->name ?? 'Walk-in' }}</td>
                                <td class="px-4 py-4 text-center text-gray-600">{{ $order->total_products }}</td>
                                <td class="px-4 py-4 text-right font-bold text-terracotta">₱{{ number_format($order->total, 2) }}</td>
                                <td class="px-4 py-4 text-gray-600 text-xs">
                                    {{ \Carbon\Carbon::parse($order->order_date)->format('M d, Y h:i A') }}
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="px-2 py-1 rounded text-white text-[10px] font-bold uppercase tracking-wider
                                        {{ $order->order_status == 'Pending' ? 'bg-yellow-500' : 'bg-sage' }}">
                                        {{ $order->order_status }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <a href="{{ route('orders.show', $order->id) }}" class="text-sienna hover:text-terracotta transition">
                                        <svg class="h-5 w-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500 italic">No orders found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
