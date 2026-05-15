@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 to-orange-50 py-12 px-4">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-800">Withdrawal Details</h1>
                <p class="text-gray-600 mt-2">Reference #{{ $withdrawal->withdrawal_no }}</p>
            </div>
            <a href="{{ route('stock-withdrawal.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">← Back</a>
        </div>

        <!-- Info Cards -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-md p-4">
                <p class="text-gray-600 text-sm">Reference #</p>
                <p class="text-sm font-semibold text-gray-800 truncate">{{ $withdrawal->withdrawal_no }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4">
                <p class="text-gray-600 text-sm">Date</p>
                <p class="text-sm font-semibold text-gray-800">{{ $withdrawal->date->format('M d, Y') }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4">
                <p class="text-gray-600 text-sm">Reason</p>
                <p class="text-sm font-semibold text-gray-800">{{ $withdrawal->reason }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4">
                <p class="text-gray-600 text-sm">Total Qty</p>
                <p class="text-sm font-semibold text-gray-800">{{ $withdrawal->total_quantity }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-md p-4">
                <p class="text-gray-600 text-sm">Status</p>
                <p class="text-sm font-semibold">
                    @if($withdrawal->status === 'Pending')
                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Pending</span>
                    @elseif($withdrawal->status === 'Approved')
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">Approved</span>
                    @else
                        <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Rejected</span>
                    @endif
                </p>
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
                    @foreach($withdrawal->items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-800">{{ $item->product->product_name }}</td>
                            <td class="px-6 py-4 text-sm text-right text-gray-600">{{ number_format($item->quantity) }}</td>
                            <td class="px-6 py-4 text-sm text-right text-gray-600">₱{{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-right text-gray-600">₱{{ number_format($item->total_value, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-100 border-t">
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-right text-sm font-semibold text-gray-800">Total Value:</td>
                        <td class="px-6 py-4 text-right text-sm font-semibold text-gray-800">₱{{ number_format($withdrawal->total_value, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Actions -->
        <div class="flex gap-4">
            @if($withdrawal->status === 'Pending')
                <form action="{{ route('stock-withdrawal.approve', $withdrawal->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700" onclick="return confirm('Approve this withdrawal?');">
                        ✓ Approve
                    </button>
                </form>
                <form action="{{ route('stock-withdrawal.reject', $withdrawal->id) }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700" onclick="return confirm('Reject this withdrawal?');">
                        ✗ Reject
                    </button>
                </form>
                <form action="{{ route('stock-withdrawal.destroy', $withdrawal->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700" onclick="return confirm('Delete this withdrawal?');">
                        Delete
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
