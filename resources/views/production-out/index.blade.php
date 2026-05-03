<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full">
            <div>
                <h2 class="font-semibold text-2xl text-amber-900 leading-tight">
                    {{ __('Production Out') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Track products removed from shelves (pull-outs)</p>
            </div>
            <a href="{{ route('production-out.create') }}"
               class="inline-flex items-center gap-2 px-5 py-3 rounded-full text-white text-sm font-semibold shadow-sm transition hover:opacity-90"
               style="background-color: #E2725B;">
                <span class="text-lg leading-none">+</span>
                Record Loss
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- Search Bar --}}
            <div class="p-5 border-b border-gray-100">
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-4 flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                        </svg>
                    </div>
                    <input
                        type="text"
                        id="searchInput"
                        placeholder="Search production loss records..."
                        class="w-full rounded-full border border-gray-200 bg-gray-50 py-3 pl-11 pr-5 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:border-transparent"
                        oninput="filterTable(this.value)"
                    />
                </div>
            </div>

            {{-- Table --}}
            <table class="min-w-full text-sm" id="productionOutTable">
                <thead>
                    <tr class="border-b border-gray-100">
                        <th class="text-left px-6 py-4 text-xs font-medium text-gray-400 uppercase tracking-wide">ID</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-gray-400 uppercase tracking-wide">Product</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-gray-400 uppercase tracking-wide">Quantity</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-gray-400 uppercase tracking-wide">Date Pulled Out</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-gray-400 uppercase tracking-wide">Reason/Remarks</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-gray-400 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="tableBody">
                    @forelse($losses as $loss)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 font-semibold" style="color: #E2725B;">
                                #{{ $loss->id }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-800">
                                {{ $loss->product->product_name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-red-600 font-bold">
                                -{{ $loss->pull_out }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ \Carbon\Carbon::parse($loss->date)->format('Y-m-d H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                    {{ $loss->remarks ?? $loss->transaction_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('production-out.destroy', $loss->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">
                                No production loss records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="p-4">
                {{ $losses->links() }}
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        function filterTable(query) {
            const rows = document.querySelectorAll('#tableBody tr');
            const q = query.toLowerCase();
            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(q) ? '' : 'none';
            });
        }
    </script>
    @endpush
</x-app-layout>