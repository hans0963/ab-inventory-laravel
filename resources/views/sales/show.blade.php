<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center w-full bg-cream p-4 rounded-xl shadow-sm border border-sienna border-opacity-20 gap-4">
            <div class="text-center md:text-left">
                <h2 class="font-formal text-4xl text-sienna leading-tight">
                    {{ __('Sale Details') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-[10px]">Transaction overview and receipt summary</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-4 w-full md:w-auto">
                <a href="{{ route('sales.print', $sale->id) }}" class="bg-sage hover:bg-sage-dark text-white font-bold px-6 py-3 rounded-xl shadow-md transition-all flex items-center justify-center">
                    Print Receipt
                </a>
                <a href="{{ route('sales.index') }}" class="bg-cream hover:bg-sienna hover:text-cream text-sienna border border-sienna font-bold px-6 py-3 rounded-xl transition-all flex items-center justify-center text-[10px] uppercase tracking-widest">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8 max-w-5xl mx-auto mt-8 px-4 sm:px-0">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column: Details --}}
            <div class="lg:col-span-2 space-y-8">
                <div class="card-rustic border-sienna p-6 md:p-8">
                    <h3 class="text-xl font-lora font-bold text-sienna mb-6 border-b border-sienna border-opacity-10 pb-2 uppercase tracking-tighter">Transaction Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8">
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-sage font-black mb-1">Receipt Number</p>
                            <p class="font-mono text-xl font-black text-sienna">{{ $sale->receipt_number }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-sage font-black mb-1">Transaction Date</p>
                            <p class="text-base font-bold text-sienna">{{ $sale->date->format('M d, Y h:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-sage font-black mb-1">Cashier/Employee</p>
                            <p class="text-base font-bold text-sienna">{{ $sale->employee->user->name ?? $sale->employee->employee_name }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-sage font-black mb-1">Payment Mode</p>
                            <span class="inline-block bg-terracotta bg-opacity-10 text-terracotta px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">
                                {{ $sale->payment_mode_label }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-rustic border-sage p-6 md:p-8">
                    <h3 class="text-xl font-lora font-bold text-sienna mb-6 border-b border-sienna border-opacity-10 pb-2 uppercase tracking-tighter">Product Details</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8">
                        <div class="sm:col-span-2">
                            <p class="text-[10px] uppercase tracking-widest text-sage font-black mb-1">Product Name</p>
                            <p class="text-xl font-black text-sienna">{{ $sale->product->product_name }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-sage font-black mb-1">Category</p>
                            <p class="text-base font-bold text-sienna">{{ $sale->product->category->category_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-sage font-black mb-1">Quantity Sold</p>
                            <p class="text-base font-bold text-sienna">{{ $sale->sold }} units</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-sage font-black mb-1">Unit Price</p>
                            <p class="text-base font-bold text-sienna">₱{{ number_format($sale->product->selling_price, 2) }}</p>
                        </div>
                    </div>
                </div>

                <div class="card-rustic border-tan p-6 md:p-8">
                    <h3 class="text-xl font-lora font-bold text-sienna mb-6 border-b border-sienna border-opacity-10 pb-2 uppercase tracking-tighter">Customer Information</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8">
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-sage font-black mb-1">Customer Name</p>
                            <p class="text-base font-bold text-sienna">{{ $sale->customer->name ?? 'Walk-in' }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase tracking-widest text-sage font-black mb-1">Contact Details</p>
                            <p class="text-base font-bold text-sienna">{{ $sale->customer->phone ?? 'No contact provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Summary --}}
            <div class="lg:col-span-1">
                <div class="card-rustic border-terracotta bg-cream bg-opacity-20 sticky top-24 overflow-hidden">
                    <div class="bg-terracotta p-4">
                        <h3 class="font-formal text-2xl text-cream">Order Summary</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between text-sage font-medium uppercase text-[10px] tracking-widest">
                            <span>Subtotal</span>
                            <span class="text-sienna font-bold">₱{{ number_format($sale->product->selling_price * $sale->sold, 2) }}</span>
                        </div>
                        
                        @if($sale->discount_amount > 0)
                            <div class="flex justify-between text-red-600 font-medium uppercase text-[10px] tracking-widest">
                                <span>Discount @if($sale->discountType) ({{ $sale->discountType->discount_name }}) @endif</span>
                                <span class="font-bold">-₱{{ number_format($sale->discount_amount, 2) }}</span>
                            </div>
                        @endif

                        <div class="flex justify-between text-green-600 font-medium uppercase text-[10px] tracking-widest">
                            <span>VAT ({{ $sale->vat_rate }}%)</span>
                            <span class="font-bold">+₱{{ number_format($sale->vat_amount, 2) }}</span>
                        </div>

                        <div class="pt-4 border-t-2 border-sienna border-dashed mt-4">
                            <div class="flex justify-between items-end">
                                <span class="font-formal text-2xl text-sienna leading-none">Total</span>
                                <span class="font-black text-3xl text-terracotta leading-none">₱{{ number_format($sale->total_amount, 2) }}</span>
                            </div>
                        </div>

                        <div class="mt-8">
                            <a href="{{ route('sales.print', $sale->id) }}" class="w-full bg-sienna hover:bg-sienna-dark text-cream font-black py-4 rounded-xl shadow-rustic transition-all flex justify-center items-center uppercase tracking-widest text-xs">
                                Generate Receipt
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
