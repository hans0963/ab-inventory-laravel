<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center w-full bg-cream p-4 rounded-xl shadow-sm border border-sienna border-opacity-20 gap-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna leading-tight">{{ __('Customer Profile') }}</h2>
                <p class="font-inter text-sage mt-1 uppercase tracking-wider text-[10px]">Details, purchase history, favorite products, and discounts</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <a href="{{ route('customers.index') }}" class="w-full md:w-auto bg-sienna hover:bg-sienna-dark text-cream font-bold px-6 py-3 rounded-xl shadow-md transition-all text-center">
                    Back to List
                </a>
                <a href="{{ route('customers.edit', $customer->id) }}" class="w-full md:w-auto bg-terracotta hover:bg-terracotta-dark text-cream font-bold px-6 py-3 rounded-xl shadow-md transition-all text-center">
                    Edit Customer
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8 max-w-6xl mx-auto mt-8 px-4 sm:px-0">
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            <div class="xl:col-span-2 space-y-8">
                <div class="card-rustic border-sienna p-6 md:p-8">
                    <h3 class="text-xl font-lora font-bold text-sienna mb-6 border-b border-sienna border-opacity-10 pb-2 uppercase tracking-tighter">Customer Overview</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-sage font-black mb-1">Full Name</p>
                            <p class="text-xl font-black text-sienna">{{ $customer->name }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-sage font-black mb-1">Customer Type</p>
                            <p class="text-base font-bold text-sienna uppercase">{{ $customer->customer_type }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-sage font-black mb-1">Email Address</p>
                            <p class="text-base font-bold text-sienna">{{ $customer->email ?? 'Not provided' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-sage font-black mb-1">Phone Number</p>
                            <p class="text-base font-bold text-sienna">{{ $customer->phone ?? 'Not provided' }}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-[10px] uppercase tracking-widest text-sage font-black mb-1">Address</p>
                            <p class="text-base font-bold text-sienna">{{ $customer->address ?? 'No address saved' }}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-[10px] uppercase tracking-widest text-sage font-black mb-1">Date Registered</p>
                            <p class="text-base font-bold text-sienna">{{ $customer->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <div class="card-rustic border-sage p-6 md:p-8">
                    <h3 class="text-xl font-lora font-bold text-sienna mb-6 border-b border-sienna border-opacity-10 pb-2 uppercase tracking-tighter">Purchase History</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-sienna text-cream uppercase text-[10px] tracking-widest font-black whitespace-nowrap">
                                    <th class="px-5 py-4 text-left">Date</th>
                                    <th class="px-5 py-4 text-left">Receipt</th>
                                    <th class="px-5 py-4 text-left">Product</th>
                                    <th class="px-5 py-4 text-center">Qty</th>
                                    <th class="px-5 py-4 text-left">Payment</th>
                                    <th class="px-5 py-4 text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                                @forelse($recentSales as $sale)
                                    <tr class="hover:bg-cream hover:bg-opacity-20 transition-colors">
                                        <td class="px-5 py-4 text-sage">{{ $sale->date->format('M d, Y') }}</td>
                                        <td class="px-5 py-4 font-mono text-xs text-sienna">{{ $sale->receipt_number }}</td>
                                        <td class="px-5 py-4 text-sienna">{{ $sale->product->product_name }}</td>
                                        <td class="px-5 py-4 text-center font-black">{{ $sale->sold }}</td>
                                        <td class="px-5 py-4 text-sage">{{ $sale->payment_mode_label }}</td>
                                        <td class="px-5 py-4 text-right font-black text-terracotta">₱{{ number_format($sale->total_amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-5 py-12 text-center text-sage italic">No sales recorded for this customer yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <aside class="space-y-8">
                <div class="card-rustic border-terracotta p-6 md:p-8">
                    <h3 class="text-xl font-lora font-bold text-sienna mb-6 border-b border-sienna border-opacity-10 pb-2 uppercase tracking-tighter">Summary</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between text-sage uppercase tracking-widest text-[10px] font-black">
                            <span>Total Purchases</span>
                            <span class="font-bold text-sienna">{{ $customer->total_purchases }}</span>
                        </div>
                        <div class="flex justify-between text-sage uppercase tracking-widest text-[10px] font-black">
                            <span>Total Amount Spent</span>
                            <span class="font-bold text-terracotta">₱{{ number_format($customer->sales->sum('total_amount'), 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sage uppercase tracking-widest text-[10px] font-black">
                            <span>Last Purchase</span>
                            <span class="font-bold text-sienna">{{ $customer->sales->isNotEmpty() ? $customer->sales->max('date')->format('M d, Y') : 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <div class="card-rustic border-sienna p-6 md:p-8">
                    <h3 class="text-xl font-lora font-bold text-sienna mb-6 border-b border-sienna border-opacity-10 pb-2 uppercase tracking-tighter">Favorite Products</h3>
                    <div class="space-y-3">
                        @forelse($favoriteProducts as $favorite)
                            <div class="rounded-2xl bg-cream bg-opacity-80 p-4 border border-sienna border-opacity-10">
                                <div class="text-sienna font-semibold">{{ $favorite->product->product_name ?? 'Unknown product' }}</div>
                                <div class="text-sage text-[11px] uppercase tracking-widest">Quantity Purchased: {{ $favorite->total_quantity }}</div>
                                <div class="text-sage text-[11px] uppercase tracking-widest">Spend: ₱{{ number_format($favorite->total_spent, 2) }}</div>
                            </div>
                        @empty
                            <p class="text-sage italic">No favorite products yet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="card-rustic border-tan p-6 md:p-8">
                    <h3 class="text-xl font-lora font-bold text-sienna mb-6 border-b border-sienna border-opacity-10 pb-2 uppercase tracking-tighter">Discount History</h3>
                    <div class="space-y-3">
                        @forelse($discountHistory as $sale)
                            <div class="rounded-2xl bg-cream bg-opacity-80 p-4 border border-sienna border-opacity-10">
                                <div class="flex justify-between items-center gap-2">
                                    <div>
                                        <div class="text-sienna font-semibold">{{ $sale->discountType->discount_name ?? 'Discount' }}</div>
                                        <div class="text-sage text-[11px] uppercase tracking-widest">{{ $sale->date->format('M d, Y') }}</div>
                                    </div>
                                    <div class="text-terracotta font-bold">-₱{{ number_format($sale->discount_amount, 2) }}</div>
                                </div>
                                <div class="text-sage text-[11px]">Receipt: {{ $sale->receipt_number }}</div>
                            </div>
                        @empty
                            <p class="text-sage italic">No discount activity yet.</p>
                        @endforelse
                    </div>
                </div>
            </aside>
        </div>
    </div>
</x-app-layout>
