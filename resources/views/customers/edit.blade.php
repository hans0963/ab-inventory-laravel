<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Customer Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('customers.update', $customer) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Full Name')" />
                            <x-text-input id="name" type="text" name="name" class="block mt-1 w-full" value="{{ old('name', $customer->name) }}" required />
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" type="email" name="email" class="block mt-1 w-full" value="{{ old('email', $customer->email) }}" />
                            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <x-input-label for="phone" :value="__('Phone Number')" />
                            <x-text-input id="phone" type="text" name="phone" class="block mt-1 w-full" value="{{ old('phone', $customer->phone) }}" />
                            @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <x-input-label for="address" :value="__('Address')" />
                            <textarea id="address" name="address" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('address', $customer->address) }}</textarea>
                            @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <x-input-label for="customer_type" :value="__('Customer Type')" />
                            <select id="customer_type" name="customer_type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach(['Regular','Senior','PWD','VIP','Credit/Loan Customer'] as $type)
                                    <option value="{{ $type }}" {{ old('customer_type', $customer->customer_type) === $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                            @error('customer_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <x-input-label for="credit_limit" :value="__('Credit Limit')" />
                            <x-text-input id="credit_limit" type="number" step="0.01" min="0" name="credit_limit" class="block mt-1 w-full" value="{{ old('credit_limit', $customer->credit_limit) }}" />
                            @error('credit_limit') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <x-input-label for="current_balance" :value="__('Current Balance')" />
                            <x-text-input id="current_balance" type="number" step="0.01" min="0" name="current_balance" class="block mt-1 w-full" value="{{ old('current_balance', $customer->current_balance) }}" />
                            @error('current_balance') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <x-input-label for="credit_due_date" :value="__('Credit Due Date')" />
                            <x-text-input id="credit_due_date" type="date" name="credit_due_date" class="block mt-1 w-full" value="{{ old('credit_due_date', optional($customer->credit_due_date)->format('Y-m-d')) }}" />
                            @error('credit_due_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach(['Active','Inactive'] as $status)
                                    <option value="{{ $status }}" {{ old('status', $customer->status ?? 'Active') === $status ? 'selected' : '' }}>{{ $status }}</option>
                                @endforeach
                            </select>
                            @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="text-end">
                            <x-primary-button type="submit">Update</x-primary-button>
                            <x-secondary-button type="button" onclick="window.location='{{ route('customers.index') }}'">Cancel</x-secondary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
