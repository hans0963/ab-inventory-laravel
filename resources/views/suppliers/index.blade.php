<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ __('Suppliers') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Manage business contacts and procurement partners</p>
            </div>

            <a href="{{ route('suppliers.create') }}" class="btn-terracotta">
                <span class="mr-2 text-xl">+</span> Add Supplier
            </a>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="card-rustic border-sienna">
            <div class="mb-6">
                <form action="{{ route('suppliers.index') }}" method="GET" class="max-w-md">
                    <label for="search" class="sr-only">Search suppliers</label>
                    <div class="relative">
                        <input id="search" type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search supplier, contact, email, phone, or items"
                               class="input-artisan pl-10">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-sienna opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto rounded-xl border border-sienna border-opacity-10">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-sienna text-cream uppercase text-xs tracking-widest">
                            <th class="px-4 py-3 text-left">Supplier ID</th>
                            <th class="px-4 py-3 text-left">Business Name</th>
                            <th class="px-4 py-3 text-left">Contact Person</th>
                            <th class="px-4 py-3 text-left">Phone Number</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-left">Address</th>
                            <th class="px-4 py-3 text-left">Items They Supply</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                        @forelse ($suppliers as $supplier)
                            <tr class="hover:bg-cream hover:bg-opacity-20 transition-colors">
                                <td class="px-4 py-4 font-semibold text-sienna">#{{ $supplier->id }}</td>
                                <td class="px-4 py-4 font-bold text-sienna">{{ $supplier->suppliers_company }}</td>
                                <td class="px-4 py-4 text-sienna">{{ $supplier->suppliers_name }}</td>
                                <td class="px-4 py-4 text-gray-600">{{ $supplier->suppliers_phone ?? 'N/A' }}</td>
                                <td class="px-4 py-4 text-gray-600 italic">{{ $supplier->suppliers_email ?? 'N/A' }}</td>
                                <td class="px-4 py-4 text-gray-600 max-w-xs">{{ $supplier->suppliers_address ?? 'N/A' }}</td>
                                <td class="px-4 py-4 text-gray-600 max-w-xs">{{ $supplier->items_supplied ?? 'N/A' }}</td>
                                <td class="px-4 py-4 text-center">
                                    @php
                                        $statusClass = $supplier->status === 'Active'
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-gray-100 text-gray-700';
                                    @endphp
                                    <span class="{{ $statusClass }} px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">
                                        {{ $supplier->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('suppliers.show', $supplier->id) }}" class="p-2 text-sage hover:text-sienna hover:bg-sage hover:bg-opacity-10 rounded-lg transition" title="View Supplier">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('suppliers.edit', $supplier->id) }}" class="p-2 text-sage hover:text-sienna hover:bg-sage hover:bg-opacity-10 rounded-lg transition" title="Edit Supplier">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <x-alert-delete
                                            route="{{ route('suppliers.destroy', $supplier->id) }}"
                                            message="Are you sure you want to delete this supplier?" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-8 text-center text-gray-500 italic">No suppliers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $suppliers->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
