<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ __('Receiving Details') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Reference #{{ $receiving->receiving_no }} — Stock Acquisition Overview</p>
            </div>
            <a href="{{ route('inventory-receiving.index') }}" class="text-xs font-black text-sage uppercase tracking-widest hover:text-sienna transition flex items-center">
                ← Back to List
            </a>
        </div>
    </x-slot>

    <div class="space-y-8 max-w-6xl mx-auto mt-8">
        {{-- Quick Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-stat-card title="Receiving #" :value="$receiving->receiving_no" icon="🔢" border="sienna" />
            <x-stat-card title="Date" :value="$receiving->date->format('M d, Y')" icon="📅" border="sage" />
            <div class="card-rustic border-terracotta flex flex-col justify-center p-4">
                <p class="text-[10px] font-black text-sage uppercase tracking-widest mb-1">Status</p>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest text-center
                    {{ $receiving->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : ($receiving->status === 'Approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                    {{ $receiving->status }}
                </span>
            </div>
            <x-stat-card title="Total Cost" :value="'₱' . number_format($receiving->total_cost, 2)" icon="💰" border="cream" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Audit Trail & Supplier --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="card-rustic border-sienna">
                    <h3 class="text-xl font-lora font-bold text-sienna mb-6 border-b border-sienna border-opacity-10 pb-2">Procurement Log</h3>
                    <div class="space-y-6">
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-sage font-black">Supplier</p>
                            <p class="text-base font-bold text-sienna">{{ $receiving->supplier->suppliers_name }}</p>
                            <p class="text-xs text-sage font-medium">{{ $receiving->supplier->suppliers_company }}</p>
                        </div>
                        <div class="pt-4 border-t border-sienna border-opacity-10">
                            <p class="text-[10px] uppercase tracking-widest text-sage font-black">Encoded By</p>
                            <p class="text-base font-bold text-sienna">{{ $receiving->createdBy->name ?? 'N/A' }}</p>
                            <p class="text-[10px] text-sage italic">{{ $receiving->created_date->format('M d, Y h:i A') }}</p>
                        </div>
                        @if($receiving->approvedBy)
                            <div class="pt-4 border-t border-sienna border-opacity-10">
                                <p class="text-[10px] uppercase tracking-widest text-sage font-black">Approved By</p>
                                <p class="text-base font-bold text-sienna">{{ $receiving->approvedBy->name }}</p>
                                <p class="text-[10px] text-sage italic">{{ $receiving->approved_date->format('M d, Y h:i A') }}</p>
                            </div>
                        @endif
                        @if($receiving->notes)
                            <div class="pt-4 border-t border-sienna border-opacity-10">
                                <p class="text-[10px] uppercase tracking-widest text-sage font-black">Notes</p>
                                <p class="text-sm text-sage font-medium">{{ $receiving->notes }}</p>
                            </div>
                        @endif
                    </div>

                    @if($receiving->status === 'Pending' && auth()->user()->isManager())
                        <div class="mt-8 flex flex-col gap-3">
                            <form action="{{ route('inventory-receiving.approve', $receiving->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-sage hover:bg-sage-dark text-white font-bold py-3 rounded-xl shadow-md transition-all uppercase tracking-widest text-xs">
                                    Approve Receiving
                                </button>
                            </form>
                            <form action="{{ route('inventory-receiving.reject', $receiving->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-terracotta hover:bg-terracotta-dark text-white font-bold py-3 rounded-xl shadow-md transition-all uppercase tracking-widest text-xs">
                                    Reject Receiving
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Items Table --}}
            <div class="lg:col-span-2">
                <div class="card-rustic border-sage p-0 overflow-hidden">
                    <div class="p-6 border-b border-sienna border-opacity-10">
                        <h3 class="text-xl font-lora font-bold text-sienna">Received Items</h3>
                    </div>
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-cream bg-opacity-50 text-sienna uppercase text-[10px] tracking-widest font-black">
                                <th class="px-6 py-4 text-left">Product</th>
                                <th class="px-6 py-4 text-center">Ordered</th>
                                <th class="px-6 py-4 text-center">Received</th>
                                <th class="px-6 py-4 text-right">Unit Cost</th>
                                <th class="px-6 py-4 text-right">Total</th>
                                <th class="px-6 py-4 text-center">Condition</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                            @foreach($receiving->items as $item)
                                <tr class="hover:bg-cream hover:bg-opacity-20 transition-colors">
                                    <td class="px-6 py-4 font-bold text-sienna">{{ $item->product->product_name }}</td>
                                    <td class="px-6 py-4 text-center text-sage">{{ $item->quantity_ordered }}</td>
                                    <td class="px-6 py-4 text-center font-black text-sienna">{{ $item->quantity_received }}</td>
                                    <td class="px-6 py-4 text-right">₱{{ number_format($item->unit_cost, 2) }}</td>
                                    <td class="px-6 py-4 text-right font-bold text-terracotta">₱{{ number_format($item->total_cost, 2) }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="text-[9px] px-2 py-0.5 rounded-full uppercase font-black tracking-widest 
                                            {{ $item->condition === 'Good' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $item->condition }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
