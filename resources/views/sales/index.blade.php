<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center w-full bg-cream p-4 rounded-xl shadow-sm border border-sienna border-opacity-20 gap-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna leading-tight text-center md:text-left">
                    {{ __('Sales Transactions') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-[10px] text-center md:text-left">Record and track bakeshop sales</p>
            </div>
            <a href="{{ route('sales.create') }}" class="w-full md:w-auto bg-terracotta hover:bg-terracotta-dark text-cream font-bold px-8 py-3 rounded-xl shadow-rustic transition-all hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center">
                <span class="mr-2 text-xl">+</span> New Sale
            </a>
        </div>
    </x-slot>

    <div class="space-y-6 md:space-y-8">
        <div class="card-rustic border-sienna overflow-hidden">
            {{-- Search & Filters --}}
            <div class="p-4 md:p-6 border-b border-sienna border-opacity-10">
                <form action="{{ route('sales.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative">
                        <input type="text" name="search" placeholder="Search receipt or product..." value="{{ request('search') }}"
                               class="w-full rounded-xl border-sienna border-opacity-20 bg-cream bg-opacity-20 py-3 pl-12 pr-4 focus:ring-2 focus:ring-terracotta focus:border-terracotta transition-all shadow-inner">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-sienna opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                    <button type="submit" class="w-full md:w-auto bg-sienna hover:bg-sienna-dark text-cream font-bold px-8 py-3 rounded-xl shadow-md transition-all">
                        Search
                    </button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-sienna text-cream uppercase text-[10px] tracking-widest font-black whitespace-nowrap">
                            <th class="px-6 py-4 text-left">Receipt No.</th>
                            <th class="px-6 py-4 text-left">Date</th>
                            <th class="px-6 py-4 text-left">Product</th>
                            <th class="px-6 py-4 text-center">Qty</th>
                            <th class="px-6 py-4 text-right">Discount</th>
                            <th class="px-6 py-4 text-right">VAT</th>
                            <th class="px-6 py-4 text-right">Total</th>
                            <th class="px-6 py-4 text-left">Cashier</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                        @forelse($sales as $sale)
                            <tr class="hover:bg-cream hover:bg-opacity-20 transition-colors whitespace-nowrap">
                                <td class="px-6 py-4 font-mono text-xs text-sienna font-bold">{{ $sale->receipt_number }}</td>
                                <td class="px-6 py-4 text-sage font-medium">{{ $sale->date->format('M d, Y') }}</td>
                                <td class="px-6 py-4 font-bold text-sienna">{{ $sale->product->product_name }}</td>
                                <td class="px-6 py-4 text-center font-black">{{ $sale->sold }}</td>
                                <td class="px-6 py-4 text-right text-red-600 font-medium">₱{{ number_format($sale->discount_amount, 2) }}</td>
                                <td class="px-6 py-4 text-right text-green-600 font-medium">₱{{ number_format($sale->vat_amount, 2) }}</td>
                                <td class="px-6 py-4 text-right font-black text-terracotta text-base">₱{{ number_format($sale->total_amount, 2) }}</td>
                                <td class="px-6 py-4 text-sage text-[10px] uppercase font-bold tracking-tighter">{{ $sale->employee->user->name ?? $sale->employee->employee_name }}</td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-3">
                                        <a href="{{ route('sales.show', $sale->id) }}" class="p-2 text-sage hover:text-sienna hover:bg-sage hover:bg-opacity-10 rounded-lg transition-all" title="View Details">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </a>
                                        <a href="{{ route('sales.print', $sale->id) }}" class="p-2 text-sage hover:text-terracotta hover:bg-terracotta hover:bg-opacity-10 rounded-lg transition-all" title="Print Receipt">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-sage italic">No sales transactions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-4 md:p-6 border-t border-sienna border-opacity-10">
                {{ $sales->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
