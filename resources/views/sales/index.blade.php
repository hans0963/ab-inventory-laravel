<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center w-full bg-cream p-4 rounded-xl shadow-sm border border-sienna border-opacity-20 gap-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna leading-tight text-center md:text-left">
                    {{ __('Sales Transactions') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-[10px] text-center md:text-left">Record and track bakeshop sales</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <a href="{{ route('sales.create') }}" class="w-full md:w-auto bg-terracotta hover:bg-terracotta-dark text-cream font-bold px-8 py-3 rounded-xl shadow-rustic transition-all hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center">
                    New Sale
                </a>
                <a href="{{ route('inventory.sales') }}" class="w-full md:w-auto bg-sienna hover:bg-sienna-dark text-cream font-bold px-8 py-3 rounded-xl shadow-rustic transition-all flex items-center justify-center">
                    Sales Summary Report
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6 md:space-y-8">
        <div class="card-rustic border-sienna overflow-hidden">
            {{-- Search & Filters --}}
            <div class="p-4 md:p-6 border-b border-sienna border-opacity-10">
                <form action="{{ route('sales.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative">
                        <input type="text" name="search" placeholder="Search receipt or product..." value="{{ request('search') }}"
                               class="w-full rounded-xl border-sienna border-opacity-20 bg-cream bg-opacity-20 py-3 pl-4 pr-4 focus:ring-2 focus:ring-terracotta focus:border-terracotta transition-all shadow-inner">
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
                            <th class="px-6 py-4 text-left">Date & Time</th>
                            <th class="px-6 py-4 text-left">Customer</th>
                            <th class="px-6 py-4 text-left">Items</th>
                            <th class="px-6 py-4 text-left">Payment Mode</th>
                            <th class="px-6 py-4 text-right">Total</th>
                            <th class="px-6 py-4 text-left">Cashier</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                        @forelse($sales as $sale)
                            <tr class="hover:bg-cream hover:bg-opacity-20 transition-colors whitespace-nowrap">
                                <td class="px-6 py-4 font-mono text-xs text-sienna font-bold">{{ $sale->receipt_number }}</td>
                                <td class="px-6 py-4 text-sage font-medium">{{ $sale->date->format('M d, Y h:i A') }}</td>
                                <td class="px-6 py-4 text-sienna font-medium">{{ $sale->customer->name ?? 'Walk-in' }}</td>
                                <td class="px-6 py-4 font-bold text-sienna">{{ $sale->product->product_name }} × {{ $sale->sold }}</td>
                                <td class="px-6 py-4 text-sage font-medium">{{ $sale->payment_mode_label }}</td>
                                <td class="px-6 py-4 text-right font-black text-terracotta text-base">₱{{ number_format($sale->total_amount, 2) }}</td>
                                <td class="px-6 py-4 text-sage text-[10px] uppercase font-bold tracking-tighter">{{ $sale->employee->user->name ?? $sale->employee->employee_name }}</td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-3">
                                        <a href="{{ route('sales.show', $sale->id) }}" class="px-3 py-2 bg-cream bg-opacity-80 text-sienna font-semibold rounded-xl hover:bg-opacity-100 transition-all" title="View Details">
                                            Details
                                        </a>
                                        <a href="{{ route('sales.print', $sale->id) }}" class="px-3 py-2 bg-sage bg-opacity-10 text-sage font-semibold rounded-xl hover:bg-sage hover:text-cream transition-all" title="Print Receipt">
                                            Receipt
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-sage italic">No sales transactions found.</td>
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
