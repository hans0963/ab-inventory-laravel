<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ __('Edit Employee') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">{{ $employee->employee_name }}</p>
            </div>
        </div>
    </x-slot>

    <div class="card-rustic border-sienna max-w-4xl mx-auto mt-8">
        <form method="POST" action="{{ route('employees.update', $employee->id) }}" class="space-y-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <x-input-label for="employee_name" value="Full Name *" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="employee_name" type="text" name="employee_name" value="{{ old('employee_name', $employee->employee_name) }}" class="mt-1 block w-full !text-sm" required />
                    <x-input-error :messages="$errors->get('employee_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="employee_email" value="Email Address *" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="employee_email" type="email" name="employee_email" value="{{ old('employee_email', $employee->employee_email) }}" class="mt-1 block w-full !text-sm" required />
                    <x-input-error :messages="$errors->get('employee_email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="employee_phone" value="Phone Number" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="employee_phone" type="text" name="employee_phone" value="{{ old('employee_phone', $employee->employee_phone) }}" class="mt-1 block w-full !text-sm" />
                    <x-input-error :messages="$errors->get('employee_phone')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="position" value="Position/Role *" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="position" type="text" name="position" value="{{ old('position', $employee->position) }}" class="mt-1 block w-full !text-sm" required />
                    <x-input-error :messages="$errors->get('position')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="role" value="System Access Level *" class="text-[10px] uppercase tracking-widest text-sage" />
                    <select id="role" name="role" required class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm">
                        <option value="admin" {{ old('role', $employee->user->role ?? '') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="manager" {{ old('role', $employee->user->role ?? '') === 'manager' ? 'selected' : '' }}>Manager</option>
                        <option value="cashier" {{ old('role', $employee->user->role ?? '') === 'cashier' ? 'selected' : '' }}>Cashier</option>
                        <option value="baker" {{ old('role', $employee->user->role ?? '') === 'baker' ? 'selected' : '' }}>Baker</option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="date_hired" value="Date Hired" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="date_hired" type="date" name="date_hired" value="{{ old('date_hired', $employee->date_hired?->format('Y-m-d')) }}" class="mt-1 block w-full !text-sm" />
                    <x-input-error :messages="$errors->get('date_hired')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="status" value="Status *" class="text-[10px] uppercase tracking-widest text-sage" />
                    <select id="status" name="status" required class="mt-1 block w-full border-sienna border-opacity-20 rounded-md shadow-sm focus:border-sienna focus:ring focus:ring-sienna focus:ring-opacity-50 text-sm">
                        <option value="Active" {{ old('status', $employee->status ?? 'Active') === 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('status', $employee->status ?? 'Active') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="Archived" {{ old('status', $employee->status ?? 'Active') === 'Archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="emergency_contact" value="Emergency Contact" class="text-[10px] uppercase tracking-widest text-sage" />
                    <x-text-input id="emergency_contact" type="text" name="emergency_contact" value="{{ old('emergency_contact', $employee->emergency_contact) }}" class="mt-1 block w-full !text-sm" />
                    <x-input-error :messages="$errors->get('emergency_contact')" class="mt-2" />
                </div>
            </div>

            <div class="flex justify-between items-center pt-8 border-t border-sienna border-opacity-10">
                <a href="{{ route('employees.show', $employee->id) }}" class="text-xs font-black text-sage uppercase tracking-widest hover:text-sienna transition">
                    Back to Profile
                </a>
                <x-primary-button class="!bg-terracotta hover:!bg-terracotta-dark shadow-rustic">
                    Update Employee
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
