<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-md shadow-sm border-b border-sienna">
            <h2 class="font-pacifico text-3xl text-sienna leading-tight">
                {{ __('Customers') }}
            </h2>
            <p class="font-inter text-sage">Manage bakeshop customer records</p>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-sienna">
            {{-- Header with search + add customer --}}
            <div class="flex justify-between items-center mb-6">
                <div class="w-1/3">
                    <form action="{{ route('customers.index') }}" method="GET">
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Search customers..." 
                                   class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-20 pl-10">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-sienna opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </form>
                </div>
                <a href="{{ route('customers.create') }}" 
                   class="bg-terracotta hover:bg-opacity-90 text-cream font-inter px-6 py-2 rounded-lg shadow-md transition flex items-center">
                    <span class="mr-2">+</span> Add Customer
                </a>
            </div>

            {{-- Customers Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-sienna text-cream uppercase text-xs tracking-wider">
                            <th class="px-4 py-3 text-left">#</th>
                            <th class="px-4 py-3 text-left">Name</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-left">Phone</th>
                            <th class="px-4 py-3 text-center">Orders</th>
                            <th class="px-4 py-3 text-right">Total Spent</th>
                            <th class="px-4 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sienna divide-opacity-20">
                        @forelse ($customers as $customer)
                            <tr class="hover:bg-cream hover:bg-opacity-30 transition">
                                <td class="px-4 py-4 text-gray-600">{{ ($customers->currentPage() - 1) * $customers->perPage() + $loop->iteration }}</td>
                                <td class="px-4 py-4 font-semibold text-sienna">{{ $customer->name }}</td>
                                <td class="px-4 py-4 text-gray-600 italic">{{ $customer->email ?? 'N/A' }}</td>
                                <td class="px-4 py-4 text-gray-600">{{ $customer->phone ?? 'N/A' }}</td>
                                <td class="px-4 py-4 text-center">
                                    <span class="bg-sage bg-opacity-20 text-sage px-2 py-1 rounded-full text-xs font-bold">
                                        {{ $customer->orders_count }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-right font-bold text-terracotta">
                                    ₱{{ number_format($customer->total_spent ?? 0, 2) }}
                                </td>
                                <td class="px-4 py-4 text-center space-x-2">
                                    <a href="{{ route('customers.edit', $customer) }}" 
                                       class="text-sage hover:text-sienna transition inline-block">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="inline-block">
                                        @csrf @method('DELETE')
                                        <button type="submit" 
                                                class="text-terracotta hover:text-sienna transition"
                                                onclick="return confirm('Are you sure you want to delete this customer?')">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500 italic">No customers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $customers->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
