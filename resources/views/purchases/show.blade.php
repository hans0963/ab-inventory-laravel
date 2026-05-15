@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $purchase->po_number }}</h1>
                    <p class="text-gray-600 mt-1">{{ $purchase->supplier->supplier_name }} • {{ $purchase->purchase_date->format('M d, Y') }}</p>
                </div>
                <div class="flex gap-2">
                    @if($purchase->status === 'Pending')
                        <a href="{{ route('purchases.edit', $purchase) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                            Edit
                        </a>
                        <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" onclick="return confirm('Are you sure?')" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                                Delete
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('purchases.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Status Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm">Status</p>
                <p class="text-2xl font-bold mt-2">
                    @if($purchase->status === 'Pending')
                        <span class="text-yellow-600">Pending</span>
                    @elseif($purchase->status === 'Partial')
                        <span class="text-blue-600">Partial</span>
                    @else
                        <span class="text-green-600">Complete</span>
                    @endif
                </p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm">Total Amount</p>
                <p class="text-2xl font-bold mt-2">₱{{ number_format($purchase->total_amount, 2) }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm">Items Ordered</p>
                <p class="text-2xl font-bold mt-2">{{ $totalOrdered }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm">Items Received</p>
                <p class="text-2xl font-bold mt-2 text-[#E2725B]">{{ $totalReceived }}</p>
            </div>
        </div>

        <!-- Reception Progress -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Reception Progress</h2>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <p class="text-sm font-medium text-gray-700">Overall Progress</p>
                        <p class="text-sm font-bold text-gray-900">{{ $receivingProgress }}%</p>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div class="bg-[#E2725B] h-4 rounded-full transition-all" style="width: {{ $receivingProgress }}%"></div>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600">Ordered</p>
                        <p class="text-xl font-bold">{{ $totalOrdered }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Received</p>
                        <p class="text-xl font-bold text-green-600">{{ $totalReceived }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Pending</p>
                        <p class="text-xl font-bold text-orange-600">{{ $totalOrdered - $totalReceived }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchase Details -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Purchase Details</h2>
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
                    <tr class="bg-gray-50 font-semibold">
                        <td colspan="3" class="px-6 py-4 text-right">Total:</td>
                        <td class="px-6 py-4 text-gray-900">₱{{ number_format($purchase->total_amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Linked Receivings -->
        @if($purchase->inventoryReceivings->count() > 0)
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Linked Inventory Receivings</h2>
                <table class="w-full">
                    <thead class="bg-gray-100 border-b">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Receiving #</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Items</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Amount</th>
                            <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($purchase->inventoryReceivings as $receiving)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $receiving->receiving_no }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $receiving->date->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                                        {{ $receiving->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $receiving->pivot->items_received }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">₱{{ number_format($receiving->pivot->amount_received, 2) }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <form action="{{ route('purchases.unlink-receiving', $purchase) }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="inventory_receiving_id" value="{{ $receiving->id }}">
                                        <button type="submit" onclick="return confirm('Unlink this receiving?')" class="text-red-600 hover:text-red-900">
                                            Unlink
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Link Receiving Button -->
        @if($purchase->canReceive())
            <div class="flex gap-2">
                <a href="{{ route('purchases.receiving-matching', $purchase) }}" class="bg-[#E2725B] hover:bg-[#D97760] text-white px-6 py-3 rounded-lg font-medium">
                    Link Inventory Receiving
                </a>
            </div>
        @endif

        <!-- Audit Trail -->
        @if($purchase->createdBy || $purchase->approvedBy)
            <div class="bg-gray-50 rounded-lg p-6 mt-6">
                <h3 class="text-sm font-semibold text-gray-900 mb-4">Audit Trail</h3>
                <div class="space-y-3 text-sm text-gray-600">
                    @if($purchase->createdBy)
                        <p>Created by <strong>{{ $purchase->createdBy->name }}</strong> on {{ $purchase->created_date?->format('M d, Y h:i A') }}</p>
                    @endif
                    @if($purchase->approvedBy)
                        <p>Approved by <strong>{{ $purchase->approvedBy->name }}</strong> on {{ $purchase->approved_date?->format('M d, Y h:i A') }}</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
