<x-app-layout>
    @if ($employees->isEmpty())
        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="text-center p-6 bg-white dark:bg-gray-800 overflow-hidden sm:rounded-lg text-gray-800 dark:text-gray-200 rounded-lg shadow">
                    <h3 class="text-lg font-semibold">{{ __('No employee found') }}</h3>
                    <p class="text-sm mt-2">{{ __('Try adjusting your search or filter to find what you\'re looking for.') }}</p>
                    <a href="{{ route('employees.create') }}" class="mt-4 inline-block bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">
                        {{ __('Add employee') }}
                    </a>
                </div>
            </div>
        </div>
    @else
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Employees') }}
            </h2>
        </x-slot>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="mb-4">
                        <div class="flex justify-between items-center">
                            <a href="{{ route('employees.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">Add Employee</a>
                        
                            <form action="{{ route('employees.index') }}" method="GET" class="flex items-center">
                                <input type="text" name="search" placeholder="Search Employee..." value="{{ request('search') }}"
                                    class="border p-2 rounded mr-2">
                                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Search</button>
                            </form>
                        </div>
                        {{ $employees->links() }}
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-200 dark:border-gray-700 rounded-lg">
                            <thead class="bg-gray-200 text-black dark:bg-gray-700 dark:text-white">
                                <tr>
                                    <th class="px-4 py-3 border-b text-left">ID</th>
                                    <th class="px-4 py-3 border-b text-left">Employee Name</th>
                                    <th class="px-4 py-3 border-b text-left">Email</th>
                                    <th class="px-4 py-3 border-b text-left">Phone</th>
                                    <th class="px-4 py-3 border-b text-left">Position</th>
                                    <th class="px-4 py-3 border-b text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-white">
                                @foreach ($employees as $employee)
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <td class="px-4 py-3">{{ $employee->id }}</td>
                                    <td class="px-4 py-3">{{ $employee->employee_name }}</td>
                                    <td class="px-4 py-3">{{ $employee->employee_email }}</td>
                                    <td class="px-4 py-3">{{ $employee->employee_phone }}</td>
                                    <td class="px-4 py-3">{{ $employee->position }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex gap-2 justify-center">
                                            <a href="{{ route('employees.edit', $employee->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white py-1 px-3 rounded text-sm">
                                                {{ __('Edit') }}
                                            </a>
                                            <x-alert-delete route="{{ route('employees.destroy', $employee->id) }}" message="Are you sure you want to delete this employee?" />
                                        </div>
                                    </td>
                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $employees->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

</x-app-layout>
