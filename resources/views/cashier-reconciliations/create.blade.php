<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 border-b-2 border-sienna pb-4 md:flex-row md:items-end md:justify-between">
            <div>
                <h2 class="font-formal text-4xl text-sienna">{{ __('New Reconciliation') }}</h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">
                    {{ $employee->employee_name }} | {{ $dateFrom }} to {{ $dateTo }}
                </p>
            </div>
            <a href="{{ route('cashier-reconciliations.index') }}" class="btn-sienna">History</a>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="card-rustic border-sienna">
            <form method="GET" action="{{ route('cashier-reconciliations.create') }}" class="grid grid-cols-1 gap-4 md:grid-cols-5 md:items-end">
                <div>
                    <x-input-label for="period_type" value="Period" class="text-[10px] uppercase tracking-widest text-sage" />
                    <select id="period_type" name="period_type" class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm text-sm">
                        @foreach(['daily' => 'Daily', 'monthly' => 'Monthly', 'yearly' => 'Yearly', 'custom' => 'Custom'] as $value => $label)
                            <option value="{{ $value }}" {{ $periodType === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="date" value="Base Date" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="date" name="date" type="date" value="{{ request('date', $dateFrom) }}" class="mt-1 block w-full !text-sm" />
                </div>
                <div>
                    <x-input-label for="date_from" value="Custom From" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="date_from" name="date_from" type="date" value="{{ $dateFrom }}" class="mt-1 block w-full !text-sm" />
                </div>
                <div>
                    <x-input-label for="date_to" value="Custom To" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="date_to" name="date_to" type="date" value="{{ $dateTo }}" class="mt-1 block w-full !text-sm" />
                </div>
                <x-primary-button class="!py-2">Load Totals</x-primary-button>
            </form>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
            <x-stat-card title="Cash Sales" :value="'PHP ' . number_format($summary['cash_sales_total'], 2)" icon="Cash" border="sage" />
            <x-stat-card title="Non-Cash Sales" :value="'PHP ' . number_format($summary['non_cash_sales_total'], 2)" icon="Other" border="sienna" />
            <x-stat-card title="Transactions" :value="$summary['transaction_count']" icon="Count" border="terracotta" />
            <x-stat-card title="Grand Total" :value="'PHP ' . number_format($summary['total_sales'], 2)" icon="Total" border="cream" />
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <div class="rounded-lg bg-white border border-sienna border-opacity-10 p-4">
                <p class="text-xs font-black uppercase tracking-widest text-sage">E-Wallet</p>
                <p class="mt-1 text-xl font-bold text-sienna">PHP {{ number_format($summary['ewallet_total'], 2) }}</p>
            </div>
            <div class="rounded-lg bg-white border border-sienna border-opacity-10 p-4">
                <p class="text-xs font-black uppercase tracking-widest text-sage">Card</p>
                <p class="mt-1 text-xl font-bold text-sienna">PHP {{ number_format($summary['card_total'], 2) }}</p>
            </div>
            <div class="rounded-lg bg-white border border-sienna border-opacity-10 p-4">
                <p class="text-xs font-black uppercase tracking-widest text-sage">Credit / Loan</p>
                <p class="mt-1 text-xl font-bold text-sienna">PHP {{ number_format($summary['credit_total'], 2) }}</p>
            </div>
            <div class="rounded-lg bg-white border border-sienna border-opacity-10 p-4">
                <p class="text-xs font-black uppercase tracking-widest text-sage">Voided Cash</p>
                <p class="mt-1 text-xl font-bold text-terracotta">PHP {{ number_format($summary['voided_cash_total'], 2) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 xl:grid-cols-3">
            <div class="card-rustic border-terracotta xl:col-span-1">
                <h3 class="text-2xl font-lora font-bold text-sienna mb-6">Cash Count</h3>
                <form method="POST" action="{{ route('cashier-reconciliations.store') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="period_type" value="{{ $periodType }}">
                    <input type="hidden" name="date_from" value="{{ $dateFrom }}">
                    <input type="hidden" name="date_to" value="{{ $dateTo }}">

                    <div>
                        <x-input-label for="opening_cash" value="Opening Cash" class="text-[10px] uppercase tracking-widest text-sage" />
                        <x-text-input id="opening_cash" name="opening_cash" type="number" min="0" step="0.01" value="{{ old('opening_cash', 0) }}" class="mt-1 block w-full" required />
                    </div>
                    <div>
                        <x-input-label for="actual_cash_count" value="Actual Cash Count" class="text-[10px] uppercase tracking-widest text-sage" />
                        <x-text-input id="actual_cash_count" name="actual_cash_count" type="number" min="0" step="0.01" value="{{ old('actual_cash_count') }}" class="mt-1 block w-full" required />
                    </div>
                    <div class="rounded-lg bg-cream bg-opacity-50 p-4">
                        <p class="text-xs font-black uppercase tracking-widest text-sage">System Cash Sales</p>
                        <p class="mt-1 text-3xl font-formal font-bold text-sienna">PHP {{ number_format($summary['cash_sales_total'], 2) }}</p>
                    </div>
                    <div>
                        <x-input-label for="cashier_notes" value="Notes" class="text-[10px] uppercase tracking-widest text-sage" />
                        <textarea id="cashier_notes" name="cashier_notes" rows="4" class="mt-1 block w-full rounded-md border-sienna border-opacity-20 shadow-sm focus:border-sienna focus:ring-sienna">{{ old('cashier_notes') }}</textarea>
                    </div>
                    <x-primary-button class="w-full justify-center !py-3">Submit Reconciliation</x-primary-button>
                </form>
            </div>

            <div class="card-rustic border-sienna xl:col-span-2 overflow-hidden p-0">
                <div class="border-b border-sienna border-opacity-10 p-6">
                    <p class="text-xs font-black uppercase tracking-widest text-sage">Sales Included</p>
                    <h3 class="text-2xl font-lora font-bold text-sienna">Recent Transactions</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-sienna text-cream uppercase text-xs tracking-widest">
                                <th class="px-4 py-3 text-left">Receipt</th>
                                <th class="px-4 py-3 text-left">Product</th>
                                <th class="px-4 py-3 text-center">Qty</th>
                                <th class="px-4 py-3 text-right">Unit Price</th>
                                <th class="px-4 py-3 text-left">Payment</th>
                                <th class="px-4 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                            @forelse($recentSales as $sale)
                                <tr>
                                    <td class="px-4 py-3 font-mono text-xs text-sienna">{{ $sale->receipt_number }}</td>
                                    <td class="px-4 py-3 text-sienna font-bold">{{ $sale->product->product_name ?? 'Unknown' }}</td>
                                    <td class="px-4 py-3 text-center font-bold text-sienna">{{ number_format($sale->sold) }}</td>
                                    <td class="px-4 py-3 text-right text-sienna">PHP {{ number_format($sale->unit_price ?: ($sale->product->selling_price ?? 0), 2) }}</td>
                                    <td class="px-4 py-3 text-sage">{{ $sale->payment_mode_label }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-terracotta">PHP {{ number_format($sale->total_amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-sage italic">No sales found for this period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-cream">
                            <tr>
                                <td colspan="5" class="px-4 py-4 text-right font-black uppercase tracking-widest text-sienna">Grand Total</td>
                                <td class="px-4 py-4 text-right font-black text-terracotta">PHP {{ number_format($summary['total_sales'], 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
