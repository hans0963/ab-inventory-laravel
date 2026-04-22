<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Orders') }}
        </h2>
    </x-slot>
    <div class="max-w-6xl mx-auto mt-10 p-6 bg-white shadow-lg rounded-lg">
        <h2 class="text-2xl font-semibold text-gray-700 mb-4">📋 Orders List</h2>

        <table class="min-w-full border border-gray-300 bg-white shadow-lg rounded-lg">
            <thead>
                <tr class="bg-gradient-to-r from-blue-500 to-blue-700 text-white">
                    <th class="px-4 py-3 text-left">Order ID</th>
                    <th class="px-4 py-3 text-left">Customer</th>
                    <th class="px-4 py-3 text-left">Total</th>
                    <th class="px-4 py-3 text-left">Date</th>
                    <th class="px-4 py-3 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($orders as $order)
                    <tr class="hover:bg-gray-100 transition">
                        <td class="px-4 py-3">#{{ $order->id }}</td>
                        <td class="px-4 py-3">{{ $order->customer->name }}</td>
                        <td class="px-4 py-3 font-semibold text-green-600">₱{{ number_format($order->total, 2) }}</td>
                        <td class="px-4 py-3">{{ $order->order_date }}</td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('orders.show', $order->id) }}" 
                               class="px-4 py-2 bg-blue-500 text-white rounded-lg">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>
</x-app-layout>
