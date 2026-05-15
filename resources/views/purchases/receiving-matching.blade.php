@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Link Inventory Receiving</h1>
                    <p class="text-gray-600 mt-1">Purchase Order: {{ $purchase->po_number }}</p>
                </div>
                <a href="{{ route('purchases.show', $purchase) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                    Back to PO
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- PO Summary -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Purchase Order Summary</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-gray-600 text-sm">Supplier</p>
                    <p class="text-gray-900 font-semibold">{{ $purchase->supplier->supplier_name }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Total Items to Receive</p>
                    <p class="text-gray-900 font-semibold">{{ $purchase->getTotalQuantityOrdered() }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Already Received</p>
                    <p class="text-gray-900 font-semibold text-[#E2725B]">{{ $purchase->getTotalQuantityReceived() }}</p>
                </div>
            </div>
        </div>

        <!-- PO Items -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">PO Items</h2>
            <table class="w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Product</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Quantity</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Unit Price</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($purchase->details as $detail)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $detail->product->product_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $detail->quantity }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">₱{{ number_format($detail->price, 2) }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">₱{{ number_format($detail->total, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Available Receivings -->
        @if($availableReceivings->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Available Inventory Receivings</h2>
                <div class="space-y-4">
                    @foreach($availableReceivings as $receiving)
                        <div class="border rounded-lg p-4 hover:bg-gray-50">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $receiving->receiving_no }}</h3>
                                    <p class="text-sm text-gray-600">{{ $receiving->date->format('M d, Y') }} • {{ $receiving->supplier->supplier_name }}</p>
                                </div>
                                <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                                    {{ $receiving->status }}
                                </span>
                            </div>

                            <!-- Receiving Items -->
                            <table class="w-full mb-4">
                                <thead class="bg-gray-50 text-xs">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-gray-700">Product</th>
                                        <th class="px-3 py-2 text-left text-gray-700">Received</th>
                                        <th class="px-3 py-2 text-left text-gray-700">Condition</th>
                                        <th class="px-3 py-2 text-left text-gray-700">Cost</th>
                                    </tr>
                                </thead>
                                <tbody class="text-sm divide-y">
                                    @foreach($receiving->items as $item)
                                        <tr>
                                            <td class="px-3 py-2 text-gray-900">{{ $item->product->product_name }}</td>
                                            <td class="px-3 py-2 text-gray-600">{{ $item->quantity_received }}</td>
                                            <td class="px-3 py-2">
                                                <span class="inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">
                                                    {{ $item->condition }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2 text-gray-600">₱{{ number_format($item->total_cost, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Link Button -->
                            <form action="{{ route('purchases.link-receiving', $purchase) }}" method="POST">
                                @csrf
                                <input type="hidden" name="inventory_receiving_id" value="{{ $receiving->id }}">
                                <button type="submit" class="w-full bg-[#E2725B] hover:bg-[#D97760] text-white px-4 py-2 rounded-lg text-sm font-medium">
                                    Link to this Receiving
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-center text-gray-500">
                    No approved inventory receivings available from {{ $purchase->supplier->supplier_name }}
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
