<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ __('Production Management') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Unified view for Production IN and Production OUT batches</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('production-management.index', ['tab' => 'in']) }}" class="px-5 py-3 rounded-lg border border-sienna text-sienna text-xs font-bold uppercase tracking-widest {{ $tab === 'in' ? 'bg-sienna text-cream' : 'bg-cream' }}">
                    Production IN
                </a>
                <a href="{{ route('production-management.index', ['tab' => 'out']) }}" class="px-5 py-3 rounded-lg border border-sienna text-sienna text-xs font-bold uppercase tracking-widest {{ $tab === 'out' ? 'bg-sienna text-cream' : 'bg-cream' }}">
                    Production OUT
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="card-rustic border-sienna p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <p class="text-sienna uppercase tracking-widest text-[11px] font-black">Current tab</p>
                    <h3 class="text-xl font-bold text-sienna mt-2">{{ $tab === 'out' ? 'Production OUT' : 'Production IN' }}</h3>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    @if($tab === 'in')
                        <a href="{{ route('production-in.create') }}" class="bg-sienna text-cream px-6 py-3 rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-opacity-90 transition">
                            New Production IN
                        </a>
                    @else
                        <a href="{{ route('production-out.create') }}" class="bg-sienna text-cream px-6 py-3 rounded-lg text-xs font-bold uppercase tracking-widest hover:bg-opacity-90 transition">
                            New Production OUT
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-rustic border-sage">
            <form method="GET" action="{{ route('production-management.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end p-6">
                <input type="hidden" name="tab" value="{{ $tab }}" />

                <div>
                    <x-input-label for="status" value="Status" class="text-[10px] uppercase tracking-widest text-sage" />
                    <select id="status" name="status" class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm">
                        <option value="">All Statuses</option>
                        <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Approved" {{ request('status') === 'Approved' ? 'selected' : '' }}>Approved</option>
                        <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div>
                    <x-input-label for="date_from" value="Date From" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="date_from" name="date_from" type="date" value="{{ request('date_from') }}" class="mt-1 block w-full !text-sm" />
                </div>

                <div>
                    <x-input-label for="date_to" value="Date To" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="date_to" name="date_to" type="date" value="{{ request('date_to') }}" class="mt-1 block w-full !text-sm" />
                </div>

                <div class="flex gap-3">
                    <x-primary-button class="w-full justify-center py-2">Filter</x-primary-button>
                    <a href="{{ route('production-management.index', ['tab' => $tab]) }}" class="w-full text-center bg-cream border border-sienna border-opacity-20 text-sienna px-4 py-2 rounded-md text-xs font-bold uppercase tracking-widest hover:bg-opacity-50 transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="card-rustic border-sienna overflow-hidden p-0">
            @if($tab === 'out')
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-sienna text-cream uppercase text-[10px] tracking-widest font-black">
                            <th class="px-6 py-4 text-left">Batch #</th>
                            <th class="px-6 py-4 text-left">Date</th>
                            <th class="px-6 py-4 text-left">Reason</th>
                            <th class="px-6 py-4 text-left">Products</th>
                            <th class="px-6 py-4 text-center">Quantity</th>
                            <th class="px-6 py-4 text-left">Recorded By</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                        @forelse($productionOuts as $batch)
                            <tr class="hover:bg-cream hover:bg-opacity-20 transition">
                                <td class="px-6 py-4 font-bold text-sienna">{{ $batch->production_out_no }}</td>
                                <td class="px-6 py-4 text-sage">{{ $batch->date->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-sienna font-medium">{{ $batch->reason }}</td>
                                <td class="px-6 py-4 text-left text-sage">
                                    {{ $batch->items->pluck('product.product_name')->filter()->unique()->join(', ') ?: 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-center font-medium">{{ $batch->items->sum('quantity') }}</td>
                                <td class="px-6 py-4 text-sage">{{ $batch->createdBy->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-[9px] px-2 py-0.5 rounded-full uppercase font-black tracking-widest {{ $batch->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : ($batch->status === 'Approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $batch->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center space-x-2">
                                    <a href="{{ route('production-out.show', $batch->id) }}" class="text-sienna hover:text-terracotta transition font-bold">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-sage italic">No production OUT batches found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @else
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-sienna text-cream uppercase text-[10px] tracking-widest font-black">
                            <th class="px-6 py-4 text-left">Batch #</th>
                            <th class="px-6 py-4 text-left">Date</th>
                            <th class="px-6 py-4 text-left">Finished Products</th>
                            <th class="px-6 py-4 text-center">Quantity</th>
                            <th class="px-6 py-4 text-left">Raw Materials</th>
                            <th class="px-6 py-4 text-left">Notes</th>
                            <th class="px-6 py-4 text-left">Recorded By</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                        @forelse($productionIns as $batch)
                            <tr class="hover:bg-cream hover:bg-opacity-20 transition">
                                <td class="px-6 py-4 font-bold text-sienna">{{ $batch->production_in_no }}</td>
                                <td class="px-6 py-4 text-sage">{{ $batch->date->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-sage">{{ $batch->items->pluck('product.product_name')->filter()->unique()->join(', ') ?: 'N/A' }}</td>
                                <td class="px-6 py-4 text-center font-medium">{{ $batch->items->sum('quantity') }}</td>
                                <td class="px-6 py-4 text-sage">Not tracked</td>
                                <td class="px-6 py-4 text-sienna font-medium truncate max-w-xs">{{ $batch->notes ?: 'N/A' }}</td>
                                <td class="px-6 py-4 text-sage">{{ $batch->createdBy->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-[9px] px-2 py-0.5 rounded-full uppercase font-black tracking-widest {{ $batch->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : ($batch->status === 'Approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                        {{ $batch->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center space-x-2">
                                    <a href="{{ route('production-in.show', $batch->id) }}" class="text-sienna hover:text-terracotta transition font-bold">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-sage italic">No production IN batches found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
            <div class="p-4 border-t border-sienna border-opacity-10">
                @if($tab === 'out')
                    {{ $productionOuts->appends(['tab' => 'out', 'status' => request('status'), 'date_from' => request('date_from'), 'date_to' => request('date_to')])->links() }}
                @else
                    {{ $productionIns->appends(['tab' => 'in', 'status' => request('status'), 'date_from' => request('date_from'), 'date_to' => request('date_to')])->links() }}
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
