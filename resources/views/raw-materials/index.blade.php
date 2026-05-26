<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ __('Raw Materials') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Monitor ingredients, packaging, stock, and expiration</p>
            </div>

            <a href="{{ route('raw-materials.create') }}"
               class="inline-flex items-center justify-center bg-sage hover:bg-opacity-90 text-white font-bold px-5 py-3 rounded-xl shadow-rustic transition-all hover:-translate-y-0.5 active:translate-y-0 text-sm">
                Create Raw Material
            </a>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="card-rustic border-sage flex items-center gap-6 bg-sage bg-opacity-5">
                <div>
                    <p class="font-inter text-sage font-black text-xs uppercase tracking-widest mb-1">Items Near Expiry</p>
                    <p class="font-lora font-bold text-3xl text-sienna leading-none">{{ $nearExpiryCount }}</p>
                </div>
            </div>

            <div class="card-rustic border-terracotta flex items-center gap-6 bg-terracotta bg-opacity-5">
                <div>
                    <p class="font-inter text-terracotta font-black text-xs uppercase tracking-widest mb-1">Expired Items</p>
                    <p class="font-lora font-bold text-3xl text-sienna leading-none">{{ $expiredCount }}</p>
                </div>
            </div>
        </div>

        <div class="card-rustic border-sienna">
            <form method="GET" action="{{ route('raw-materials.index') }}" class="mb-8">
                <div class="relative w-full md:w-1/2">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search raw materials..."
                           class="w-full rounded-xl border-sienna border-opacity-20 bg-cream bg-opacity-20 py-3 pl-4 pr-4 focus:ring-2 focus:ring-terracotta focus:border-terracotta transition-all shadow-inner">
                </div>
            </form>

            <div class="overflow-hidden rounded-xl border border-sienna border-opacity-10 shadow-sm">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-sienna text-cream uppercase text-xs tracking-widest font-bold">
                            <th class="px-6 py-4 text-left">Raw Material</th>
                            <th class="px-6 py-4 text-left">Type</th>
                            <th class="px-6 py-4 text-center">Stock</th>
                            <th class="px-6 py-4 text-left">Unit</th>
                            <th class="px-6 py-4 text-left">Expiration</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                        @forelse ($materials as $material)
                            @php
                                $today = \Carbon\Carbon::today();
                                $expiry = $material->expiration_date ? \Carbon\Carbon::parse($material->expiration_date) : null;

                                if (!$expiry) {
                                    $status = $material->status;
                                    $badge = $material->status === 'Active' ? 'bg-sage bg-opacity-10 text-sage-dark' : 'bg-gray-100 text-gray-600';
                                } elseif ($expiry->lt($today)) {
                                    $status = 'Expired';
                                    $badge = 'bg-red-100 text-red-600';
                                } elseif ($expiry->diffInDays($today) <= 7) {
                                    $status = 'Near Expiry';
                                    $badge = 'bg-yellow-100 text-yellow-700';
                                } else {
                                    $status = 'Good';
                                    $badge = 'bg-sage bg-opacity-10 text-sage-dark';
                                }
                            @endphp

                            <tr class="hover:bg-cream hover:bg-opacity-20 transition-colors">
                                <td class="px-6 py-5 font-bold text-sienna text-base">{{ $material->material_name }}</td>
                                <td class="px-6 py-5 text-sage font-medium">{{ $material->type ?: 'N/A' }}</td>
                                <td class="px-6 py-5 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[3rem] px-2 py-1 rounded-lg font-black bg-sage bg-opacity-10 text-sage-dark">
                                        {{ $material->quantity }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-sage font-medium uppercase tracking-wider text-xs">{{ $material->unit ?: 'N/A' }}</td>
                                <td class="px-6 py-5 text-sienna opacity-80 font-medium">{{ $expiry ? $expiry->format('M d, Y') : 'No expiry' }}</td>
                                <td class="px-6 py-5 text-center">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $badge }}">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <div class="flex items-center justify-center space-x-3">
                                        <a href="{{ route('raw-materials.edit', $material) }}" class="px-3 py-2 bg-cream bg-opacity-80 text-sienna font-semibold rounded-xl hover:bg-opacity-100 transition-all">
                                            Edit
                                        </a>
                                        <x-alert-delete
                                            route="{{ route('raw-materials.destroy', $material) }}"
                                            button-label="Archive"
                                            confirm-label="Archive Raw Material"
                                            message="Are you sure you want to archive this raw material?" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-sage italic">No raw materials found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-8">
                {{ $materials->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
