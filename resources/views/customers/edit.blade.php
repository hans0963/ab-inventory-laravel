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
                                @foreach(['Regular','Senior','PWD','VIP'] as $type)
                                    <option value="{{ $type }}" {{ old('customer_type', $customer->customer_type) === $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                            @error('customer_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
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
