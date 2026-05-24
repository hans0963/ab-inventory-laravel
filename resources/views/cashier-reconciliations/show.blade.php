<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 border-b-2 border-sienna pb-4 md:flex-row md:items-end md:justify-between">
            <div>
                <h2 class="font-formal text-4xl text-sienna">{{ __('Reconciliation Details') }}</h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">
                    {{ $cashierReconciliation->employee->employee_name ?? 'Unknown Cashier' }} | {{ $cashierReconciliation->date_from->format('M d, Y') }} - {{ $cashierReconciliation->date_to->format('M d, Y') }}
                </p>
            </div>
            <a href="{{ route('cashier-reconciliations.index') }}" class="btn-sienna">Back to History</a>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
            <x-stat-card title="Expected Cash" :value="'PHP ' . number_format($cashierReconciliation->expected_cash, 2)" icon="Expected" border="sage" />
            <x-stat-card title="Actual Cash" :value="'PHP ' . number_format($cashierReconciliation->actual_cash_count, 2)" icon="Actual" border="sienna" />
            <x-stat-card title="Variance" :value="'PHP ' . number_format($cashierReconciliation->variance, 2)" icon="Diff" :border="$cashierReconciliation->variance == 0 ? 'sage' : 'terracotta'" />
            <x-stat-card title="Grand Total" :value="'PHP ' . number_format($cashierReconciliation->total_sales, 2)" icon="Total" border="cream" />
        </div>

        <div class="grid grid-cols-1 gap-8 xl:grid-cols-3">
            <div class="card-rustic border-sienna xl:col-span-2">
                <h3 class="text-2xl font-lora font-bold text-sienna mb-6">Cash Breakdown</h3>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    @foreach([
                        'Opening Cash' => $cashierReconciliation->opening_cash,
                        'Cash Sales' => $cashierReconciliation->cash_sales_total,
                        'E-Wallet Sales' => $summary['ewallet_total'],
                        'Card Sales' => $summary['card_total'],
                        'Credit / Loan Sales' => $summary['credit_total'],
                        'Non-Cash Sales' => $cashierReconciliation->non_cash_sales_total,
                        'Voided Cash Sales' => $cashierReconciliation->voided_cash_total,
                        'Total Sales' => $cashierReconciliation->total_sales,
                        'Expected Cash' => $cashierReconciliation->expected_cash,
                    ] as $label => $amount)
                        <div class="rounded-lg bg-cream bg-opacity-40 p-4">
                            <p class="text-xs font-black uppercase tracking-widest text-sage">{{ $label }}</p>
                            <p class="mt-1 text-2xl font-bold text-sienna">PHP {{ number_format($amount, 2) }}</p>
                        </div>
                    @endforeach
                </div>
                @if($cashierReconciliation->cashier_notes)
                    <div class="mt-6 rounded-lg border border-sienna border-opacity-10 p-4">
                        <p class="text-xs font-black uppercase tracking-widest text-sage">Cashier Notes</p>
                        <p class="mt-2 text-sienna">{{ $cashierReconciliation->cashier_notes }}</p>
                    </div>
                @endif
            </div>

            <div class="card-rustic border-terracotta">
                <h3 class="text-2xl font-lora font-bold text-sienna mb-4">Review Status</h3>
                <p class="mb-4 inline-block rounded-full px-3 py-1 text-xs font-black uppercase tracking-widest {{ $cashierReconciliation->status === 'Approved' ? 'bg-sage text-white' : ($cashierReconciliation->status === 'Flagged' ? 'bg-terracotta text-white' : 'bg-cream text-sienna') }}">
                    {{ $cashierReconciliation->status }}
                </p>

                @if($cashierReconciliation->reviewer)
                    <p class="text-sm text-sage">Reviewed by {{ $cashierReconciliation->reviewer->name }} on {{ $cashierReconciliation->reviewed_at?->format('M d, Y h:i A') }}</p>
                    @if($cashierReconciliation->review_notes)
                        <p class="mt-3 text-sm text-sienna">{{ $cashierReconciliation->review_notes }}</p>
                    @endif
                @endif

                @if(auth()->user()->hasRole(['admin', 'manager']))
                    <form method="POST" action="{{ route('cashier-reconciliations.review', $cashierReconciliation) }}" class="mt-6 space-y-4">
                        @csrf
                        @method('PATCH')
                        <div>
                            <x-input-label for="status" value="Review Decision" class="text-[10px] uppercase tracking-widest text-sage" />
                            <select id="status" name="status" class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm text-sm">
                                <option value="Approved">Approve</option>
                                <option value="Flagged">Flag</option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="review_notes" value="Review Notes" class="text-[10px] uppercase tracking-widest text-sage" />
                            <textarea id="review_notes" name="review_notes" rows="4" class="mt-1 block w-full rounded-md border-sienna border-opacity-20 shadow-sm focus:border-sienna focus:ring-sienna">{{ old('review_notes', $cashierReconciliation->review_notes) }}</textarea>
                        </div>
                        <x-primary-button class="w-full justify-center !py-3">Save Review</x-primary-button>
                    </form>
                @endif
            </div>
        </div>

        <div class="card-rustic border-sienna overflow-hidden p-0">
            <div class="border-b border-sienna border-opacity-10 p-6">
                <p class="text-xs font-black uppercase tracking-widest text-sage">Transaction Reference</p>
                <h3 class="text-2xl font-lora font-bold text-sienna">Sales Included in This Reconciliation</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-sienna text-cream uppercase text-xs tracking-widest">
                            <th class="px-4 py-3 text-left">Date</th>
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
                                <td class="px-4 py-3 text-sage">{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                                <td class="px-4 py-3 font-mono text-xs text-sienna">{{ $sale->receipt_number }}</td>
                                <td class="px-4 py-3 text-sienna font-bold">{{ $sale->product->product_name ?? 'Unknown' }}</td>
                                <td class="px-4 py-3 text-center font-bold text-sienna">{{ number_format($sale->sold) }}</td>
                                <td class="px-4 py-3 text-right text-sienna">PHP {{ number_format($sale->unit_price ?: ($sale->product->selling_price ?? 0), 2) }}</td>
                                <td class="px-4 py-3 text-sage">{{ $sale->payment_mode_label }}</td>
                                <td class="px-4 py-3 text-right font-bold text-terracotta">PHP {{ number_format($sale->total_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-sage italic">No sales found for this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-cream">
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-right font-black uppercase tracking-widest text-sienna">Grand Total</td>
                            <td class="px-4 py-4 text-right font-black text-terracotta">PHP {{ number_format($cashierReconciliation->total_sales, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
