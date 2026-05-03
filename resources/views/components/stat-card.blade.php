@props(['title', 'value', 'icon', 'border'])

<div class="card-rustic border-{{ $border }} hover:-translate-y-1 transition-transform duration-300">
    <div class="flex items-center justify-between">
        <div>
            <p class="font-inter text-sage font-medium text-xs uppercase tracking-widest mb-1">{{ $title }}</p>
            <p class="font-lora font-bold text-3xl text-sienna leading-none">{{ $value }}</p>
        </div>
        <div class="text-4xl opacity-30 transform -rotate-12">{{ $icon }}</div>
    </div>
</div>
