<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Employee Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('employees.update', $employee->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Employee Name -->
                        <div class="mb-4">
                            <x-input-label for="employee_name" :value="__('Full Name')" />
                            <x-text-input id="employee_name" type="text" name="employee_name" class="block mt-1 w-full" 
                                value="{{ old('employee_name', $employee->employee_name) }}" required />
                            @error('employee_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Employee Email -->
                        <div class="mb-4">
                            <x-input-label for="employee_email" :value="__('Email')" />
                            <x-text-input id="employee_email" type="email" name="employee_email" class="block mt-1 w-full" 
                                value="{{ old('employee_email', $employee->employee_email) }}" required />
                            @error('employee_email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Employee Phone -->
                        <div class="mb-4">
                            <x-input-label for="employee_phone" :value="__('Phone Number')" />
                            <x-text-input id="employee_phone" type="text" name="employee_phone" class="block mt-1 w-full" 
                                value="{{ old('employee_phone', $employee->employee_phone) }}" />
                            @error('employee_phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Position -->
                        <div class="mb-4">
                            <x-input-label for="position" :value="__('Position')" />
                            <x-text-input id="position" type="text" name="position" class="block mt-1 w-full" 
                                value="{{ old('position', $employee->position) }}" required />
                            @error('position') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Role (Login Permission) -->
                        <div class="mb-4">
                            <x-input-label for="role" :value="__('System Role (Login Permission)')" />
                            <select id="role" name="role" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                <option value="manager" {{ old('role', $employee->user->role ?? '') === 'manager' ? 'selected' : '' }}>Manager (Can access Inventory & Reports)</option>
                                <option value="cashier" {{ old('role', $employee->user->role ?? '') === 'cashier' ? 'selected' : '' }}>Cashier (Can access Sales only)</option>
                            </select>
                            @error('role') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="text-end">
                            <x-primary-button type="submit">
                                {{ __('Update') }}
                            </x-primary-button>
                            <x-secondary-button type="button" onclick="window.location='{{ route('employees.index') }}'">
                                {{ __('Cancel') }}
                            </x-secondary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
