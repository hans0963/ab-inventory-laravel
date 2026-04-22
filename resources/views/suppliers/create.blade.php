<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add Supplier') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('suppliers.store') }}">
                        @csrf
                    
                        <!-- Supplier Name -->
                        <div class="mb-4">
                            <x-input-label for="suppliers_name" :value="__('Full Name')" />
                            <x-text-input id="suppliers_name" type="text" name="suppliers_name" class="block mt-1 w-full" required />
                        </div>
                        
                        <div class="mb-4">
                            <x-input-label for="suppliers_company" :value="__('Company')" />
                            <x-text-input id="suppliers_company" type="text" name="suppliers_company" class="block mt-1 w-full" />
                        </div>
                        
                        <div class="mb-4">
                            <x-input-label for="suppliers_email" :value="__('Email Address')" />
                            <x-text-input id="suppliers_email" type="email" name="suppliers_email" class="block mt-1 w-full" />
                        </div>
                        
                        <div class="mb-4">
                            <x-input-label for="suppliers_phone" :value="__('Phone Number')" />
                            <x-text-input id="suppliers_phone" type="text" name="suppliers_phone" class="block mt-1 w-full" />
                        </div>
                        
                        <div class="mb-4">
                            <x-input-label for="suppliers_address" :value="__('Address')" />
                            <x-text-input id="suppliers_address" type="text" name="suppliers_address" class="block mt-1 w-full" />
                        </div>
                    
                        <!-- Buttons -->
                        <div class="text-end">
                            <x-primary-button type="submit">
                                {{ __('Save') }}
                            </x-primary-button>
                            <x-secondary-button type="button" onclick="window.location='{{ route('suppliers.index') }}'">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
