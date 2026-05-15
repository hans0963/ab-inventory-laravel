@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold" style="color: #E2725B;">Inventory Receiving</h1>
                <p class="text-gray-600 mt-2">Manage incoming stock from suppliers</p>
            </div>
            <a href="{{ route('inventory-receiving.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold">
                + New Receiving
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="">All Statuses</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                        <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Supplier</label>
                    <select name="supplier_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->supplier_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <div class="md:col-span-4 flex gap-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">Filter</button>
                    <a href="{{ route('inventory-receiving.index') }}" class="bg-gray-400 hover:bg-gray-500 text-white px-6 py-2 rounded-lg">Reset</a>
                </div>
            </form>
        </div>

        <!-- Receivings Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="w-full">
                <thead style="background-color: #F5E6D3;">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-semibold" style="color: #E2725B;">Receiving #</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold" style="color: #E2725B;">Date</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold" style="color: #E2725B;">Supplier</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold" style="color: #E2725B;">Items</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold" style="color: #E2725B;">Total Cost</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold" style="color: #E2725B;">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-semibold" style="color: #E2725B;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receivings as $receiving)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold">{{ $receiving->receiving_no }}</td>
                            <td class="px-6 py-4">{{ $receiving->date->format('M d, Y') }}</td>
                            <td class="px-6 py-4">{{ $receiving->supplier->supplier_name }}</td>
                            <td class="px-6 py-4">{{ $receiving->total_items }}</td>
                            <td class="px-6 py-4 font-semibold">₱{{ number_format($receiving->total_cost, 2) }}</td>
                            <td class="px-6 py-4">
                                @if($receiving->status === 'Pending')
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif($receiving->status === 'Approved')
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">Approved</span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">Rejected</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('inventory-receiving.show', $receiving) }}" class="text-blue-600 hover:text-blue-800 text-sm font-semibold">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">No inventory receivings found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $receivings->links() }}
        </div>
    </div>
</div>
@endsection
