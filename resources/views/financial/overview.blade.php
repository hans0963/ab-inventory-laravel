<x-app-layout>
    <x-slot name="header">
        <h2 class="font-pacifico text-3xl text-sienna leading-tight">
            {{ __('Financial Overview') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Total Revenue -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-300 rounded-lg p-6 shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-semibold">Total Revenue</p>
                        <p class="text-3xl font-bold text-green-700 mt-2">₱{{ number_format($totalRevenue, 2) }}</p>
                        <p class="text-xs text-gray-500 mt-1">From {{ $orderCount }} orders</p>
                    </div>
                    <div class="text-4xl">💰</div>
                </div>
            </div>

            <!-- Total Expenses -->
            <div class="bg-gradient-to-br from-red-50 to-red-100 border border-red-300 rounded-lg p-6 shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-semibold">Total Expenses</p>
                        <p class="text-3xl font-bold text-red-700 mt-2">₱{{ number_format($totalExpenses, 2) }}</p>
                        <p class="text-xs text-gray-500 mt-1">From {{ $purchaseCount }} purchases</p>
                    </div>
                    <div class="text-4xl">📊</div>
                </div>
            </div>

            <!-- Net Profit -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-300 rounded-lg p-6 shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-semibold">Net Profit</p>
                        <p class="text-3xl font-bold {{ $profit >= 0 ? 'text-blue-700' : 'text-red-700' }} mt-2">
                            ₱{{ number_format($profit, 2) }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $profit >= 0 ? '✓ Positive' : '✗ Negative' }}
                        </p>
                    </div>
                    <div class="text-4xl">{{ $profit >= 0 ? '📈' : '📉' }}</div>
                </div>
            </div>

            <!-- Profit Margin -->
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-300 rounded-lg p-6 shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-semibold">Profit Margin</p>
                        <p class="text-3xl font-bold text-purple-700 mt-2">{{ number_format($profitMargin, 1) }}%</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $profitMargin > 20 ? 'Excellent' : ($profitMargin > 10 ? 'Good' : 'Need improvement') }}
                        </p>
                    </div>
                    <div class="text-4xl">📊</div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Orders -->
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <h3 class="text-xl font-semibold text-sienna mb-4">Recent Orders</h3>
                @if($recentOrders->isEmpty())
                    <p class="text-gray-500 text-center py-8">No orders yet</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="px-4 py-2 text-left text-gray-700">Order ID</th>
                                    <th class="px-4 py-2 text-left text-gray-700">Amount</th>
                                    <th class="px-4 py-2 text-left text-gray-700">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-2 text-gray-900 font-semibold">#{{ $order->id }}</td>
                                        <td class="px-4 py-2 text-green-600 font-semibold">₱{{ number_format($order->total, 2) }}</td>
                                        <td class="px-4 py-2 text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Recent Purchases -->
            <div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
                <h3 class="text-xl font-semibold text-sienna mb-4">Recent Purchases</h3>
                @if($recentPurchases->isEmpty())
                    <p class="text-gray-500 text-center py-8">No purchases yet</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="px-4 py-2 text-left text-gray-700">Purchase ID</th>
                                    <th class="px-4 py-2 text-left text-gray-700">Supplier</th>
                                    <th class="px-4 py-2 text-left text-gray-700">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentPurchases as $purchase)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-2 text-gray-900 font-semibold">#{{ $purchase->id }}</td>
                                        <td class="px-4 py-2 text-gray-700">{{ $purchase->supplier->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-2 text-gray-500">{{ $purchase->purchase_date }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
