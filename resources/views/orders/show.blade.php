<x-app-layout>
    <div class="max-w-3xl mx-auto mt-8 p-6 bg-white shadow-md rounded-lg">
        <h2 class="text-2xl font-bold text-gray-700 mb-4">📜 Order Details</h2>

        <div class="bg-gray-50 p-4 rounded-lg shadow-sm mb-6">
            <div class="grid grid-cols-2 gap-4">
                <p class="text-gray-600"><span class="font-semibold">📅 Date:</span> {{ $order->order_date }}</p>
                <p class="text-gray-600"><span class="font-semibold">👤 Customer:</span> {{ $order->customer->customer_name }}</p>
                <p class="text-gray-600"><span class="font-semibold">👨‍💼 Processed By:</span> {{ $order->employee->employee_name ?? 'N/A' }}</p>
                <p class="text-gray-600"><span class="font-semibold">💳 Payment Type:</span> {{ $order->payment_type }}</p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-sm">
            <h3 class="text-lg font-semibold text-gray-700 mb-2">🛒 Ordered Products</h3>

            <div class="overflow-x-auto">
                <table class="w-full border border-gray-300 rounded-lg">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-2 text-left border-b">Product</th>
                            <th class="px-4 py-2 text-left border-b">Quantity</th>
                            <th class="px-4 py-2 text-left border-b">Unit Price</th>
                            <th class="px-4 py-2 text-left border-b">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderDetails as $detail)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="px-4 py-2">{{ $detail->product->product_name }}</td>
                            <td class="px-4 py-2">{{ $detail->quantity }}</td>
                            <td class="px-4 py-2">₱{{ number_format($detail->unit_cost, 2) }}</td>
                            <td class="px-4 py-2 font-semibold text-green-600">₱{{ number_format($detail->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="text-right text-xl font-semibold text-gray-700 mt-6">
            <span>Total: </span>
            <span class="text-green-600">₱{{ number_format($order->orderDetails->sum('total'), 2) }}</span>
        </div>

        <div class="mt-6">
            <a href="{{ route('orders.index') }}" class="inline-block px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                🔙 Back to Orders
            </a>
        </div>
    </div>
</x-app-layout>
