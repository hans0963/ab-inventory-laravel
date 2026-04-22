<div>
    <!-- Search Bar -->
    <input type="text" wire:model="search" placeholder="Search categories..." 
        class="border-gray-300 dark:border-gray-600 rounded-md p-2 mb-4 w-full">

    <!-- Table -->
    <table class="min-w-full bg-gray-800 text-white rounded-lg overflow-hidden">
        <thead>
            <tr class="bg-gray-900 text-white">
                <th class="px-4 py-2">ID</th>
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categories as $category)
                <tr class="border-t border-gray-700">
                    <td class="px-4 py-2">{{ $category->category_id }}</td>
                    <td class="px-4 py-2">{{ $category->category_name }}</td>
                    <td class="px-4 py-2 text-right">
                        <button class="bg-yellow-500 text-white px-3 py-1 rounded text-sm">Edit</button>
                        <button class="bg-red-500 text-white px-3 py-1 rounded text-sm">Delete</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>
