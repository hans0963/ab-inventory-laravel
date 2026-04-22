<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Purchases') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Purchase List</h2>
                    <a href="{{ route('purchases.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded">
                        + Add Purchase
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="py-2 px-4 border">Date</th>
                                <th class="py-2 px-4 border">Supplier</th>
                                <th class="py-2 px-4 border">Company</th>
                                <th class="py-2 px-4 border">Reference</th>
                                <th class="py-2 px-4 border">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($purchases as $purchase)
                            <tr class="border-b">
                                <td class="py-2 px-4 border">{{ $purchase->purchase_date }}</td>
                                <td class="py-2 px-4 border">{{ $purchase->supplier->suppliers_name }}</td>
                                <td class="py-2 px-4 border">{{ $purchase->supplier->suppliers_company }}</td>
                                <td class="py-2 px-4 border">{{ $purchase->reference }}</td>
                                <td class="py-2 px-4 border flex space-x-2">
                                    <a href="{{ route('purchases.show', $purchase->id) }}" class="text-blue-500">View Details</a>
                                    {{-- <a href="{{ route('purchases.edit', $purchase->id) }}" class="text-yellow-500">Edit</a> --}}
                                    <form action="{{ route('purchases.destroy', $purchase->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500">Delete</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $purchases->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
