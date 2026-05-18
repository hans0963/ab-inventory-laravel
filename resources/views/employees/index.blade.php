<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">
                    {{ __('Bakeshop Personnel') }}
                </h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Manage your team and positions</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="card-rustic border-sienna">
            {{-- Header with search + add employee --}}
            <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                <div class="w-full md:w-1/3">
                    <form action="{{ route('employees.index') }}" method="GET">
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Search employees..." 
                                   class="w-full rounded-xl border-sienna border-opacity-20 bg-cream bg-opacity-20 py-3 pl-12 pr-4 focus:ring-2 focus:ring-terracotta focus:border-terracotta transition-all shadow-inner">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-sienna opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                    </form>
                </div>
                <a href="{{ route('employees.create') }}" 
                   class="bg-terracotta hover:bg-terracotta-dark text-cream font-bold px-8 py-3 rounded-xl shadow-rustic transition-all hover:-translate-y-0.5 active:translate-y-0 flex items-center">
                    <span class="mr-2 text-xl">+</span> Add Employee
                </a>
            </div>

            {{-- Employees Table --}}
            <div class="overflow-hidden rounded-xl border border-sienna border-opacity-10 shadow-sm">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-sienna text-cream uppercase text-xs tracking-widest font-bold">
                            <th class="px-6 py-4 text-left">ID</th>
                            <th class="px-6 py-4 text-left">Employee Name</th>
                            <th class="px-6 py-4 text-left">Email</th>
                            <th class="px-6 py-4 text-left">Phone</th>
                            <th class="px-6 py-4 text-left">Position</th>
                            <th class="px-6 py-4 text-left">Role</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                        @forelse ($employees as $employee)
                            <tr class="hover:bg-cream hover:bg-opacity-20 transition-colors">
                                <td class="px-6 py-5 font-bold text-sienna opacity-60">#{{ $employee->id }}</td>
                                <td class="px-6 py-5 text-sienna font-bold text-base">{{ $employee->employee_name }}</td>
                                <td class="px-6 py-5 text-sage font-medium">{{ $employee->employee_email }}</td>
                                <td class="px-6 py-5 text-sienna opacity-80">{{ $employee->employee_phone }}</td>
                                <td class="px-6 py-5">
                                    <span class="bg-sage bg-opacity-10 text-sage-dark px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                                        {{ $employee->position }}
                                    </span>
                                </td>
                                <td class="px-6 py-5">
                                    @if($employee->user)
                                        <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">
                                            {{ $employee->user->role }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 italic text-xs">No account</span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <div class="flex items-center justify-center space-x-3">
                                        <a href="{{ route('employees.edit', $employee->id) }}" class="p-2 text-sage hover:text-sienna hover:bg-sage hover:bg-opacity-10 rounded-lg transition-all">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <x-alert-delete 
                                            route="{{ route('employees.destroy', $employee->id) }}" 
                                            message="Are you sure you want to remove this employee?" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sage italic font-medium">No personnel records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-8">
                {{ $employees->links() }}
            </div>
        </div>
    </div>

</x-app-layout>
