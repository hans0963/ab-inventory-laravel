@props(['title', 'value', 'icon', 'border'])

<div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-{{ $border }}">
    <div class="flex items-center justify-between">
        <div>
            <p class="font-inter text-gray-600 text-sm uppercase tracking-wide">{{ $title }}</p>
            <p class="font-lora font-semibold text-2xl text-sienna mt-1">{{ $value }}</p>
        </div>
        <div class="text-4xl opacity-20">{{ $icon }}</div>
    </div>
</div>
