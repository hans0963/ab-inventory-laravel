<x-app-layout>

    <!-- Header -->
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-3xl font-bold text-gray-800">
                    Raw Materials Inventory
                </h2>
                <p class="text-sm text-gray-500">
                    Track ingredients and expiration dates
                </p>
            </div>

            <a href="{{ route('raw-materials.create') }}"
               class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2 rounded-lg shadow">
                + Stock In
            </a>
        </div>
    </x-slot>

    <!-- Content -->
    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <!-- Alerts -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- Near Expiry -->
            <div class="flex items-center gap-4 bg-yellow-100 border border-yellow-300 text-yellow-800 px-5 py-4 rounded-xl">
                <span class="text-2xl">⚠️</span>
                <div>
                    <p class="font-semibold">
                        {{ $nearExpiryCount }} items near expiry
                    </p>
                    <p class="text-sm">Check and use soon</p>
                </div>
            </div>

            <!-- Expired -->
            <div class="flex items-center gap-4 bg-red-100 border border-red-300 text-red-700 px-5 py-4 rounded-xl">
                <span class="text-2xl">⚠️</span>
                <div>
                    <p class="font-semibold">
                        {{ $expiredCount }} expired items
                    </p>
                    <p class="text-sm">Remove from inventory</p>
                </div>
            </div>

        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-xl shadow overflow-hidden">

            <!-- Search -->
            <div class="p-4 border-b">
                <input type="text"
                       placeholder="Search raw materials..."
                       class="w-full border rounded-lg px-4 py-2 focus:ring focus:ring-orange-200">
            </div>

            <!-- Table -->
            <table class="w-full text-left">
                <thead class="bg-gray-100 text-gray-600 text-sm">
                    <tr>
                        <th class="px-6 py-3">Material Name</th>
                        <th class="px-6 py-3">Quantity</th>
                        <th class="px-6 py-3">Unit</th>
                        <th class="px-6 py-3">Expiration Date</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Supplier</th>
                        <th class="px-6 py-3 text-center">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y">

                    @foreach ($materials as $material)

                        @php
                            $today = \Carbon\Carbon::today();
                            $expiry = \Carbon\Carbon::parse($material->expiration_date);

                            if ($expiry->lt($today)) {
                                $status = 'Expired';
                                $badge = 'bg-red-100 text-red-600';
                            } elseif ($expiry->diffInDays($today) <= 7) {
                                $status = 'Near Expiry';
                                $badge = 'bg-yellow-100 text-yellow-700';
                            } else {
                                $status = 'Good';
                                $badge = 'bg-gray-200 text-gray-700';
                            }
                        @endphp

                        <tr class="hover:bg-gray-50">

                            <td class="px-6 py-4 font-semibold text-gray-800">
                                {{ $material->material_name }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $material->quantity }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $material->unit }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $material->expiration_date }}
                            </td>

                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                                    {{ $status }}
                                </span>
                            </td>

                            <td class="px-6 py-4">
                                {{ $material->supplier }}
                            </td>

                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-3">

                                    <a href="{{ route('raw-materials.edit', $material->id) }}"
                                       class="text-orange-500 hover:text-orange-600">
                                        ✏️
                                    </a>

                                    <x-alert-delete
                                        route="{{ route('raw-materials.destroy', $material->id) }}"
                                        message="Delete this material?" />

                                </div>
                            </td>

                        </tr>

                    @endforeach

                </tbody>
            </table>

            <!-- Pagination -->
            <div class="p-4">
                {{ $materials->links() }}
            </div>

        </div>

    </div>

</x-app-layout>