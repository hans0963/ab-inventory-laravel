<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-md shadow-sm border-b border-sienna">
            <h2 class="font-pacifico text-3xl text-sienna leading-tight">
                {{ __('Raw Materials') }}
            </h2>
            <p class="font-inter text-sage">Manage ingredients and supplies</p>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md p-12 border-l-4 border-sienna text-center">
            <div class="text-6xl mb-4">🌾</div>
            <h3 class="text-xl font-lora font-semibold text-sienna mb-2">Raw Materials Module</h3>
            <p class="text-gray-600">This module is currently under development. Here you will be able to track flour, sugar, and other essential baking supplies.</p>
        </div>
    </div>
</x-app-layout>
