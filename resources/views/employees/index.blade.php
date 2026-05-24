<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ __('Employees') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Manage team records, access levels, and employment status</p>
            </div>
            <a href="{{ route('employees.create') }}" class="btn-terracotta">
                <span class="mr-2 text-xl">+</span> Add Employee
            </a>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="card-rustic border-sienna">
            <div class="mb-6">
                <form action="{{ route('employees.index') }}" method="GET" class="max-w-md">
                    <label for="search" class="sr-only">Search employees</label>
                    <div class="relative">
                        <input id="search" type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search name, email, phone, or position"
                               class="input-artisan pl-10">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-sienna opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto rounded-xl border border-sienna border-opacity-10">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-sienna text-cream uppercase text-xs tracking-widest">
                            <th class="px-4 py-3 text-left">Employee ID</th>
                            <th class="px-4 py-3 text-left">Full Name</th>
                            <th class="px-4 py-3 text-left">Role</th>
                            <th class="px-4 py-3 text-left">Email</th>
                            <th class="px-4 py-3 text-left">Phone Number</th>
                            <th class="px-4 py-3 text-left">Date Hired</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                        @forelse ($employees as $employee)
                            <tr class="hover:bg-cream hover:bg-opacity-20 transition-colors">
                                <td class="px-4 py-4 font-semibold text-sienna">#{{ $employee->id }}</td>
                                <td class="px-4 py-4">
                                    <div class="font-bold text-sienna">{{ $employee->employee_name }}</div>
                                    <div class="text-xs text-sage">{{ $employee->position }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">
                                        {{ $employee->user ? ucfirst($employee->user->role) : 'No Access' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-gray-600 italic">{{ $employee->employee_email }}</td>
                                <td class="px-4 py-4 text-gray-600">{{ $employee->employee_phone ?? 'N/A' }}</td>
                                <td class="px-4 py-4 text-sienna">{{ $employee->date_hired?->format('M d, Y') ?? 'N/A' }}</td>
                                <td class="px-4 py-4 text-center">
                                    @php
                                        $statusClass = match($employee->status) {
                                            'Active' => 'bg-green-100 text-green-800',
                                            'Inactive' => 'bg-yellow-100 text-yellow-800',
                                            'Archived' => 'bg-gray-100 text-gray-700',
                                            default => 'bg-gray-100 text-gray-700',
                                        };
                                    @endphp
                                    <span class="{{ $statusClass }} px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">
                                        {{ $employee->status ?? 'Active' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('employees.show', $employee->id) }}" class="p-2 text-sage hover:text-sienna hover:bg-sage hover:bg-opacity-10 rounded-lg transition" title="View Employee">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('employees.edit', $employee->id) }}" class="p-2 text-sage hover:text-sienna hover:bg-sage hover:bg-opacity-10 rounded-lg transition" title="Edit Employee">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        @if(($employee->status ?? 'Active') !== 'Archived')
                                            <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Archive this employee record?')" class="px-3 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-lg text-xs font-bold uppercase tracking-widest transition">
                                                    Archive
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-500 italic">No employee records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $employees->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
