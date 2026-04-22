<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Customers') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Customer List</h3>
                        <a href="{{ route('customers.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                            + Add Customer
                        </a>
                    </div>

                    <table class="w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-200 dark:bg-gray-700">
                                <th class="border p-2">#</th>
                                <th class="border p-2">Name</th>
                                <th class="border p-2">Email</th>
                                <th class="border p-2">Phone</th>
                                <th class="border p-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customers as $customer)
                                <tr class="border">
                                    <td class="border p-2">{{ $loop->iteration }}</td>
                                    <td class="border p-2">{{ $customer->name }}</td>
                                    <td class="border p-2">{{ $customer->email }}</td>
                                    <td class="border p-2">{{ $customer->phone }}</td>
                                    <td class="border p-2 text-center">
                                        <a href="{{ route('customers.edit', $customer) }}" class="px-2 py-1 bg-yellow-500 text-white rounded">Edit</a>
                                        <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="px-2 py-1 bg-red-500 text-white rounded" onclick="return confirm('Delete customer?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $customers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
