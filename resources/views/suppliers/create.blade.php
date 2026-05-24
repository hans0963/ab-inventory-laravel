<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ __('Add Supplier') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Register supplier details, items, and payment terms</p>
            </div>
        </div>
    </x-slot>

    <div class="card-rustic border-sienna max-w-4xl mx-auto mt-8">
        <form method="POST" action="{{ route('suppliers.store') }}" class="space-y-8">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <x-input-label for="suppliers_company" value="Business Name *" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="suppliers_company" type="text" name="suppliers_company" value="{{ old('suppliers_company') }}" class="mt-1 block w-full !text-sm" required />
                    <x-input-error :messages="$errors->get('suppliers_company')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="suppliers_name" value="Contact Person Name *" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="suppliers_name" type="text" name="suppliers_name" value="{{ old('suppliers_name') }}" class="mt-1 block w-full !text-sm" required />
                    <x-input-error :messages="$errors->get('suppliers_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="suppliers_phone" value="Phone Number" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="suppliers_phone" type="text" name="suppliers_phone" value="{{ old('suppliers_phone') }}" class="mt-1 block w-full !text-sm" />
                    <x-input-error :messages="$errors->get('suppliers_phone')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="suppliers_email" value="Email Address" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="suppliers_email" type="email" name="suppliers_email" value="{{ old('suppliers_email') }}" class="mt-1 block w-full !text-sm" />
                    <x-input-error :messages="$errors->get('suppliers_email')" class="mt-2" />
                </div>

                <div class="md:col-span-2">
                    <x-input-label for="suppliers_address" value="Address" class="text-[10px] uppercase tracking-widest text-sage" />
                    <textarea id="suppliers_address" name="suppliers_address" rows="3" class="input-artisan mt-1">{{ old('suppliers_address') }}</textarea>
                    <x-input-error :messages="$errors->get('suppliers_address')" class="mt-2" />
                </div>

                <div class="md:col-span-2">
                    <x-input-label for="items_supplied" value="Products/Materials They Provide" class="text-[10px] uppercase tracking-widest text-sage" />
                    <textarea id="items_supplied" name="items_supplied" rows="3" class="input-artisan mt-1" placeholder="e.g. Flour, sugar, butter, packaging materials">{{ old('items_supplied') }}</textarea>
                    <x-input-error :messages="$errors->get('items_supplied')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="payment_terms" value="Payment Terms *" class="text-[10px] uppercase tracking-widest text-sage" />
                    <select id="payment_terms" name="payment_terms" required class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm">
                        <option value="Cash" {{ old('payment_terms', 'Cash') === 'Cash' ? 'selected' : '' }}>Cash</option>
                        <option value="Credit" {{ old('payment_terms') === 'Credit' ? 'selected' : '' }}>Credit</option>
                    </select>
                    <x-input-error :messages="$errors->get('payment_terms')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="status" value="Status *" class="text-[10px] uppercase tracking-widest text-sage" />
                    <select id="status" name="status" required class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm">
                        <option value="Active" {{ old('status', 'Active') === 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('status') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>
            </div>

            <div class="flex justify-between items-center pt-8 border-t border-sienna border-opacity-10">
                <a href="{{ route('suppliers.index') }}" class="text-xs font-black text-sage uppercase tracking-widest hover:text-sienna transition">
                    Back to Suppliers
                </a>
                <x-primary-button class="!bg-terracotta hover:!bg-terracotta-dark shadow-rustic">
                    Save Supplier
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
