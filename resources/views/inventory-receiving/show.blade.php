@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold" style="color: #E2725B;">{{ $receiving->receiving_no }}</h1>
                <p class="text-gray-600 mt-2">{{ $receiving->date->format('F d, Y') }} • {{ $receiving->supplier->supplier_name }}</p>
            </div>
            <div class="text-right">
                @if($receiving->status === 'Pending')
                    <span class="px-4 py-2 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">Pending</span>
                @elseif($receiving->status === 'Approved')
                    <span class="px-4 py-2 rounded-full text-sm font-semibold bg-green-100 text-green-800">Approved</span>
                @else
                    <span class="px-4 py-2 rounded-full text-sm font-semibold bg-red-100 text-red-800">Rejected</span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Summary Cards -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <p class="text-gray-600 text-sm">Total Items</p>
                <p class="text-3xl font-bold" style="color: #E2725B;">{{ $receiving->total_items }}</p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <p class="text-gray-600 text-sm">Total Cost</p>
                <p class="text-3xl font-bold" style="color: #E2725B;">₱{{ number_format($receiving->total_cost, 2) }}</p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <p class="text-gray-600 text-sm">Created By</p>
                <p class="text-lg font-semibold">{{ $receiving->createdBy->name }}</p>
                <p class="text-xs text-gray-500">{{ $receiving->created_date->format('M d, Y') }}</p>
            </div>
        </div>

        <!-- Items Table -->
        <div class="bg-white rounded-lg shadow-md mb-8 overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="text-xl font-bold" style="color: #E2725B;">Received Items</h2>
            </div>
            <table class="w-full">
                <thead style="background-color: #F5E6D3;">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold" style="color: #E2725B;">Product</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold" style="color: #E2725B;">Ordered</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold" style="color: #E2725B;">Received</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold" style="color: #E2725B;">Unit Cost</th>
                        <th class="px-6 py-3 text-right text-sm font-semibold" style="color: #E2725B;">Total</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold" style="color: #E2725B;">Condition</th>
                        <th class="px-6 py-3 text-center text-sm font-semibold" style="color: #E2725B;">Exp. Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($receiving->items as $item)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold">{{ $item->product->product_name }}</td>
                            <td class="px-6 py-4 text-center">{{ $item->quantity_ordered }}</td>
                            <td class="px-6 py-4 text-center font-semibold">{{ $item->quantity_received }}</td>
                            <td class="px-6 py-4 text-right">₱{{ number_format($item->unit_cost, 2) }}</td>
                            <td class="px-6 py-4 text-right font-semibold">₱{{ number_format($item->total_cost, 2) }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($item->condition === 'Good')
                                    <span class="px-2 py-1 rounded text-xs font-semibold bg-green-100 text-green-800">Good</span>
                                @elseif($item->condition === 'Damaged')
                                    <span class="px-2 py-1 rounded text-xs font-semibold bg-red-100 text-red-800">Damaged</span>
                                @elseif($item->condition === 'Expired')
                                    <span class="px-2 py-1 rounded text-xs font-semibold bg-orange-100 text-orange-800">Expired</span>
                                @else
                                    <span class="px-2 py-1 rounded text-xs font-semibold bg-gray-100 text-gray-800">Other</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                {{ $item->expiration_date ? $item->expiration_date->format('M d, Y') : '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Approval Info -->
        @if($receiving->status !== 'Pending')
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h3 class="text-lg font-bold mb-4" style="color: #E2725B;">Approval Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-gray-600 text-sm">Approved By</p>
                        <p class="font-semibold">{{ $receiving->approvedBy->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600 text-sm">Approval Date</p>
                        <p class="font-semibold">{{ $receiving->approved_date ? $receiving->approved_date->format('M d, Y g:i A') : 'N/A' }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Actions -->
        @if($receiving->status === 'Pending')
            <div class="flex gap-4 mb-8">
                <a href="{{ route('inventory-receiving.edit', $receiving) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold">
                    Edit
                </a>
                <form action="{{ route('inventory-receiving.approve', $receiving) }}" method="POST" class="inline">
                    @csrf
                    @method('POST')
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold" onclick="return confirm('Approve this receiving?')">
                        Approve
                    </button>
                </form>
                <form action="{{ route('inventory-receiving.reject', $receiving) }}" method="POST" class="inline">
                    @csrf
                    @method('POST')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-semibold" onclick="return confirm('Reject this receiving?')">
                        Reject
                    </button>
                </form>
                <form action="{{ route('inventory-receiving.destroy', $receiving) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold" onclick="return confirm('Delete this receiving?')">
                        Delete
                    </button>
                </form>
            </div>
        @endif

        <a href="{{ route('inventory-receiving.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">← Back to Receivings</a>
    </div>
</div>
@endsection
