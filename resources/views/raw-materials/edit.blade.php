<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-md shadow-sm border-b border-sienna">
            <h2 class="font-pacifico text-3xl text-sienna leading-tight">
                {{ __('Edit Raw Material') }}
            </h2>
            <p class="font-inter text-sage">Update ingredient details</p>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-sienna">
            <h3 class="text-xl font-lora font-semibold text-sienna mb-4">Raw Material Edit Form</h3>
            <p class="text-gray-600 italic">This is a placeholder for the Raw Materials Edit view.</p>
        </div>
    </div>
</x-app-layout>
