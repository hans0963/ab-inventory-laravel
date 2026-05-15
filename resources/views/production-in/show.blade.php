<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ __('Production IN Details') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Batch #{{ $productionIn->production_in_no }} — Overview and Items</p>
            </div>
            <a href="{{ route('production-in.index') }}" class="text-xs font-black text-sage uppercase tracking-widest hover:text-sienna transition flex items-center">
                ← Back to List
            </a>
        </div>
    </x-slot>

    <div class="space-y-8 max-w-5xl mx-auto mt-8">
        {{-- Quick Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-stat-card title="Batch Number" :value="$productionIn->production_in_no" icon="🔢" border="sienna" />
            <x-stat-card title="Date" :value="$productionIn->date->format('M d, Y')" icon="📅" border="sage" />
            <div class="card-rustic border-terracotta flex flex-col justify-center p-4">
                <p class="text-[10px] font-black text-sage uppercase tracking-widest mb-1">Status</p>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-widest text-center
                    {{ $productionIn->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : ($productionIn->status === 'Approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                    {{ $productionIn->status }}
                </span>
            </div>
            <x-stat-card title="Total Value" :value="'₱' . number_format($productionIn->total_inventory_value, 2)" icon="💰" border="cream" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Batch Information --}}
            <div class="lg:col-span-1 space-y-6">
                <div class="card-rustic border-sienna h-full">
                    <h3 class="text-xl font-lora font-bold text-sienna mb-6 border-b border-sienna border-opacity-10 pb-2">Audit Trail</h3>
                    <div class="space-y-6">
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-sage font-black">Created By</p>
                            <p class="text-base font-bold text-sienna">{{ $productionIn->createdBy->name ?? 'N/A' }}</p>
                            <p class="text-[10px] text-sage italic">{{ $productionIn->created_date->format('M d, Y h:i A') }}</p>
                        </div>
                        @if($productionIn->approvedBy)
                            <div class="pt-4 border-t border-sienna border-opacity-10">
                                <p class="text-[10px] uppercase tracking-widest text-sage font-black">Approved By</p>
                                <p class="text-base font-bold text-sienna">{{ $productionIn->approvedBy->name }}</p>
                                <p class="text-[10px] text-sage italic">{{ $productionIn->approved_date->format('M d, Y h:i A') }}</p>
                            </div>
                        @endif
                        @if($productionIn->notes)
                            <div class="pt-4 border-t border-sienna border-opacity-10">
                                <p class="text-[10px] uppercase tracking-widest text-sage font-black">Notes</p>
                                <p class="text-sm text-sage font-medium">{{ $productionIn->notes }}</p>
                            </div>
                        @endif
                    </div>

                    @if($productionIn->status === 'Pending' && auth()->user()->isManager())
                        <div class="mt-8 flex flex-col gap-3">
                            <form action="{{ route('production-in.approve', $productionIn->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-sage hover:bg-sage-dark text-white font-bold py-3 rounded-xl shadow-md transition-all uppercase tracking-widest text-xs">
                                    Approve Batch
                                </button>
                            </form>
                            <form action="{{ route('production-in.reject', $productionIn->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full bg-terracotta hover:bg-terracotta-dark text-white font-bold py-3 rounded-xl shadow-md transition-all uppercase tracking-widest text-xs">
                                    Reject Batch
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
                        <h3 class="text-xl font-lora font-bold text-sienna">Batch Items</h3>
                    </div>
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="bg-cream bg-opacity-50 text-sienna uppercase text-[10px] tracking-widest font-black">
                                <th class="px-6 py-4 text-left">Product</th>
                                <th class="px-6 py-4 text-center">Qty</th>
                                <th class="px-6 py-4 text-right">Unit Price</th>
                                <th class="px-6 py-4 text-right">Total</th>
                                <th class="px-6 py-4 text-left">Expiration</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                            @foreach($productionIn->items as $item)
                                <tr class="hover:bg-cream hover:bg-opacity-20 transition-colors">
                                    <td class="px-6 py-4 font-bold text-sienna">{{ $item->product->product_name }}</td>
                                    <td class="px-6 py-4 text-center font-black">{{ number_format($item->quantity) }}</td>
                                    <td class="px-6 py-4 text-right">₱{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-6 py-4 text-right font-bold text-terracotta">₱{{ number_format($item->total_value, 2) }}</td>
                                    <td class="px-6 py-4">
                                        @if($item->expiration_date)
                                            <span class="text-xs font-medium {{ $item->expiration_date->isPast() ? 'text-red-600' : 'text-sage' }}">
                                                {{ $item->expiration_date->format('M d, Y') }}
                                            </span>
                                        @else
                                            <span class="text-xs text-sage italic opacity-60">N/A</span>
                                        @endif
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
