<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 border-b-2 border-sienna pb-4 md:flex-row md:items-end md:justify-between">
            <div>
                <h2 class="font-formal text-4xl text-sienna">{{ __('Cashier Reconciliation') }}</h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">
                    Compare counted cash against system sales totals
                </p>
            </div>
            @if(auth()->user()->isCashier())
                <a href="{{ route('cashier-reconciliations.create') }}" class="btn-sage">New Reconciliation</a>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(auth()->user()->hasRole(['admin', 'manager']))
            <div class="card-rustic border-sienna">
                <form method="GET" action="{{ route('cashier-reconciliations.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4 md:items-end">
                    <div>
                        <x-input-label for="employee_id" value="Cashier" class="text-[10px] uppercase tracking-widest text-sage" />
                        <select id="employee_id" name="employee_id" class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm text-sm">
                            <option value="">All cashiers</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->employee_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="status" value="Status" class="text-[10px] uppercase tracking-widest text-sage" />
                        <select id="status" name="status" class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm text-sm">
                            <option value="">All statuses</option>
                            @foreach(['Submitted', 'Approved', 'Flagged'] as $status)
                                <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-primary-button class="!py-2">Filter</x-primary-button>
                    <a href="{{ route('cashier-reconciliations.index') }}" class="btn-sienna justify-center !py-2 text-center">Reset</a>
                </form>
            </div>
        @endif

        <div class="card-rustic border-sienna overflow-hidden p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-sienna text-cream uppercase text-xs tracking-widest">
                            <th class="px-6 py-4 text-left">Period</th>
                            <th class="px-6 py-4 text-left">Cashier</th>
                            <th class="px-6 py-4 text-center">Transactions</th>
                            <th class="px-6 py-4 text-right">Grand Total</th>
                            <th class="px-6 py-4 text-right">Expected Cash</th>
                            <th class="px-6 py-4 text-right">Actual Cash</th>
                            <th class="px-6 py-4 text-right">Variance</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                        @forelse($reconciliations as $reconciliation)
                            <tr class="hover:bg-cream hover:bg-opacity-20 transition-colors">
                                <td class="px-6 py-4 text-sienna">
                                    <p class="font-bold">{{ ucfirst($reconciliation->period_type) }}</p>
                                    <p class="text-xs text-sage">{{ $reconciliation->date_from->format('M d, Y') }} - {{ $reconciliation->date_to->format('M d, Y') }}</p>
                                </td>
                                <td class="px-6 py-4 text-sienna font-bold">{{ $reconciliation->employee->employee_name ?? 'Unknown' }}</td>
                                <td class="px-6 py-4 text-center font-bold text-sienna">{{ $reconciliation->transaction_count }}</td>
                                <td class="px-6 py-4 text-right font-bold text-sienna">PHP {{ number_format($reconciliation->total_sales, 2) }}</td>
                                <td class="px-6 py-4 text-right font-bold text-sienna">PHP {{ number_format($reconciliation->expected_cash, 2) }}</td>
                                <td class="px-6 py-4 text-right font-bold text-sienna">PHP {{ number_format($reconciliation->actual_cash_count, 2) }}</td>
                                <td class="px-6 py-4 text-right font-black {{ $reconciliation->variance == 0 ? 'text-sage' : 'text-terracotta' }}">
                                    PHP {{ number_format($reconciliation->variance, 2) }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-widest {{ $reconciliation->status === 'Approved' ? 'bg-sage text-white' : ($reconciliation->status === 'Flagged' ? 'bg-terracotta text-white' : 'bg-cream text-sienna') }}">
                                        {{ $reconciliation->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('cashier-reconciliations.show', $reconciliation) }}" class="text-sage font-bold hover:text-sienna">Details</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-sage italic">No reconciliation records yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-sienna border-opacity-10 p-6">
                {{ $reconciliations->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
