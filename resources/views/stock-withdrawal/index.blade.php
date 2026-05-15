@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-amber-50 to-orange-50 py-12 px-4">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-800">Stock Withdrawals</h1>
                <p class="text-gray-600 mt-2">Manage stock removals for damage, expiry, waste, and internal use</p>
            </div>
            <a href="{{ route('stock-withdrawal.create') }}" class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-6 py-3 rounded-lg hover:shadow-lg transition-shadow">
                + New Withdrawal
            </a>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        <option value="">All Statuses</option>
                        <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Approved" {{ request('status') === 'Approved' ? 'selected' : '' }}>Approved</option>
                        <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                    <select name="reason" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                        <option value="">All Reasons</option>
                        <option value="Internal Use" {{ request('reason') === 'Internal Use' ? 'selected' : '' }}>Internal Use</option>
                        <option value="Damaged" {{ request('reason') === 'Damaged' ? 'selected' : '' }}>Damaged</option>
                        <option value="Expired" {{ request('reason') === 'Expired' ? 'selected' : '' }}>Expired</option>
                        <option value="Wastage" {{ request('reason') === 'Wastage' ? 'selected' : '' }}>Wastage</option>
                        <option value="Other" {{ request('reason') === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Filter
                    </button>
                </div>
                <div class="flex items-end">
                    <a href="{{ route('stock-withdrawal.index') }}" class="w-full text-center bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Reference #</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Date</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Reason</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Qty</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-gray-700">Total Value</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($withdrawals as $withdrawal)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $withdrawal->withdrawal_no }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $withdrawal->date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $withdrawal->reason }}</td>
                            <td class="px-6 py-4 text-sm text-right text-gray-600">{{ $withdrawal->total_quantity }}</td>
                            <td class="px-6 py-4 text-sm text-right text-gray-600">₱{{ number_format($withdrawal->total_value, 2) }}</td>
                            <td class="px-6 py-4 text-sm">
                                @if($withdrawal->status === 'Pending')
                                    <span class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">Pending</span>
                                @elseif($withdrawal->status === 'Approved')
                                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">Approved</span>
                                @else
                                    <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-semibold">Rejected</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-sm">
                                <a href="{{ route('stock-withdrawal.show', $withdrawal->id) }}" class="text-blue-600 hover:text-blue-800 font-semibold">View</a>
                                @if($withdrawal->status === 'Pending')
                                    <form action="{{ route('stock-withdrawal.destroy', $withdrawal->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-semibold ml-3">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                No withdrawals found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $withdrawals->links() }}
        </div>
    </div>
</div>
@endsection
