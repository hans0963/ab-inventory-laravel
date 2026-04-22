<x-app-layout>
    @if ($categories->isEmpty())
        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="text-center p-6 bg-white dark:bg-gray-800 overflow-hidden sm:rounded-lg text-gray-800 dark:text-gray-200 rounded-lg shadow">
                    <h3 class="text-lg font-semibold">{{ __('No categories found') }}</h3>
                    <p class="text-sm mt-2">{{ __('Don\'t have one? Create one!') }}</p>
                    <a href="{{ route('categories.create') }}" class="mt-4 inline-block bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded">
                        {{ __('Add your first category') }}
                    </a>
                </div>
            </div>
        </div>~
    @else
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Categories') }}
            </h2>
        </x-slot>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="mb-4">
                        <a href="{{ route('categories.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Add Category') }}
                        </a>
                    </div>
                    
                    <div class="overflow-hidden rounded-lg">
                        <table class="min-w-full border border-gray-200 dark:border-gray-700">
                            <thead class="bg-gray-200 text-black dark:bg-gray-700 dark:text-white">
                                <tr>
                                    <th class="px-4 py-3 border-b text-left">ID</th>
                                    <th class="px-4 py-3 border-b text-left">Name</th>
                                    <th class="px-4 py-3 border-b text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-white">
                                @foreach ($categories as $category)
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <td class="px-4 py-3">{{ $category->id }}</td>
                                    <td class="px-4 py-3">{{ $category->category_name }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex gap-2 justify-center"> 
                                            <a href="{{ route('categories.edit', $category->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white py-1 px-3 rounded text-sm">
                                                {{ __('Edit') }}
                                            </a>                                   
                                            <x-alert-delete route="{{ route('categories.destroy', $category->id) }}" message="Are you sure you want to delete this category?" />
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $categories->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>
