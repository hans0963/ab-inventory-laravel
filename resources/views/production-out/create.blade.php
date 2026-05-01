<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full bg-cream p-4 rounded-md shadow-sm border-b border-sienna">
            <h2 class="font-pacifico text-3xl text-sienna leading-tight">
                {{ __('Create Production Out') }}
            </h2>
            <p class="font-inter text-sage">Record product pull-outs or expirations</p>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-terracotta">
            <h3 class="text-xl font-lora font-semibold text-sienna mb-4">Production Out Creation Form</h3>
            <p class="text-gray-600 italic">This is a placeholder for the Production Out Create view.</p>
        </div>
    </div>
</x-app-layout>
