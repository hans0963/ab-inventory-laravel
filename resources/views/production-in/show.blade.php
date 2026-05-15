@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 to-orange-50 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-800">Production IN Details</h1>
                <p class="text-gray-600 mt-2">Batch #{{ $productionIn->production_in_no }}</p>
            </div>
            <a href="{{ route('production-in.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">← Back</a>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Info Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-md p-4">
                <p class="text-gray-600 text-sm">Batch #</p>
                <p class="text-lg font-semibold text-gray-800">{{ $productionIn->production_in_no }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4">
                <p class="text-gray-600 text-sm">Date</p>
                <p class="text-lg font-semibold text-gray-800">{{ $productionIn->date->format('M d, Y') }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4">
                <p class="text-gray-600 text-sm">Status</p>
                <p class="text-lg font-semibold">
                    @if($productionIn->status === 'Pending')
                        <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">Pending</span>
                    @elseif($productionIn->status === 'Approved')
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">Approved</span>
                    @else
                        <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm">Rejected</span>
                    @endif
                </p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4">
                <p class="text-gray-600 text-sm">Total Value</p>
                <p class="text-lg font-semibold text-gray-800">₱{{ number_format($productionIn->total_inventory_value, 2) }}</p>
            </div>
        </div>

        <!-- Details -->
        <div class="bg-white rounded-lg shadow-md p-8 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">Batch Information</h2>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-gray-600 text-sm">Created By</p>
                    <p class="text-gray-800 font-medium">{{ $productionIn->createdBy->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Created Date</p>
                    <p class="text-gray-800 font-medium">{{ $productionIn->created_date->format('M d, Y') }}</p>
                </div>
                @if($productionIn->approvedBy)
                    <div>
                        <p class="text-gray-600 text-sm">Approved By</p>
                        <p class="text-gray-800 font-medium">{{ $productionIn->approvedBy->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Approved Date</p>
                        <p class="text-gray-800 font-medium">{{ $productionIn->approved_date->format('M d, Y') }}</p>
                    </div>
                @endif
                @if($productionIn->notes)
                    <div class="col-span-2">
                        <p class="text-gray-600 text-sm">Notes</p>
                        <p class="text-gray-800">{{ $productionIn->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Items Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
            <table class="w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Product</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Quantity</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Unit Price</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Total Value</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Expiration</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($productionIn->items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-800">{{ $item->product->product_name }}</td>
                            <td class="px-6 py-4 text-sm text-right text-gray-600">{{ number_format($item->quantity) }}</td>
                            <td class="px-6 py-4 text-sm text-right text-gray-600">₱{{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-right text-gray-600">₱{{ number_format($item->total_value, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @if($item->expiration_date)
                                    {{ $item->expiration_date->format('M d, Y') }}
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Actions -->
        <div class="flex gap-4">
            @if($productionIn->status === 'Pending')
                <form action="{{ route('production-in.approve', $productionIn->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700" onclick="return confirm('Approve this production batch?');">
                        ✓ Approve Batch
                    </button>
                </form>
                <form action="{{ route('production-in.reject', $productionIn->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700" onclick="return confirm('Reject this production batch?');">
                        ✗ Reject Batch
                    </button>
                </form>
                <form action="{{ route('production-in.destroy', $productionIn->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700" onclick="return confirm('Delete this batch?');">
                        Delete
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
