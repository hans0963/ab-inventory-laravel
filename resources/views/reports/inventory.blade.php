<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ __('Inventory Report') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Stock movements and balance summary</p>
            </div>
            <div class="flex space-x-2">
                <form action="{{ route('reports.inventory') }}" method="GET" class="flex items-end space-x-2">
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
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-sienna text-cream uppercase text-[10px] tracking-widest font-black">
                        <th class="px-6 py-4 text-left">Product</th>
                        <th class="px-6 py-4 text-left">Category</th>
                        <th class="px-6 py-4 text-center">Type</th>
                        <th class="px-6 py-4 text-right">Beginning</th>
                        <th class="px-6 py-4 text-right">Stock In</th>
                        <th class="px-6 py-4 text-right">Stock Out</th>
                        <th class="px-6 py-4 text-right">Sold</th>
                        <th class="px-6 py-4 text-right">Ending</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                    @forelse($reportData as $row)
                        <tr class="hover:bg-cream hover:bg-opacity-20 transition">
                            <td class="px-6 py-4 font-bold text-sienna">{{ $row->product_name }}</td>
                            <td class="px-6 py-4 text-sage">{{ $row->category }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-[9px] px-2 py-0.5 rounded-full {{ $row->type === 'Finished Product' ? 'bg-sage bg-opacity-20 text-sage-dark' : 'bg-terracotta bg-opacity-20 text-terracotta-dark' }} uppercase font-black tracking-widest">
                                    {{ $row->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-medium">{{ $row->beginning }}</td>
                            <td class="px-6 py-4 text-right text-green-600 font-bold">+{{ $row->in }}</td>
                            <td class="px-6 py-4 text-right text-red-600 font-bold">-{{ $row->out }}</td>
                            <td class="px-6 py-4 text-right text-orange-600 font-bold">-{{ $row->sold }}</td>
                            <td class="px-6 py-4 text-right font-black text-sienna">{{ $row->ending }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-sage italic">No inventory data found for the selected period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
