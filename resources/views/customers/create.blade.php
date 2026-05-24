<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-md shadow-sm border-b border-sienna">
            <h2 class="font-pacifico text-3xl text-sienna leading-tight">
                {{ __('Create New Customer') }}
            </h2>
            <p class="font-inter text-sage">Add a new customer to your records</p>
        </div>
    </x-slot>

    <div class="py-6 max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md p-8 border-l-4 border-sienna">
            <form method="POST" action="{{ route('customers.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-sienna mb-2">Full Name *</label>
                    <input type="text" name="name" required 
                           class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10"
                           placeholder="Enter customer name">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-sienna mb-2">Email Address</label>
                    <input type="email" name="email" 
                           class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10"
                           placeholder="customer@example.com">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-sienna mb-2">Phone Number</label>
                    <input type="text" name="phone" 
                           class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10"
                           placeholder="e.g. 09123456789">
                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-sienna mb-2">Address</label>
                    <textarea name="address" rows="3"
                              class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10"
                              placeholder="Enter customer address"></textarea>
                    @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-sienna mb-2">Customer Type *</label>
                    <select name="customer_type" required
                            class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10">
                        <option value="Regular">Regular</option>
                        <option value="Senior">Senior</option>
                        <option value="PWD">PWD</option>
                        <option value="VIP">VIP</option>
                        <option value="Credit/Loan Customer">Credit/Loan Customer</option>
                    </select>
                    @error('customer_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-sienna mb-2">Credit Limit</label>
                        <input type="number" step="0.01" min="0" name="credit_limit" value="{{ old('credit_limit', 0) }}"
                               class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10">
                        @error('credit_limit') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-sienna mb-2">Credit Due Date</label>
                        <input type="date" name="credit_due_date" value="{{ old('credit_due_date') }}"
                               class="w-full border-sienna focus:ring-terracotta focus:border-terracotta rounded-md shadow-sm bg-cream bg-opacity-10">
                        @error('credit_due_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <input type="hidden" name="status" value="Active">

                <div class="flex justify-between items-center pt-4 border-t border-tan border-opacity-30">
                    <button type="button" onclick="window.location='{{ route('customers.index') }}'" 
                            class="text-gray-500 hover:text-sienna font-semibold transition">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-sienna hover:bg-opacity-90 text-cream font-inter px-10 py-2 rounded-lg shadow-md transition font-bold uppercase tracking-widest">
                        Save Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
