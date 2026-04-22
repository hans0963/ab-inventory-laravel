<div x-data="{ open: false }">
    <!-- Delete Button -->
    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white py-1 px-3 rounded text-sm" @click="open = true">
        {{ __('Delete') }}
    </button>

    <!-- Modal Overlay -->
    <div x-show="open" x-cloak class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg w-96">
            <!-- Modal Title -->
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-200">
                {{ __('Warning') }}
            </h2>
            <!-- Modal Message -->
            <p class="mt-2 text-gray-700 dark:text-gray-300">
                {{ $message }}
            </p>

            <!-- Modal Buttons -->
            <div class="px-4 py-3 text-end col-auto">
                <x-secondary-button @click="open = false">
                    {{ __('Cancel') }}
                </x-secondary-button>

                <form method="POST" action="{{ $route }}" class="inline">
                    @csrf
                    @method('DELETE')
                    <x-primary-button  type="submit" class="bg-red-600 hover:bg-red-700 text-white py-1 px-3 rounded text-sm">
                        {{ __('Delete') }}
                    </x-primary-button >
                </form>
            </div>
        </div>
    </div>
</div>
