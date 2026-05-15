@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 to-orange-50 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-800">Production OUT Details</h1>
                <p class="text-gray-600 mt-2">Reference #{{ $productionOut->production_out_no }}</p>
            </div>
            <a href="{{ route('production-out.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">← Back</a>
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-md p-4">
                <p class="text-gray-600 text-sm">Reference #</p>
                <p class="text-lg font-semibold text-gray-800">{{ $productionOut->production_out_no }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4">
                <p class="text-gray-600 text-sm">Date</p>
                <p class="text-lg font-semibold text-gray-800">{{ $productionOut->date->format('M d, Y') }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4">
                <p class="text-gray-600 text-sm">Status</p>
                <p class="text-lg font-semibold">
                    @if($productionOut->status === 'Pending')
                        <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-sm">Pending</span>
                    @elseif($productionOut->status === 'Approved')
                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">Approved</span>
                    @else
                        <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm">Rejected</span>
                    @endif
                </p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4">
                <p class="text-gray-600 text-sm">Reason</p>
                <p class="text-lg font-semibold text-gray-800">{{ $productionOut->reason }}</p>
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
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($productionOut->items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-800">{{ $item->product->product_name }}</td>
                            <td class="px-6 py-4 text-sm text-right text-gray-600">{{ number_format($item->quantity) }}</td>
                            <td class="px-6 py-4 text-sm text-right text-gray-600">₱{{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-right text-gray-600">₱{{ number_format($item->total_value, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Actions -->
        <div class="flex gap-4">
            @if($productionOut->status === 'Pending')
                <form action="{{ route('production-out.approve', $productionOut->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700" onclick="return confirm('Approve this OUT transaction?');">
                        ✓ Approve
                    </button>
                </form>
                <form action="{{ route('production-out.reject', $productionOut->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700" onclick="return confirm('Reject this OUT transaction?');">
                        ✗ Reject
                    </button>
                </form>
                <form action="{{ route('production-out.destroy', $productionOut->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700" onclick="return confirm('Delete this OUT transaction?');">
                        Delete
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
