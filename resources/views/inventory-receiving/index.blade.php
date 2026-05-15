<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ __('Inventory Receiving') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Manage incoming stock from artisan suppliers</p>
            </div>
            <a href="{{ route('inventory-receiving.create') }}" class="bg-terracotta hover:bg-terracotta-dark text-cream font-bold px-8 py-3 rounded-xl shadow-rustic transition-all hover:-translate-y-0.5 active:translate-y-0 flex items-center">
                <span class="mr-2 text-xl">+</span> New Receiving
            </a>
        </div>
    </x-slot>

    <div class="space-y-8">
        <!-- Filters -->
        <div class="card-rustic border-sage">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-6 items-end">
                <div>
                    <x-input-label for="status" value="Status" class="text-[10px] uppercase tracking-widest text-sage" />
                    <select name="status" class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm bg-cream bg-opacity-10">
                        <option value="">All Statuses</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Approved" {{ request('status') == 'Approved' ? 'selected' : '' }}>Approved</option>
                        <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div>
                    <x-input-label for="supplier_id" value="Supplier" class="text-[10px] uppercase tracking-widest text-sage" />
                    <select name="supplier_id" class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm bg-cream bg-opacity-10">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->suppliers_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label for="date_from" value="From Date" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input type="date" name="date_from" value="{{ request('date_from') }}" class="mt-1 block w-full !text-sm" />
                </div>

                <div>
                    <x-input-label for="date_to" value="To Date" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input type="date" name="date_to" value="{{ request('date_to') }}" class="mt-1 block w-full !text-sm" />
                </div>

                <div class="md:col-span-4 flex gap-4">
                    <x-primary-button class="px-8 py-2">Filter</x-primary-button>
                    <a href="{{ route('inventory-receiving.index') }}" class="inline-flex items-center px-8 py-2 bg-cream border border-sienna border-opacity-20 rounded-md font-bold text-xs text-sienna uppercase tracking-widest hover:bg-opacity-50 transition">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Receivings Table -->
        <div class="card-rustic border-sienna p-0 overflow-hidden">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-sienna text-cream uppercase text-[10px] tracking-widest font-black">
                        <th class="px-6 py-4 text-left">Receiving #</th>
                        <th class="px-6 py-4 text-left">Date</th>
                        <th class="px-6 py-4 text-left">Supplier</th>
                        <th class="px-6 py-4 text-center">Items</th>
                        <th class="px-6 py-4 text-right">Total Cost</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                    @forelse($receivings as $receiving)
                        <tr class="hover:bg-cream hover:bg-opacity-20 transition-colors">
                            <td class="px-6 py-4 font-bold text-sienna">{{ $receiving->receiving_no }}</td>
                            <td class="px-6 py-4 text-sage font-medium">{{ $receiving->date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 font-bold text-sienna">{{ $receiving->supplier->suppliers_name }}</td>
                            <td class="px-6 py-4 text-center font-black">{{ $receiving->items->count() }}</td>
                            <td class="px-6 py-4 text-right font-black text-terracotta">₱{{ number_format($receiving->total_cost, 2) }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-[9px] px-2 py-0.5 rounded-full uppercase font-black tracking-widest 
                                    {{ $receiving->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : ($receiving->status === 'Approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $receiving->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('inventory-receiving.show', $receiving->id) }}" class="text-sienna hover:text-terracotta transition font-bold">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-sage italic">No inventory receivings found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-6 border-t border-sienna border-opacity-10">
                {{ $receivings->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
