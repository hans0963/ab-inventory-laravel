<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ __('Production IN Report') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">{{ $viewType === 'summary' ? 'Summary' : 'Detailed' }} view of production entries</p>
            </div>
            <div class="flex space-x-2">
                <form action="{{ route('reports.production') }}" method="GET" class="flex items-end space-x-2">
                    <input type="hidden" name="type" value="in">
                    <div>
                        <x-input-label for="view" value="View" class="text-[10px] uppercase tracking-widest text-sage" />
                        <select name="view" id="view" class="mt-1 block w-full !py-1 !text-sm border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50">
                            <option value="summary" {{ $viewType === 'summary' ? 'selected' : '' }}>Summary</option>
                            <option value="detailed" {{ $viewType === 'detailed' ? 'selected' : '' }}>Detailed</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="date_from" value="From" class="text-[10px] uppercase tracking-widest text-sage" />
                        <x-text-input id="date_from" name="date_from" type="date" value="{{ $dateFrom }}" class="mt-1 block w-full !py-1 !text-sm" />
                    </div>
                    <div>
                        <x-input-label for="date_to" value="To" class="text-[10px] uppercase tracking-widest text-sage" />
                        <x-text-input id="date_to" name="date_to" type="date" value="{{ $dateTo }}" class="mt-1 block w-full !py-1 !text-sm" />
                    </div>
                    <x-primary-button class="!py-2">Filter</x-primary-button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="card-rustic border-sienna">
        <div class="overflow-x-auto rounded-xl border border-sienna border-opacity-10">
            @if($viewType === 'summary')
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-sienna text-cream uppercase text-[10px] tracking-widest font-black">
                            <th class="px-6 py-4 text-left">No.</th>
                            <th class="px-6 py-4 text-left">Date</th>
                            <th class="px-6 py-4 text-left">Encoded By</th>
                            <th class="px-6 py-4 text-left">Approved By</th>
                            <th class="px-6 py-4 text-right">Total Value</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                        @forelse($data as $row)
                            <tr class="hover:bg-cream hover:bg-opacity-20 transition">
                                <td class="px-6 py-4 font-bold text-sienna">{{ $row->production_in_no }}</td>
                                <td class="px-6 py-4 text-sage">{{ $row->date->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-sage">{{ $row->createdBy->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-sage">{{ $row->approvedBy->name ?? 'N/A' }}</td>
                                <td class="px-6 py-4 text-right font-black text-terracotta">₱{{ number_format($row->total_inventory_value, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sage italic">No production data found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @else
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-sienna text-cream uppercase text-[10px] tracking-widest font-black">
                            <th class="px-6 py-4 text-left">No.</th>
                            <th class="px-6 py-4 text-left">Date</th>
                            <th class="px-6 py-4 text-left">Product</th>
                            <th class="px-6 py-4 text-center">Qty</th>
                            <th class="px-6 py-4 text-right">Unit Price</th>
                            <th class="px-6 py-4 text-right">Total Value</th>
                            <th class="px-6 py-4 text-center">Expiry</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                        @forelse($data as $row)
                            @foreach($row->items as $item)
                                <tr class="hover:bg-cream hover:bg-opacity-20 transition">
                                    <td class="px-6 py-4 font-bold text-sienna">{{ $loop->first ? $row->production_in_no : '' }}</td>
                                    <td class="px-6 py-4 text-sage">{{ $loop->first ? $row->date->format('M d, Y') : '' }}</td>
                                    <td class="px-6 py-4 text-sienna font-medium">{{ $item->product->product_name }}</td>
                                    <td class="px-6 py-4 text-center font-black">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 text-right">₱{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-6 py-4 text-right font-black text-terracotta">₱{{ number_format($item->total_value, 2) }}</td>
                                    <td class="px-6 py-4 text-center text-xs {{ $item->expiration_date && $item->expiration_date->isPast() ? 'text-red-600 font-bold' : 'text-sage' }}">
                                        {{ $item->expiration_date ? $item->expiration_date->format('M d, Y') : 'N/A' }}
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-sage italic">No production data found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</x-app-layout>
