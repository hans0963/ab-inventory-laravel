<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ __('Sale Details') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Transaction overview and receipt summary</p>
            </div>
            <div class="flex gap-4">
                <a href="{{ route('sales.print', $sale->id) }}" class="bg-sage hover:bg-sage-dark text-white font-bold px-6 py-2 rounded-xl shadow-md transition-all flex items-center">
                    🖨️ Print Receipt
                </a>
                <a href="{{ route('sales.index') }}" class="text-xs font-black text-sage uppercase tracking-widest hover:text-sienna transition flex items-center">
                    ← Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8 max-w-4xl mx-auto mt-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column: Details --}}
            <div class="lg:col-span-2 space-y-8">
                <div class="card-rustic border-sienna">
                    <h3 class="text-xl font-lora font-bold text-sienna mb-6 border-b border-sienna border-opacity-10 pb-2">Transaction Information</h3>
                    <div class="grid grid-cols-2 gap-y-6 gap-x-4">
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-sage font-black">Receipt Number</p>
                            <p class="font-mono text-lg font-bold text-sienna">{{ $sale->receipt_number }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-sage font-black">Date</p>
                            <p class="text-base font-bold text-sienna">{{ $sale->date->format('M d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-sage font-black">Cashier/Employee</p>
                            <p class="text-base font-bold text-sienna">{{ $sale->employee->user->name ?? $sale->employee->employee_name }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-sage font-black">Payment Type</p>
                            <span class="inline-block bg-sage bg-opacity-10 text-sage-dark px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">
                                {{ $sale->payment_type }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-rustic border-sage">
                    <h3 class="text-xl font-lora font-bold text-sienna mb-6 border-b border-sienna border-opacity-10 pb-2">Product Details</h3>
                    <div class="grid grid-cols-2 gap-y-6 gap-x-4">
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-sage font-black">Product Name</p>
                            <p class="text-base font-bold text-sienna">{{ $sale->product->product_name }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-sage font-black">Category</p>
                            <p class="text-base font-bold text-sienna">{{ $sale->product->category->category_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-sage font-black">Quantity Sold</p>
                            <p class="text-base font-bold text-sienna">{{ $sale->sold }} units</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-sage font-black">Unit Price</p>
                            <p class="text-base font-bold text-sienna">₱{{ number_format($sale->product->selling_price, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="card-rustic border-terracotta">
                    <h3 class="text-xl font-lora font-bold text-sienna mb-6 border-b border-sienna border-opacity-10 pb-2">Customer Information</h3>
                    <div class="grid grid-cols-2 gap-y-6 gap-x-4">
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-sage font-black">Customer Name</p>
                            <p class="text-base font-bold text-sienna">{{ $sale->customer->name ?? 'Walk-in' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-sage font-black">Customer Phone</p>
                            <p class="text-base font-bold text-sienna">{{ $sale->customer->phone ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Summary --}}
            <div class="lg:col-span-1">
                <div class="card-rustic border-sienna bg-cream bg-opacity-20 sticky top-24">
                    <h3 class="text-xl font-lora font-bold text-sienna mb-6 border-b border-sienna border-opacity-20 pb-2">Summary</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between text-sm font-medium">
                            <span class="text-sage uppercase tracking-widest">Subtotal</span>
                            <span class="text-sienna">₱{{ number_format($sale->product->selling_price * $sale->sold, 2) }}</span>
                        </div>
                        
                        @if($sale->discount_amount > 0)
                            <div class="flex justify-between text-sm font-medium text-red-600">
                                <span class="uppercase tracking-widest">Discount @if($sale->discountType) ({{ $sale->discountType->discount_name }}) @endif</span>
                                <span>-₱{{ number_format($sale->discount_amount, 2) }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between text-sm font-medium text-green-600">
                            <span class="uppercase tracking-widest">VAT ({{ $sale->vat_rate }}%)</span>
                            <span>+₱{{ number_format($sale->vat_amount, 2) }}</span>
                        </div>

                        <div class="pt-4 border-t-2 border-sienna border-dashed mt-4">
                            <div class="flex justify-between items-end">
                                <span class="text-[10px] font-black text-sienna uppercase tracking-[0.2em]">Total Amount</span>
                                <span class="text-3xl font-formal text-terracotta font-black">₱{{ number_format($sale->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <a href="{{ route('sales.print', $sale->id) }}" class="w-full bg-sienna hover:bg-sienna-dark text-cream font-bold py-4 rounded-xl shadow-rustic transition-all flex justify-center items-center uppercase tracking-widest text-xs">
                            Generate Receipt
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
