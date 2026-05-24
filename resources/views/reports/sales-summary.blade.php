<x-app-layout>
    @php
        $salesRoute = request()->routeIs('sales-report.index') ? 'sales-report.index' : 'inventory.sales';
    @endphp

    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">{{ __('Sales Summary') }}</h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Sales totals, product/category mix, cashier activity, and payment modes</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="card-rustic border-sienna">
            <form method="GET" action="{{ route($salesRoute) }}" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                <div>
                    <x-input-label for="date_from" value="From" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="date_from" name="date_from" type="date" value="{{ $dateFrom }}" class="mt-1 block w-full !text-sm" />
                </div>
                <div>
                    <x-input-label for="date_to" value="To" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="date_to" name="date_to" type="date" value="{{ $dateTo }}" class="mt-1 block w-full !text-sm" />
                </div>
                <div>
                    <x-input-label for="group_by" value="Total Sales Per" class="text-[10px] uppercase tracking-widest text-sage" />
                    <select id="group_by" name="group_by" class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm">
                        <option value="day" {{ $groupBy === 'day' ? 'selected' : '' }}>Day</option>
                        <option value="week" {{ $groupBy === 'week' ? 'selected' : '' }}>Week</option>
                        <option value="month" {{ $groupBy === 'month' ? 'selected' : '' }}>Month</option>
                    </select>
                </div>
                <x-primary-button class="!py-2">Filter</x-primary-button>
                <a href="{{ route($salesRoute, array_merge(request()->query(), ['export' => 'pdf'])) }}" target="_blank" class="btn-sage justify-center !py-2">PDF</a>
                <a href="{{ route($salesRoute, array_merge(request()->query(), ['export' => 'excel'])) }}" class="btn-sienna justify-center !py-2">Excel</a>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <x-stat-card title="Total Sales" :value="'PHP ' . number_format($totalSales, 2)" icon="Sales" border="terracotta" />
            <x-stat-card title="Transactions" :value="$totalOrders" icon="Orders" border="sienna" />
            <x-stat-card title="Average Sale" :value="'PHP ' . number_format($averageOrder, 2)" icon="Avg" border="sage" />
            <x-stat-card title="Discounts" :value="'PHP ' . number_format($discounts, 2)" icon="Disc" border="cream" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="card-rustic border-sienna">
                <h3 class="text-xl font-lora font-bold text-sienna mb-4">Total Sales Per {{ ucfirst($groupBy) }}</h3>
                <div class="overflow-x-auto rounded-xl border border-sienna border-opacity-10">
                    <table class="min-w-full text-sm">
                        <thead><tr class="bg-sienna text-cream uppercase text-xs tracking-widest"><th class="px-4 py-3 text-left">Period</th><th class="px-4 py-3 text-center">Transactions</th><th class="px-4 py-3 text-right">Total</th></tr></thead>
                        <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                            @forelse($salesByPeriod as $row)
                                <tr><td class="px-4 py-3 font-bold text-sienna">{{ $row->label }}</td><td class="px-4 py-3 text-center">{{ $row->transactions }}</td><td class="px-4 py-3 text-right font-black text-terracotta">PHP {{ number_format($row->total, 2) }}</td></tr>
                            @empty
                                <tr><td colspan="3" class="px-4 py-8 text-center text-gray-500 italic">No sales in this range.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-rustic border-sage">
                <h3 class="text-xl font-lora font-bold text-sienna mb-4">Payment Mode Breakdown</h3>
                <div class="space-y-3">
                    @forelse($paymentBreakdown as $row)
                        <div class="flex justify-between rounded-lg bg-cream bg-opacity-40 p-4"><span class="font-bold text-sienna">{{ $row->payment_type }}</span><span class="font-black text-terracotta">{{ $row->transactions }} | PHP {{ number_format($row->revenue, 2) }}</span></div>
                    @empty
                        <p class="text-gray-500 italic">No payment data.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            @include('reports.partials.simple-table', ['title' => 'Sales per Product', 'rows' => $salesPerProduct, 'columns' => ['name' => 'Product', 'units_sold' => 'Units', 'revenue' => 'Revenue']])
            @include('reports.partials.simple-table', ['title' => 'Sales per Category', 'rows' => $salesPerCategory, 'columns' => ['category_name' => 'Category', 'revenue' => 'Revenue']])
            @include('reports.partials.simple-table', ['title' => 'Sales per Cashier', 'rows' => $salesPerCashier, 'columns' => ['cashier' => 'Cashier', 'transactions' => 'Transactions', 'revenue' => 'Revenue']])
            @include('reports.partials.simple-table', ['title' => 'Best Selling Products', 'rows' => $bestSellingProducts, 'columns' => ['name' => 'Product', 'units_sold' => 'Units Sold', 'revenue' => 'Revenue']])
        </div>
    </div>
</x-app-layout>
