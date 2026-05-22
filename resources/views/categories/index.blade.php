<x-app-layout>

    <x-slot name="header">
        <div class="flex justify-between items-end border-b-2 border-sienna pb-4">
            <div>
                <h2 class="font-formal text-4xl text-sienna">{{ __('Product Categories') }}</h2>
                <p class="font-inter text-sage mt-1 font-medium uppercase tracking-wider text-xs">Organize your bakeshop catalog</p>
            </div>

            <a href="{{ route('categories.create') }}" class="bg-terracotta hover:bg-terracotta-dark text-cream font-bold px-6 py-2 rounded-xl shadow-rustic transition-all">
                Add Category
            </a>
        </div>
    </x-slot>

    <div class="space-y-8">
        <div class="card-rustic border-sienna">
            <div class="overflow-hidden rounded-xl border border-sienna border-opacity-10 shadow-sm">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="bg-sienna text-cream uppercase text-xs tracking-widest font-bold">
                            <th class="px-6 py-4 text-left">ID</th>
                            <th class="px-6 py-4 text-left">Category Name</th>
                            <th class="px-6 py-4 text-left">Description</th>
                            <th class="px-6 py-4 text-center">Products</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sienna divide-opacity-10 bg-white">
                        @forelse ($categories as $category)
                            <tr class="hover:bg-cream hover:bg-opacity-20 transition-colors">
                                <td class="px-6 py-5 font-bold text-sienna opacity-60">#{{ $category->id }}</td>
                                <td class="px-6 py-5 text-sienna font-bold">{{ $category->category_name }}</td>
                                <td class="px-6 py-5 text-sage text-sm">{{ Str::limit($category->description ?: '—', 80) }}</td>
                                <td class="px-6 py-5 text-center font-black text-sienna">{{ $category->products_count }}</td>
                                <td class="px-6 py-5 text-center font-semibold {{ $category->status === 'Inactive' ? 'text-red-600' : 'text-sage-dark' }}">{{ $category->status }}</td>
                                <td class="px-6 py-5 text-center">
                                    <div class="flex items-center justify-center gap-3">
                                        <a href="{{ route('categories.show', $category->id) }}" class="px-3 py-2 rounded-xl bg-sienna bg-opacity-10 text-sienna text-xs uppercase tracking-wider font-semibold">View</a>
                                        <a href="{{ route('categories.edit', $category->id) }}" class="px-3 py-2 rounded-xl bg-sage bg-opacity-10 text-sage text-xs uppercase tracking-wider font-semibold">Edit</a>
                                        <x-alert-delete route="{{ route('categories.destroy', $category->id) }}" message="Are you sure you want to delete this category?" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sage italic font-medium">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6 px-4">
                {{ $categories->links() }}
            </div>
        </div>
    </div>

</x-app-layout>