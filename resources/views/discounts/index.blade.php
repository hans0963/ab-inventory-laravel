<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-md shadow-sm border-b border-sienna">
            <h2 class="font-pacifico text-3xl text-sienna leading-tight">
                {{ __('Discount Types') }}
            </h2>
            <a href="{{ route('discounts.create') }}" class="px-4 py-2 bg-sienna text-white rounded-md hover:bg-opacity-80">
                + New Discount Type
            </a>
        </div>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8">
        @if ($message = Session::get('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded">
                {{ $message }}
            </div>
        @endif

        @if ($message = Session::get('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded">
                {{ $message }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-cream border-b-2 border-sienna">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-sienna">Discount Name</th>
                            <th class="px-6 py-3 text-left font-semibold text-sienna">Percentage</th>
                            <th class="px-6 py-3 text-left font-semibold text-sienna">Description</th>
                            <th class="px-6 py-3 text-left font-semibold text-sienna">Status</th>
                            <th class="px-6 py-3 text-center font-semibold text-sienna">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($discountTypes as $discount)
                            <tr class="border-b hover:bg-cream hover:bg-opacity-20">
                                <td class="px-6 py-4 font-semibold text-sienna">{{ $discount->discount_name }}</td>
                                <td class="px-6 py-4">
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-semibold">
                                        {{ $discount->discount_percentage }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">{{ $discount->description ?? 'N/A' }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $discount->status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $discount->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center space-x-2">
                                    <a href="{{ route('discounts.edit', $discount->id) }}" class="text-blue-500 hover:text-blue-700">Edit</a>
                                    <form method="POST" action="{{ route('discounts.destroy', $discount->id) }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Are you sure?')" class="text-red-500 hover:text-red-700">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No discount types found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4">
                {{ $discountTypes->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
