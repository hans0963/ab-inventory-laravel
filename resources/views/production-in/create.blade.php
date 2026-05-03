<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-md shadow-sm border-b border-sienna">
            <h2 class="font-pacifico text-3xl text-sienna leading-tight">
                {{ __('Create Production In') }}
            </h2>
            <p class="font-inter text-sage">Record fresh baking output</p>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="card-rustic border-sage">
            <h3 class="text-xl font-bold text-sienna mb-6">Record New Production Batch</h3>
            
            <form action="{{ route('production-in.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label for="product_id" class="block text-sm font-semibold text-sienna mb-2">Select Product</label>
                    <select name="product_id" id="product_id" class="w-full rounded-xl border-sienna border-opacity-20 bg-cream bg-opacity-20 py-3 px-4 focus:ring-2 focus:ring-terracotta focus:border-terracotta transition-all shadow-inner">
                        <option value="">-- Choose a Product --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->product_name }} (Current: {{ $product->quantity }})</option>
                        @endforeach
                    </select>
                    @error('product_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <x-input label="Quantity Produced" name="quantity" type="number" placeholder="Enter amount baked..." required />
                </div>

                <div class="flex justify-end gap-4 pt-4">
                    <a href="{{ route('production-in.index') }}" class="px-6 py-2.5 rounded-xl border border-sienna text-sienna font-bold hover:bg-cream transition-all">Cancel</a>
                    <button type="submit" class="bg-terracotta text-cream font-bold px-8 py-2.5 rounded-xl shadow-rustic hover:bg-terracotta-dark transition-all">Record Production</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
