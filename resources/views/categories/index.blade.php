<x-app-layout>

    @if ($categories->isEmpty())
        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="text-center p-6 bg-white dark:bg-gray-800 rounded-lg shadow text-gray-800 dark:text-gray-200">
                    <h3 class="text-lg font-semibold">No Categories Available</h3>
                    <p class="text-sm mt-2">Please create a new category to proceed.</p>

                    <a href="{{ route('categories.create') }}"
                       class="mt-4 inline-block bg-orange-500 hover:bg-orange-600 text-white py-2 px-4 rounded-lg">
                        + Create Category
                    </a>
                </div>
            </div>
        </div>
    @else

        <!-- Header -->
        <x-slot name="header">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                        Categories
                    </h2>
                    <p class="text-sm text-gray-500">
                        Organize your products by category
                    </p>
                </div>

                <a href="{{ route('categories.create') }}"
                   class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2 rounded-lg shadow">
                    + Add Category
                </a>
            </div>
        </x-slot>

        <!-- Content -->
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <!-- Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    @foreach ($categories as $category)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 flex flex-col justify-between hover:shadow-lg transition">

                            <!-- Top -->
                            <div class="flex justify-between items-start">
                                <div class="bg-orange-100 p-3 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                         class="h-6 w-6 text-orange-500"
                                         fill="none"
                                         viewBox="0 0 24 24"
                                         stroke="currentColor">
                                        <path stroke-linecap="round"
                                              stroke-linejoin="round"
                                              stroke-width="2"
                                              d="M7 7h.01M3 11l8.586-8.586a2 2 0 012.828 0L21 9m-2 2v6a2 2 0 01-2 2h-6"/>
                                    </svg>
                                </div>

                                <div class="flex gap-2">
                                    <a href="{{ route('categories.edit', $category->id) }}"
                                       class="text-orange-500 hover:text-orange-600">
                                        ✏️
                                    </a>

                                    <x-alert-delete
                                        route="{{ route('categories.destroy', $category->id) }}"
                                        message="Are you sure you want to delete this category?" />
                                </div>
                            </div>

                            <!-- Middle -->
                            <div class="mt-4">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                                    {{ $category->category_name }}
                                </h3>

                                <p class="text-sm text-gray-500">
                                    {{ $category->description ?? 'No description available' }}
                                </p>
                            </div>

                            <!-- Bottom -->
                            <div class="mt-6 border-t pt-3 flex justify-between items-center text-sm text-gray-500">
                                <span>Products</span>
                                <span class="text-orange-500 font-bold">
                                    {{ $category->products_count ?? 0 }}
                                </span>
                            </div>

                        </div>
                    @endforeach

                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $categories->links() }}
                </div>

            </div>
        </div>

    @endif

</x-app-layout>