<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Suppliers') }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Supplier List</h3>
                <a href="{{ route('suppliers.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    + Add New Supplier
                </a>
            </div>

            <table class="w-full border-collapse border border-gray-300 dark:border-gray-700 mt-4">
                <thead>
                    <tr class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">#</th>
                        <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Name</th>
                        <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Email</th>
                        <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Phone</th>
                        <th class="border border-gray-300 dark:border-gray-600 px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($suppliers as $supplier)
                        <tr class="border border-gray-300 dark:border-gray-600">
                            <td class="px-4 py-2">{{ $supplier->id }}</td>
                            <td class="px-4 py-2">{{ $supplier->suppliers_name }}</td>
                            <td class="px-4 py-2">{{ $supplier->suppliers_email }}</td>
                            <td class="px-4 py-2">{{ $supplier->suppliers_phone }}</td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex gap-2 justify-center"> 
                                    <a href="{{ route('suppliers.edit', $supplier->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white py-1 px-3 rounded text-sm">
                                        {{ __('Edit') }}
                                    </a>                                   
                                    <x-alert-delete route="{{ route('suppliers.destroy', $supplier->id) }}" message="Are you sure you want to delete this supplier?" />
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
