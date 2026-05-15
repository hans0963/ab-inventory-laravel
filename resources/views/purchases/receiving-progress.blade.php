@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <h1 class="text-2xl font-bold text-gray-900">Purchase Order Receiving Progress Report</h1>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm">Total POs</p>
                <p class="text-3xl font-bold mt-2">{{ $purchases->total() }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm">Pending</p>
                <p class="text-3xl font-bold mt-2 text-yellow-600">{{ $purchases->total() }}</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm">Partial Received</p>
                <p class="text-3xl font-bold mt-2 text-blue-600">0</p>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm">Average Progress</p>
                <p class="text-3xl font-bold mt-2 text-[#E2725B]">{{ round($purchases->avg(function($p) { return $p->getReceptionProgress(); })) }}%</p>
            </div>
        </div>

        <!-- PO Progress List -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b">
                <h2 class="text-lg font-semibold text-gray-900">Purchase Orders by Reception Progress</h2>
            </div>

            <div class="space-y-4 p-6">
                @forelse($purchases as $purchase)
                    <div class="border rounded-lg p-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900">{{ $purchase->po_number }}</h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ $purchase->supplier->supplier_name }} • Ordered: {{ $purchase->purchase_date->format('M d, Y') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-semibold text-gray-900">
                                    <span class="text-[#E2725B]">{{ $purchase->getTotalQuantityReceived() }}</span> / {{ $purchase->getTotalQuantityOrdered() }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    ₱{{ number_format($purchase->total_amount, 2) }}
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mb-3">
                            <div class="flex justify-between items-center mb-2">
                                <p class="text-sm font-medium text-gray-700">Reception Progress</p>
                                <p class="text-sm font-bold text-gray-900">{{ $purchase->getReceptionProgress() }}%</p>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-[#E2725B] h-3 rounded-full transition-all" style="width: {{ $purchase->getReceptionProgress() }}%"></div>
                            </div>
                        </div>

                        <!-- Status Badge and Status -->
                        <div class="flex items-center justify-between">
                            <div class="flex gap-2">
                                @if($purchase->status === 'Pending')
                                    <span class="inline-block bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">Pending</span>
                                @elseif($purchase->status === 'Partial')
                                    <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold">Partial</span>
                                @else
                                    <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">Complete</span>
                                @endif

                                @if($purchase->inventoryReceivings->count() > 0)
                                    <span class="inline-block bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs font-semibold">
                                        {{ $purchase->inventoryReceivings->count() }} receiving(s)
                                    </span>
                                @endif
                            </div>
                            <a href="{{ route('purchases.show', $purchase) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                View Details →
                            </a>
                        </div>

                        <!-- Details Grid -->
                        @if($purchase->inventoryReceivings->count() > 0)
                            <div class="mt-3 pt-3 border-t grid grid-cols-3 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-600">Items Received</p>
                                    <p class="font-semibold text-gray-900">{{ $purchase->getTotalQuantityReceived() }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Items Pending</p>
                                    <p class="font-semibold text-gray-900">{{ $purchase->getTotalQuantityOrdered() - $purchase->getTotalQuantityReceived() }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Receivings</p>
                                    <p class="font-semibold text-gray-900">{{ $purchase->inventoryReceivings->count() }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-gray-500">No active purchase orders</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $purchases->links() }}
        </div>
    </div>
</div>
@endsection
