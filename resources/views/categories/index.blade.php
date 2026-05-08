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
            <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
                <div>
                    <h2 class="font-formal text-4xl text-sienna">
                        {{ __('Product Categories') }}
                    </h2>
                    <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Organize your bakeshop catalog</p>
                </div>

                <a href="{{ route('categories.create') }}" 
                   class="bg-terracotta hover:bg-terracotta-dark text-cream font-bold px-8 py-3 rounded-xl shadow-rustic transition-all hover:-translate-y-0.5 active:translate-y-0 flex items-center">
                    <span class="mr-2 text-xl">+</span> Add Category
                </a>
            </div>
        </x-slot>

        <!-- Content -->
        <div class="space-y-8">
            <!-- Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($categories as $category)
                    <div class="card-rustic border-sienna hover:-translate-y-1 transition-all duration-300">
                        <div class="flex justify-between items-start mb-4">
                            <div class="bg-terracotta bg-opacity-10 p-3 rounded-xl">
                                <span class="text-2xl">📂</span>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ route('categories.edit', $category->id) }}" class="p-2 text-sage hover:text-sienna hover:bg-sage hover:bg-opacity-10 rounded-lg transition-all">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <h3 class="text-xl font-lora font-bold text-sienna mb-2">{{ $category->category_name }}</h3>
                        <p class="text-sm text-sage font-medium line-clamp-2 mb-4">{{ $category->description ?? 'No description available for this artisan group.' }}</p>
                        
                        <div class="pt-4 border-t border-sienna border-opacity-10 flex justify-between items-center">
                            <span class="text-xs font-bold text-sienna opacity-60 uppercase tracking-widest">
                                {{ $category->products_count ?? $category->products->count() }} Products
                            </span>
                            <a href="{{ route('products.index', ['category' => $category->id]) }}" class="text-xs font-black text-terracotta hover:underline">View Catalog →</a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $categories->links() }}
            </div>
        </div>

    @endif

</x-app-layout>