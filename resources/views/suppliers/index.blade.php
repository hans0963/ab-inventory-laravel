<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-md shadow-sm border-b border-sienna">
            <h2 class="font-pacifico text-3xl text-sienna leading-tight">
                {{ __('Suppliers') }}
            </h2>
            <p class="font-inter text-sage">Manage your bakeshop suppliers</p>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-sienna">
            
            {{-- Header with search + add supplier --}}
            <div class="flex justify-between items-center mb-6">
                <div class="w-1/3">
                    <form action="{{ route('suppliers.index') }}" method="GET">
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Search suppliers..." 
                                   class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10 pl-10">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-sienna opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </form>
                </div>
                <a href="{{ route('suppliers.create') }}" 
                   class="bg-terracotta hover:bg-opacity-90 text-cream font-inter px-6 py-2 rounded-lg shadow-md transition flex items-center">
                    <span class="mr-2">+</span> Add Supplier
                </a>
            </div>

            {{-- Suppliers Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-sienna text-cream uppercase text-xs tracking-wider">
                            <th class="px-4 py-3 text-left">ID</th>
                            <th class="px-4 py-3 text-left">Supplier Name</th>
                            <th class="px-4 py-3 text-left">Company</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-left">Phone</th>
                            <th class="px-4 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sienna divide-opacity-20">
                        @forelse ($suppliers as $supplier)
                            <tr class="hover:bg-cream hover:bg-opacity-30 transition">
                                <td class="px-4 py-4 font-semibold text-sienna">#{{ $supplier->id }}</td>
                                <td class="px-4 py-4 text-sienna font-medium">{{ $supplier->suppliers_name }}</td>
                                <td class="px-4 py-4 text-gray-600">{{ $supplier->suppliers_company ?? 'N/A' }}</td>
                                <td class="px-4 py-4 text-gray-600 italic">{{ $supplier->suppliers_email ?? 'N/A' }}</td>
                                <td class="px-4 py-4 text-gray-600">{{ $supplier->suppliers_phone ?? 'N/A' }}</td>
                                <td class="px-4 py-4 text-center space-x-2">
                                    <a href="{{ route('suppliers.edit', $supplier->id) }}" class="text-sage hover:text-sienna transition inline-block">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <x-alert-delete 
                                        route="{{ route('suppliers.destroy', $supplier->id) }}" 
                                        message="Are you sure you want to delete this supplier?" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500 italic">No suppliers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if(method_exists($suppliers, 'links'))
            <div class="mt-6">
                {{ $suppliers->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
