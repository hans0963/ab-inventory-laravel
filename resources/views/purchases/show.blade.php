<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ $purchase->po_number }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">
                    {{ $purchase->supplier->suppliers_name }} • {{ \Carbon\Carbon::parse($purchase->purchase_date)->format('M d, Y') }}
                </p>
            </div>
            <div class="flex gap-3">
                @if(in_array($purchase->status, ['Pending', 'Pending Approval', 'Rejected']))
                    <a href="{{ route('purchases.edit', $purchase->id) }}" class="btn-sage">
                        Edit Record
                    </a>
                    <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Cancel this purchase order?')" class="btn-terracotta bg-red-600 hover:bg-red-700">
                            Cancel
                        </button>
                    </form>
                @endif
                @if($purchase->status === 'Pending Approval' && auth()->user()->hasRole(['admin']))
                    <form action="{{ route('purchases.approve', $purchase) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="btn-sage">Approve</button>
                    </form>
                    <form action="{{ route('purchases.reject', $purchase) }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="rejection_reason" value="Rejected by owner review">
                        <button type="submit" class="btn-terracotta">Reject</button>
                    </form>
                @endif
                <a href="{{ route('purchases.index') }}" class="btn-sienna">
                    Back to Records
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-10">
        <!-- Status Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <x-stat-card title="Status" :value="$purchase->status" icon="📋" :border="match($purchase->status) { 'Pending' => 'terracotta', 'Partial' => 'sage', 'Complete' => 'sienna', default => 'cream' }" />
            <x-stat-card title="Total Amount" :value="'₱' . number_format($purchase->total_amount, 2)" icon="💰" border="terracotta" />
            <x-stat-card title="Items Ordered" :value="$totalOrdered" icon="📦" border="sage" />
            <x-stat-card title="Items Received" :value="$totalReceived" icon="✅" border="sienna" />
        </div>

        <!-- Reception Progress -->
        <div class="card-rustic border-terracotta">
            <h2 class="text-2xl font-lora font-bold text-sienna mb-6">Reception Progress</h2>
            <div class="space-y-6">
                <div>
                    <div class="flex justify-between items-center mb-3">
                        <p class="text-sm font-bold text-sienna uppercase tracking-widest">Overall Completion</p>
                        <p class="text-xl font-formal font-bold text-terracotta">{{ $receivingProgress }}%</p>
                    </div>
                    <div class="w-full bg-cream rounded-full h-6 shadow-inner border border-sienna border-opacity-10 overflow-hidden">
                        <div class="bg-terracotta h-6 rounded-full transition-all duration-1000 ease-out shadow-lg" style="width: {{ $receivingProgress }}%"></div>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-8 pt-4">
                    <div class="text-center p-4 bg-cream bg-opacity-30 rounded-xl border border-sienna border-opacity-5">
                        <p class="text-xs font-black text-sage uppercase tracking-tighter mb-1">Ordered</p>
                        <p class="text-3xl font-formal font-bold text-sienna">{{ $totalOrdered }}</p>
                    </div>
                    <div class="text-center p-4 bg-cream bg-opacity-30 rounded-xl border border-sienna border-opacity-5">
                        <p class="text-xs font-black text-sage uppercase tracking-tighter mb-1">Received</p>
                        <p class="text-3xl font-formal font-bold text-sage">{{ $totalReceived }}</p>
                    </div>
                    <div class="text-center p-4 bg-cream bg-opacity-30 rounded-xl border border-sienna border-opacity-5">
                        <p class="text-xs font-black text-sage uppercase tracking-tighter mb-1">Pending</p>
                        <p class="text-3xl font-formal font-bold text-terracotta">{{ $totalOrdered - $totalReceived }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchase Details -->
        <div class="card-rustic border-sienna">
            <h2 class="text-2xl font-lora font-bold text-sienna mb-6">Order Breakdown</h2>
            <div class="overflow-hidden rounded-xl border border-sienna border-opacity-10">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-sienna text-cream uppercase text-xs tracking-widest font-bold">
                            <th class="px-6 py-4 text-left">Product</th>
                            <th class="px-6 py-4 text-center">Quantity</th>
                            <th class="px-6 py-4 text-right">Unit Price</th>
                            <th class="px-6 py-4 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sienna divide-opacity-10">
                        @foreach($purchase->details as $detail)
                            <tr class="hover:bg-cream hover:bg-opacity-20 transition-colors">
                                <td class="px-6 py-4 font-bold text-sienna">{{ $detail->product->product_name }}</td>
                                <td class="px-6 py-4 text-center font-medium text-sage">{{ $detail->quantity }}</td>
                                <td class="px-6 py-4 text-right text-sage">₱{{ number_format($detail->price, 2) }}</td>
                                <td class="px-6 py-4 text-right font-black text-terracotta">₱{{ number_format($detail->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-cream bg-opacity-50 font-black text-sienna border-t-2 border-sienna">
                            <td colspan="3" class="px-6 py-5 text-right uppercase tracking-widest">Grand Total</td>
                            <td class="px-6 py-5 text-right text-xl">₱{{ number_format($purchase->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Linked Receivings -->
        @if($purchase->inventoryReceivings->count() > 0)
            <div class="card-rustic border-sage">
                <h2 class="text-2xl font-lora font-bold text-sienna mb-6">Linked Shipments</h2>
                <div class="overflow-hidden rounded-xl border border-sienna border-opacity-10">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-sage text-white uppercase text-xs tracking-widest font-bold">
                                <th class="px-6 py-4 text-left">Receiving #</th>
                                <th class="px-6 py-4 text-left">Date</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-center">Items</th>
                                <th class="px-6 py-4 text-right">Amount</th>
                                <th class="px-6 py-4 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sienna divide-opacity-10">
                            @foreach($purchase->inventoryReceivings as $receiving)
                                <tr class="hover:bg-cream hover:bg-opacity-20 transition-colors">
                                    <td class="px-6 py-4 font-bold text-sienna">{{ $receiving->receiving_no }}</td>
                                    <td class="px-6 py-4 text-sage">{{ $receiving->date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">
                                            {{ $receiving->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-sienna">{{ $receiving->pivot->items_received }}</td>
                                    <td class="px-6 py-4 text-right font-bold text-terracotta">₱{{ number_format($receiving->pivot->amount_received, 2) }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <form action="{{ route('purchases.unlink-receiving', $purchase->id) }}" method="POST" class="inline">
                                            @csrf
                                            <input type="hidden" name="inventory_receiving_id" value="{{ $receiving->id }}">
                                            <button type="submit" onclick="return confirm('Unlink this shipment from the record?')" class="text-terracotta hover:text-red-700 transition-colors">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Link Receiving Button -->
        @if($purchase->canReceive() && in_array($purchase->status, ['Approved', 'Ordered', 'Partial']))
            <div class="flex justify-center py-4">
                <a href="{{ route('purchases.receiving-matching', $purchase->id) }}" class="btn-terracotta text-lg px-10 py-4 shadow-rustic-lg">
                    Link Artisan Shipment
                </a>
            </div>
        @endif

        <!-- Audit Trail -->
        @if($purchase->createdBy || $purchase->approvedBy)
            <div class="bg-cream bg-opacity-50 rounded-2xl p-8 border border-sienna border-opacity-10">
                <h3 class="text-xs font-black text-sage uppercase tracking-[0.2em] mb-4">Record Audit Trail</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-sienna">
                    @if($purchase->createdBy)
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-sienna bg-opacity-10 flex items-center justify-center text-xl">✍️</div>
                            <div>
                                <p class="font-bold">Created by {{ $purchase->createdBy->name }}</p>
                                <p class="text-xs opacity-60">{{ $purchase->created_date?->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    @endif
                    @if($purchase->approvedBy)
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-sage bg-opacity-10 flex items-center justify-center text-xl">✔️</div>
                            <div>
                                <p class="font-bold">Approved by {{ $purchase->approvedBy->name }}</p>
                                <p class="text-xs opacity-60">{{ $purchase->approved_date?->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
