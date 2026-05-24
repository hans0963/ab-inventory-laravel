<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ $supplier->suppliers_company }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">
                    Supplier #{{ $supplier->id }} | {{ $supplier->suppliers_name }}
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn-sage">Edit Supplier</a>
                <a href="{{ route('suppliers.index') }}" class="btn-sienna">Back to Suppliers</a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <x-stat-card title="Status" :value="$supplier->status" icon="ID" :border="$supplier->status === 'Active' ? 'sage' : 'cream'" />
            <x-stat-card title="Payment Terms" :value="$supplier->payment_terms" icon="Pay" border="sienna" />
            <x-stat-card title="Total Purchased" :value="'PHP ' . number_format($totalAmountPurchased, 2)" icon="Sum" border="terracotta" />
            <x-stat-card title="Outstanding" :value="'PHP ' . number_format($outstandingPayments, 2)" icon="Due" border="sage" />
        </div>

        <div class="card-rustic border-sienna">
            <h3 class="text-2xl font-lora font-bold text-sienna mb-6">Supplier Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                <div>
                    <p class="text-xs font-black text-sage uppercase tracking-widest">Business Name</p>
                    <p class="mt-1 font-bold text-sienna">{{ $supplier->suppliers_company }}</p>
                </div>
                <div>
                    <p class="text-xs font-black text-sage uppercase tracking-widest">Contact Person</p>
                    <p class="mt-1 font-bold text-sienna">{{ $supplier->suppliers_name }}</p>
                </div>
                <div>
                    <p class="text-xs font-black text-sage uppercase tracking-widest">Phone Number</p>
                    <p class="mt-1 text-sienna">{{ $supplier->suppliers_phone ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs font-black text-sage uppercase tracking-widest">Email</p>
                    <p class="mt-1 text-sienna">{{ $supplier->suppliers_email ?? 'N/A' }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-xs font-black text-sage uppercase tracking-widest">Address</p>
                    <p class="mt-1 text-sienna">{{ $supplier->suppliers_address ?? 'N/A' }}</p>
                </div>
                <div class="md:col-span-2">
                    <p class="text-xs font-black text-sage uppercase tracking-widest">Items They Supply</p>
                    <p class="mt-1 text-sienna">{{ $supplier->items_supplied ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="card-rustic border-terracotta">
            <h3 class="text-2xl font-lora font-bold text-sienna mb-6">Purchase History</h3>

            <div class="overflow-x-auto rounded-xl border border-sienna border-opacity-10">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-sienna text-cream uppercase text-xs tracking-widest">
                            <th class="px-6 py-4 text-left">Date</th>
                            <th class="px-6 py-4 text-left">PO Number</th>
                            <th class="px-6 py-4 text-left">Items</th>
                            <th class="px-6 py-4 text-right">Total Amount</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                        @forelse($purchaseHistory as $purchase)
                            <tr class="hover:bg-cream hover:bg-opacity-20 transition-colors">
                                <td class="px-6 py-4 text-sienna">{{ $purchase->purchase_date->format('M d, Y') }}</td>
                                <td class="px-6 py-4 font-bold text-sienna">{{ $purchase->po_number ?? $purchase->reference }}</td>
                                <td class="px-6 py-4 text-sage">
                                    {{ $purchase->details->pluck('product.product_name')->filter()->implode(', ') ?: 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-right font-black text-terracotta">PHP {{ number_format($purchase->total_amount, 2) }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="bg-cream text-sienna px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">
                                        {{ $purchase->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('purchases.show', $purchase->id) }}" class="text-sage hover:text-sienna font-bold text-xs uppercase tracking-widest">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500 italic">No purchase history yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $purchaseHistory->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
