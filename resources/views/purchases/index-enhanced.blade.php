@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-900">Purchase Orders</h1>
                <a href="{{ route('purchases.create') }}" class="bg-[#E2725B] hover:bg-[#D97760] text-white px-4 py-2 rounded-lg">
                    New Purchase Order
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm mt-4 mx-4 p-4 rounded-lg">
        <form method="GET" action="{{ route('purchases.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Statuses</option>
                    <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Partial" {{ request('status') === 'Partial' ? 'selected' : '' }}>Partial</option>
                    <option value="Complete" {{ request('status') === 'Complete' ? 'selected' : '' }}>Complete</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                <select name="supplier_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->supplier_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>

            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    Filter
                </button>
                <a href="{{ route('purchases.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- PO List Table -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">PO Number</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Supplier</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Date</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Total Amount</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Progress</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($purchases as $purchase)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $purchase->po_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $purchase->supplier->supplier_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $purchase->purchase_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">₱{{ number_format($purchase->total_amount, 2) }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if($purchase->status === 'Pending')
                                    <span class="inline-block bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">Pending</span>
                                @elseif($purchase->status === 'Partial')
                                    <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold">Partial</span>
                                @else
                                    <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">Complete</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="flex items-center gap-2">
                                    <div class="w-24 bg-gray-200 rounded-full h-2">
                                        <div class="bg-[#E2725B] h-2 rounded-full" style="width: {{ $purchase->getReceptionProgress() }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold">{{ $purchase->getReceptionProgress() }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <a href="{{ route('purchases.show', $purchase) }}" class="text-blue-600 hover:text-blue-900">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">No purchase orders found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $purchases->links() }}
        </div>
    </div>
</div>
@endsection
