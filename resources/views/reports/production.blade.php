<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 border-b-2 border-sienna pb-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="font-formal text-4xl text-sienna">{{ __('Production Summary') }}</h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">
                    Production volume, outflow, material use, and recent activity
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('reports.production', array_merge(request()->query(), ['export' => 'pdf'])) }}" target="_blank" class="btn-sage justify-center !py-2">PDF</a>
                <a href="{{ route('reports.production', array_merge(request()->query(), ['export' => 'excel'])) }}" class="btn-sienna justify-center !py-2">Excel</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="card-rustic border-sienna">
            <form method="GET" action="{{ route('reports.production') }}" class="grid grid-cols-1 gap-4 md:grid-cols-5 md:items-end">
                <div>
                    <x-input-label for="date_from" value="From" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="date_from" name="date_from" type="date" value="{{ $dateFrom }}" class="mt-1 block w-full !text-sm" />
                </div>
                <div>
                    <x-input-label for="date_to" value="To" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="date_to" name="date_to" type="date" value="{{ $dateTo }}" class="mt-1 block w-full !text-sm" />
                </div>
                <div>
                    <x-input-label for="type" value="Movement" class="text-[10px] uppercase tracking-widest text-sage" />
                    <select id="type" name="type" class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm">
                        <option value="all" {{ $type === 'all' ? 'selected' : '' }}>In and Out</option>
                        <option value="in" {{ $type === 'in' ? 'selected' : '' }}>In only</option>
                        <option value="out" {{ $type === 'out' ? 'selected' : '' }}>Out only</option>
                    </select>
                </div>
                <x-primary-button class="!py-2 md:col-span-1">Apply</x-primary-button>
                <a href="{{ route('reports.production') }}" class="btn-sienna justify-center !py-2 text-center">Reset</a>
            </form>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
            <x-stat-card title="Produced" :value="number_format($totalProductionIn)" icon="IN" border="sage" />
            <x-stat-card title="Moved Out" :value="number_format($totalProductionOut)" icon="OUT" border="terracotta" />
            <x-stat-card title="Net Output" :value="number_format($netProduction)" icon="NET" border="sienna" />
            <x-stat-card title="Active Days" :value="$activeDays" icon="DAYS" border="cream" />
        </div>

        <div class="grid grid-cols-1 gap-8 xl:grid-cols-3">
            <div class="card-rustic border-sage xl:col-span-2">
                <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div>
                        <p class="font-inter text-sage font-medium text-xs uppercase tracking-widest mb-2">Production Flow</p>
                        <h3 class="text-2xl font-lora font-bold text-sienna">In vs Out Balance</h3>
                        <p class="mt-2 text-sm text-sage">
                            {{ number_format($totalHandled) }} total units were handled in this period.
                            Outflow is {{ number_format($outflowRate, 1) }}% of production in.
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] uppercase tracking-widest text-sage">Net Quantity</p>
                        <p class="text-4xl font-formal font-bold {{ $netProduction >= 0 ? 'text-sage' : 'text-terracotta' }}">
                            {{ number_format($netProduction) }}
                        </p>
                    </div>
                </div>

                <div class="mt-6 space-y-4">
                    @php
                        $inPercent = $totalHandled > 0 ? round(($totalProductionIn / $totalHandled) * 100, 1) : 0;
                        $outPercent = $totalHandled > 0 ? round(($totalProductionOut / $totalHandled) * 100, 1) : 0;
                    @endphp
                    <div>
                        <div class="mb-2 flex justify-between text-xs font-bold uppercase tracking-widest text-sienna">
                            <span>Production In</span>
                            <span>{{ number_format($inPercent, 1) }}%</span>
                        </div>
                        <div class="h-4 overflow-hidden rounded-full bg-cream">
                            <div class="h-full rounded-full bg-sage" style="width: {{ $inPercent }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="mb-2 flex justify-between text-xs font-bold uppercase tracking-widest text-sienna">
                            <span>Production Out</span>
                            <span>{{ number_format($outPercent, 1) }}%</span>
                        </div>
                        <div class="h-4 overflow-hidden rounded-full bg-cream">
                            <div class="h-full rounded-full bg-terracotta" style="width: {{ $outPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-rustic border-terracotta">
                <p class="font-inter text-sage font-medium text-xs uppercase tracking-widest mb-4">Key Highlights</p>
                <div class="space-y-5">
                    <div>
                        <p class="text-xs uppercase tracking-widest text-sage">Top Produced</p>
                        <p class="mt-1 text-lg font-lora font-bold text-sienna">{{ $topProducedProduct->product ?? 'No production in' }}</p>
                        <p class="text-sm text-terracotta font-bold">{{ number_format($topProducedProduct->quantity ?? 0) }} units</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-widest text-sage">Top Outflow</p>
                        <p class="mt-1 text-lg font-lora font-bold text-sienna">{{ $topOutProduct->product ?? 'No production out' }}</p>
                        <p class="text-sm text-terracotta font-bold">{{ number_format($topOutProduct->quantity ?? 0) }} units</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-widest text-sage">Most Used Material</p>
                        <p class="mt-1 text-lg font-lora font-bold text-sienna">{{ $topConsumedMaterial->product ?? 'No material use' }}</p>
                        <p class="text-sm text-terracotta font-bold">{{ number_format($topConsumedMaterial->quantity ?? 0) }} units</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-widest text-sage">Main Out Reason</p>
                        <p class="mt-1 text-lg font-lora font-bold text-sienna">{{ $topWithdrawalReason->reason ?? 'No outflow reason' }}</p>
                        <p class="text-sm text-terracotta font-bold">{{ number_format($topWithdrawalReason->quantity ?? 0) }} units</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-rustic border-sienna overflow-hidden p-0">
            <div class="flex flex-col gap-1 border-b border-sienna border-opacity-10 p-6 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="font-inter text-sage font-medium text-xs uppercase tracking-widest">Recent Activity</p>
                    <h3 class="text-2xl font-lora font-bold text-sienna">Latest Production Movements</h3>
                </div>
                <p class="text-sm text-sage">{{ $dateFrom }} to {{ $dateTo }}</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-sienna text-cream uppercase text-xs tracking-widest">
                            <th class="px-4 py-3 text-left">Date</th>
                            <th class="px-4 py-3 text-left">Reference</th>
                            <th class="px-4 py-3 text-left">Movement</th>
                            <th class="px-4 py-3 text-left">Quantity</th>
                            <th class="px-4 py-3 text-left">Created By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                        @forelse($recentProductionActivity as $activity)
                            <tr class="hover:bg-cream hover:bg-opacity-20 transition-colors">
                                <td class="px-4 py-3 text-sienna">{{ $activity->date->format('M d, Y') }}</td>
                                <td class="px-4 py-3 font-bold text-sienna">{{ $activity->reference }}</td>
                                <td class="px-4 py-3">
                                    <span class="rounded-full px-3 py-1 text-xs font-black {{ $activity->type === 'IN' ? 'bg-sage text-white' : 'bg-terracotta text-white' }}">
                                        {{ $activity->type }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-bold text-terracotta">{{ number_format($activity->quantity) }}</td>
                                <td class="px-4 py-3 text-sienna">{{ $activity->employee }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500 italic">No production activity for this period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 xl:grid-cols-2">
            @include('reports.partials.simple-table', ['title' => 'Production by Period', 'rows' => $productionByPeriod, 'columns' => ['date' => 'Date', 'type' => 'Movement', 'quantity' => 'Quantity']])
            @include('reports.partials.simple-table', ['title' => 'Production by Product', 'rows' => $productionPerProduct, 'columns' => ['product' => 'Product', 'type' => 'Movement', 'quantity' => 'Quantity']])
            @include('reports.partials.simple-table', ['title' => 'Raw Materials Consumed', 'rows' => $rawMaterialsConsumed, 'columns' => ['product' => 'Raw Material', 'quantity' => 'Quantity']])
            @include('reports.partials.simple-table', ['title' => 'Outflow Reasons', 'rows' => $withdrawalReasons, 'columns' => ['reason' => 'Reason', 'count' => 'Records', 'quantity' => 'Quantity']])
            @include('reports.partials.simple-table', ['title' => 'Production by Employee', 'rows' => $productionByEmployee, 'columns' => ['employee' => 'Employee', 'quantity' => 'Quantity']])
        </div>
    </div>
</x-app-layout>
