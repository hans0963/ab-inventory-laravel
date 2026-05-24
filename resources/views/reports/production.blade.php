<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">{{ __('Production Report') }}</h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Merged Production IN and OUT reporting</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="card-rustic border-sienna">
            <form method="GET" action="{{ route('reports.production') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                <div>
                    <x-input-label for="date_from" value="From" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="date_from" name="date_from" type="date" value="{{ $dateFrom }}" class="mt-1 block w-full !text-sm" />
                </div>
                <div>
                    <x-input-label for="date_to" value="To" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="date_to" name="date_to" type="date" value="{{ $dateTo }}" class="mt-1 block w-full !text-sm" />
                </div>
                <div>
                    <x-input-label for="type" value="Type" class="text-[10px] uppercase tracking-widest text-sage" />
                    <select id="type" name="type" class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm">
                        <option value="all" {{ $type === 'all' ? 'selected' : '' }}>IN and OUT</option>
                        <option value="in" {{ $type === 'in' ? 'selected' : '' }}>IN</option>
                        <option value="out" {{ $type === 'out' ? 'selected' : '' }}>OUT</option>
                    </select>
                </div>
                <x-primary-button class="!py-2">Filter</x-primary-button>
                <a href="{{ route('reports.production', array_merge(request()->query(), ['export' => 'pdf'])) }}" target="_blank" class="btn-sage justify-center !py-2">PDF</a>
                <a href="{{ route('reports.production', array_merge(request()->query(), ['export' => 'excel'])) }}" class="btn-sienna justify-center !py-2">Excel</a>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-stat-card title="Production IN" :value="$totalProductionIn" icon="IN" border="sage" />
            <x-stat-card title="Production OUT" :value="$totalProductionOut" icon="OUT" border="terracotta" />
            <x-stat-card title="Net Quantity" :value="$totalProductionIn - $totalProductionOut" icon="Net" border="sienna" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            @include('reports.partials.simple-table', ['title' => 'Total Production per Period', 'rows' => $productionByPeriod, 'columns' => ['date' => 'Date', 'type' => 'Type', 'quantity' => 'Quantity']])
            @include('reports.partials.simple-table', ['title' => 'Production per Product', 'rows' => $productionPerProduct, 'columns' => ['product' => 'Product', 'type' => 'Type', 'quantity' => 'Quantity']])
            @include('reports.partials.simple-table', ['title' => 'Raw Materials Consumed', 'rows' => $rawMaterialsConsumed, 'columns' => ['product' => 'Raw Material', 'quantity' => 'Quantity']])
            @include('reports.partials.simple-table', ['title' => 'Withdrawal Reasons Breakdown', 'rows' => $withdrawalReasons, 'columns' => ['reason' => 'Reason', 'count' => 'Records', 'quantity' => 'Quantity']])
            @include('reports.partials.simple-table', ['title' => 'Production by Employee', 'rows' => $productionByEmployee, 'columns' => ['employee' => 'Employee', 'quantity' => 'Quantity']])
        </div>
    </div>
</x-app-layout>
