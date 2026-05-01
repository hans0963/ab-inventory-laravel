<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-md shadow-sm border-b border-sienna">
            <h2 class="font-pacifico text-3xl text-sienna leading-tight">
                {{ __('Order Details') }}
            </h2>
            <p class="font-inter text-sage">Detailed receipt for Order #{{ $order->id }}</p>
        </div>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white rounded-lg shadow-md p-8 border-l-4 border-sienna">
            {{-- Receipt Header --}}
            <div class="flex justify-between items-start border-b border-tan border-opacity-30 pb-6 mb-6">
                <div class="space-y-1">
                    <p class="text-xs uppercase tracking-widest text-sage font-bold">Customer</p>
                    <p class="text-xl font-lora font-bold text-sienna">{{ $order->customer->name ?? 'Walk-in Customer' }}</p>
                    <p class="text-sm text-gray-500">{{ $order->customer->email ?? '' }}</p>
                </div>
                <div class="text-right space-y-1">
                    <p class="text-xs uppercase tracking-widest text-sage font-bold">Order ID</p>
                    <p class="text-xl font-lora font-bold text-sienna">#{{ $order->id }}</p>
                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($order->order_date)->format('M d, Y h:i A') }}</p>
                </div>
            </div>

            {{-- Meta Info --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 bg-cream bg-opacity-20 p-4 rounded-lg border border-tan border-opacity-20">
                <div>
                    <p class="text-[10px] uppercase tracking-widest text-sage font-bold">Processed By</p>
                    <p class="text-sm font-semibold text-sienna">{{ $order->employee->employee_name ?? 'System' }}</p>
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-widest text-sage font-bold">Payment Method</p>
                    <p class="text-sm font-semibold text-sienna">{{ $order->payment_type }}</p>
                </div>
                <div>
                    <p class="text-[10px] uppercase tracking-widest text-sage font-bold">Status</p>
                    <span class="inline-block px-2 py-0.5 rounded text-white text-[10px] font-bold uppercase tracking-wider mt-1
                        {{ $order->order_status == 'Pending' ? 'bg-yellow-500' : 'bg-sage' }}">
                        {{ $order->order_status }}
                    </span>
                </div>
            </div>

            {{-- Order Items Table --}}
            <div class="space-y-4">
                <h3 class="text-lg font-lora font-semibold text-sienna">Ordered Items</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-sienna text-cream uppercase text-[10px] tracking-widest">
                                <th class="px-4 py-2 text-left">Product</th>
                                <th class="px-4 py-2 text-center">Qty</th>
                                <th class="px-4 py-2 text-right">Unit Price</th>
                                <th class="px-4 py-2 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-tan divide-opacity-20">
                            @foreach($order->orderDetails as $detail)
                            <tr class="hover:bg-cream hover:bg-opacity-30 transition">
                                <td class="px-4 py-4 font-semibold text-sienna">{{ $detail->product->product_name }}</td>
                                <td class="px-4 py-4 text-center text-gray-600">{{ $detail->quantity }}</td>
                                <td class="px-4 py-4 text-right text-gray-600">₱{{ number_format($detail->unit_cost, 2) }}</td>
                                <td class="px-4 py-4 text-right font-bold text-terracotta">₱{{ number_format($detail->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Summary --}}
            <div class="mt-8 pt-6 border-t border-tan border-opacity-30 flex justify-between items-end">
                <div class="text-left">
                    <a href="{{ route('orders.index') }}" class="text-sage hover:text-sienna font-semibold transition flex items-center">
                        <span class="mr-2">←</span> Back to Orders
                    </a>
                </div>
                <div class="text-right space-y-1">
                    <p class="text-sm text-gray-500 uppercase tracking-widest font-bold">Grand Total</p>
                    <p class="text-4xl font-lora font-bold text-terracotta">₱{{ number_format($order->total, 2) }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
