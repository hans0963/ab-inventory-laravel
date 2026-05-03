<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-pacifico text-4xl text-sienna">
                    {{ __('Procurement Records') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Track artisan ingredient acquisitions</p>
            </div>

            <a href="{{ route('purchases.create') }}" 
               class="bg-terracotta hover:bg-terracotta-dark text-cream font-bold px-8 py-3 rounded-xl shadow-rustic transition-all hover:-translate-y-0.5 active:translate-y-0 flex items-center">
                <span class="mr-2 text-xl">+</span> New Purchase
            </a>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="card-rustic border-sienna">
            <div class="overflow-hidden rounded-xl border border-sienna border-opacity-10 shadow-sm">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-sienna text-cream uppercase text-xs tracking-widest font-bold">
                            <th class="px-6 py-4 text-left">Date</th>
                            <th class="px-6 py-4 text-left">Supplier</th>
                            <th class="px-6 py-4 text-left">Company</th>
                            <th class="px-6 py-4 text-left">Reference</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                        @foreach ($purchases as $purchase)
                            <tr class="hover:bg-cream hover:bg-opacity-20 transition-colors">
                                <td class="px-6 py-5 font-medium text-sienna opacity-80">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('M d, Y') }}</td>
                                <td class="px-6 py-5 font-bold text-sienna text-base">{{ $purchase->supplier->suppliers_name }}</td>
                                <td class="px-6 py-5 text-sage font-medium">{{ $purchase->supplier->suppliers_company }}</td>
                                <td class="px-6 py-5">
                                    <span class="bg-terracotta bg-opacity-10 text-terracotta px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider">
                                        {{ $purchase->reference }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <div class="flex items-center justify-center space-x-3">
                                        <a href="{{ route('purchases.show', $purchase->id) }}" class="p-2 text-sage hover:text-sienna hover:bg-sage hover:bg-opacity-10 rounded-lg transition-all">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <x-alert-delete 
                                            route="{{ route('purchases.destroy', $purchase->id) }}" 
                                            message="Are you sure you want to remove this procurement record?" />
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-8">
                {{ $purchases->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
