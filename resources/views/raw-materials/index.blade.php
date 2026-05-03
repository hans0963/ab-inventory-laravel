<x-app-layout>

    <!-- Header -->
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-pacifico text-4xl text-sienna">
                    {{ __('Baking Ingredients') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Monitor raw materials and expiration</p>
            </div>

            <a href="{{ route('raw-materials.create') }}" 
               class="bg-terracotta hover:bg-terracotta-dark text-cream font-bold px-8 py-3 rounded-xl shadow-rustic transition-all hover:-translate-y-0.5 active:translate-y-0 flex items-center">
                <span class="mr-2 text-xl">+</span> Stock In
            </a>
        </div>
    </x-slot>

    <div class="space-y-8">
        <!-- Alerts -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Near Expiry -->
            <div class="card-rustic border-sage flex items-center gap-6 bg-sage bg-opacity-5">
                <span class="text-4xl transform -rotate-12 opacity-40">⏳</span>
                <div>
                    <p class="font-inter text-sage font-black text-xs uppercase tracking-widest mb-1">Items near Expiry</p>
                    <p class="font-lora font-bold text-3xl text-sienna leading-none">{{ $nearExpiryCount }}</p>
                </div>
            </div>

            <!-- Expired -->
            <div class="card-rustic border-terracotta flex items-center gap-6 bg-terracotta bg-opacity-5">
                <span class="text-4xl transform -rotate-12 opacity-40">🚫</span>
                <div>
                    <p class="font-inter text-terracotta font-black text-xs uppercase tracking-widest mb-1">Expired Items</p>
                    <p class="font-lora font-bold text-3xl text-sienna leading-none">{{ $expiredCount }}</p>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card-rustic border-sienna">
            <!-- Search -->
            <div class="mb-8">
                <div class="relative w-full md:w-1/2">
                    <input type="text"
                           placeholder="Search ingredients..."
                           class="w-full rounded-xl border-sienna border-opacity-20 bg-cream bg-opacity-20 py-3 pl-12 pr-4 focus:ring-2 focus:ring-terracotta focus:border-terracotta transition-all shadow-inner">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-sienna opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-hidden rounded-xl border border-sienna border-opacity-10 shadow-sm">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-sienna text-cream uppercase text-xs tracking-widest font-bold">
                            <th class="px-6 py-4 text-left">Ingredient</th>
                            <th class="px-6 py-4 text-center">Stock</th>
                            <th class="px-6 py-4 text-left">Unit</th>
                            <th class="px-6 py-4 text-left">Expiration</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                        @foreach ($materials as $material)
                            @php
                                $today = \Carbon\Carbon::today();
                                $expiry = \Carbon\Carbon::parse($material->expiration_date);

                                if ($expiry->lt($today)) {
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
                                <td class="px-6 py-5 text-center">
                                    <span class="inline-flex items-center justify-center min-w-[3rem] px-2 py-1 rounded-lg font-black bg-sage bg-opacity-10 text-sage-dark">
                                        {{ $material->quantity }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-sage font-medium uppercase tracking-wider text-xs">{{ $material->unit }}</td>
                                <td class="px-6 py-5 text-sienna opacity-80 font-medium">{{ $expiry->format('M d, Y') }}</td>
                                <td class="px-6 py-5 text-center">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $badge }}">
                                        {{ $status }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <div class="flex items-center justify-center space-x-3">
                                        <a href="{{ route('raw-materials.edit', $material->id) }}" class="p-2 text-sage hover:text-sienna hover:bg-sage hover:bg-opacity-10 rounded-lg transition-all">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <x-alert-delete 
                                            route="{{ route('raw-materials.destroy', $material->id) }}" 
                                            message="Are you sure you want to remove this ingredient?" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-8">
                {{ $materials->links() }}
            </div>
        </div>
    </div>

</x-app-layout>