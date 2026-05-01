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
                        <th class="text-left px-6 py-4 text-xs font-medium text-gray-400 uppercase tracking-wide">Reference No.</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-gray-400 uppercase tracking-wide">Product</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-gray-400 uppercase tracking-wide">Quantity</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-gray-400 uppercase tracking-wide">Date Pulled Out</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-gray-400 uppercase tracking-wide">Reason</th>
                        <th class="text-left px-6 py-4 text-xs font-medium text-gray-400 uppercase tracking-wide">Recorded By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100" id="tableBody">
                    @forelse($losses ?? [] as $loss)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 font-semibold" style="color: #E2725B;">
                                {{ $loss->reference_number }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-800">
                                {{ $loss->product->name ?? $loss->product_name }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $loss->quantity }}
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ \Carbon\Carbon::parse($loss->date_pulled_out)->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $reason = strtolower($loss->reason ?? '');
                                    if (str_contains($reason, 'expired')) {
                                        $reasonStyle = 'background-color: #fde8e8; color: #a33a2a;';
                                    } elseif (str_contains($reason, 'damaged')) {
                                        $reasonStyle = 'background-color: #fef3cd; color: #8a6000;';
                                    } else {
                                        $reasonStyle = 'background-color: #f0f0f0; color: #666;';
                                    }
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-medium" style="{{ $reasonStyle }}">
                                    {{ ucfirst($loss->reason ?? 'Pull-out') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">
                                {{ $loss->user->name ?? $loss->recorded_by ?? '—' }}
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