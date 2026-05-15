<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-md shadow-sm border-b border-sienna">
            <div>
                <h2 class="font-formal text-3xl text-sienna leading-tight">
                    {{ __('Stock In: Baking Ingredients') }}
                </h2>
                <p class="font-inter text-sage">Add quantity to existing raw materials</p>
            </div>
            <a href="{{ route('raw-materials.index') }}" 
               class="bg-sienna hover:bg-opacity-90 text-white font-bold px-6 py-2 rounded-lg shadow-md transition flex items-center">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-xl border-t-4 border-green-600 p-8">
            <form action="{{ route('raw-materials.processIn') }}" method="POST">
                @csrf

                <div class="mb-8">
                    <label class="block text-sm font-semibold text-sienna mb-2">Reference / Note *</label>
                    <input type="text" name="reason" required 
                           class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10"
                           placeholder="e.g. Weekly Restock, Delivery from Supplier X, etc.">
                </div>

                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-lora font-bold text-sienna">Select Ingredients</h3>
                        <a href="{{ route('raw-materials.create') }}" class="text-terracotta font-black text-xs hover:underline italic">
                            + Register New Ingredient instead?
                        </a>
                    </div>
                    
                    <div id="material-rows" class="space-y-4">
                        <div class="flex flex-col md:flex-row gap-4 items-end bg-cream bg-opacity-20 p-4 rounded-xl border border-sienna border-opacity-10">
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-sage uppercase tracking-widest mb-1">Ingredient</label>
                                <select name="material_id[]" required 
                                        class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-white">
                                    <option value="">Select Ingredient</option>
                                    @foreach($materials as $material)
                                        <option value="{{ $material->id }}">
                                            {{ $material->material_name }} (Current: {{ $material->quantity }} {{ $material->unit }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-full md:w-32">
                                <label class="block text-xs font-bold text-sage uppercase tracking-widest mb-1">Qty to Add</label>
                                <input type="number" name="quantity[]" required min="1"
                                       class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-white"
                                       placeholder="0">
                            </div>
                            <button type="button" class="remove-row p-2 text-red-500 hover:text-red-700 transition-colors" onclick="this.parentElement.remove()">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="button" onclick="addRow()" 
                            class="mt-4 inline-flex items-center text-terracotta font-bold hover:underline">
                        <span class="mr-1">+</span> Add Another Ingredient
                    </button>
                </div>

                <div class="flex justify-end pt-6 border-t border-sienna border-opacity-10">
                    <button type="submit" 
                            class="bg-green-600 hover:bg-green-700 text-white font-black px-12 py-3 rounded-xl shadow-rustic transition-all hover:-translate-y-0.5 active:translate-y-0">
                        Process Stock In
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function addRow() {
            const container = document.getElementById('material-rows');
            const newRow = container.children[0].cloneNode(true);
            
            // Clear inputs in the new row
            newRow.querySelector('select').value = '';
            newRow.querySelector('input[type="number"]').value = '';
            
            container.appendChild(newRow);
        }
    </script>
</x-app-layout>
