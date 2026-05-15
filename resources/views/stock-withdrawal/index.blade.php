<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ __('Stock Withdrawals') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Internal use, damaged goods, and wastage tracking</p>
            </div>
            <a href="{{ route('stock-withdrawal.create') }}" class="bg-sienna text-cream px-6 py-3 rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-opacity-90 transition">
                + New Withdrawal
            </a>
        </div>
    </x-slot>

    <div class="space-y-8">
        <!-- Filters -->
        <div class="card-rustic border-sage">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <x-input-label for="status" value="Status" class="text-[10px] uppercase tracking-widest text-sage" />
                    <select name="status" class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm">
                        <option value="">All Statuses</option>
                        <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Approved" {{ request('status') === 'Approved' ? 'selected' : '' }}>Approved</option>
                        <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="date_from" value="Date From" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input type="date" name="date_from" value="{{ request('date_from') }}" class="mt-1 block w-full !text-sm" />
                </div>
                <div>
                    <x-input-label for="date_to" value="Date To" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input type="date" name="date_to" value="{{ request('date_to') }}" class="mt-1 block w-full !text-sm" />
                </div>
                <div class="flex space-x-2">
                    <x-primary-button class="flex-1 justify-center py-2">Filter</x-primary-button>
                    <a href="{{ route('stock-withdrawal.index') }}" class="flex-1 text-center bg-cream border border-sienna border-opacity-20 text-sienna px-4 py-2 rounded-md text-xs font-bold uppercase tracking-widest hover:bg-opacity-50 transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="card-rustic border-sienna overflow-hidden p-0">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-sienna text-cream uppercase text-[10px] tracking-widest font-black">
                        <th class="px-6 py-4 text-left">Ref #</th>
                        <th class="px-6 py-4 text-left">Date</th>
                        <th class="px-6 py-4 text-left">Reason</th>
                        <th class="px-6 py-4 text-center">Items</th>
                        <th class="px-6 py-4 text-right">Total Value</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-left">Encoded By</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                    @forelse($withdrawals as $withdrawal)
                        <tr class="hover:bg-cream hover:bg-opacity-20 transition">
                            <td class="px-6 py-4 font-bold text-sienna">{{ $withdrawal->withdrawal_no }}</td>
                            <td class="px-6 py-4 text-sage">{{ $withdrawal->date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-sienna font-medium">{{ $withdrawal->reason }}</td>
                            <td class="px-6 py-4 text-center font-medium">{{ $withdrawal->items->count() }}</td>
                            <td class="px-6 py-4 text-right font-black text-terracotta">₱{{ number_format($withdrawal->total_value, 2) }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-[9px] px-2 py-0.5 rounded-full uppercase font-black tracking-widest 
                                    {{ $withdrawal->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : ($withdrawal->status === 'Approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $withdrawal->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sage">{{ $withdrawal->createdBy->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-center space-x-2">
                                <a href="{{ route('stock-withdrawal.show', $withdrawal->id) }}" class="text-sienna hover:text-terracotta transition font-bold">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-sage italic">No withdrawals found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-4 border-t border-sienna border-opacity-10">
                {{ $withdrawals->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
