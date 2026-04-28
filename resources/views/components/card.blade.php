@props([
    'title' => 'Default Title', 
    'description' => 'Default Description', 
    'link' => '#', 
    'color' => 'terracotta' // Default artisan color
])

@php
    // Map artisan palette to Tailwind classes
    $palette = [
        'terracotta' => 'bg-terracotta text-cream',
        'sage' => 'bg-sage text-cream',
        'sienna' => 'bg-sienna text-cream',
        'cream' => 'bg-cream text-sienna',
    ];

    $classes = $palette[$color] ?? $palette['terracotta'];
@endphp

<a href="{{ $link }}" 
   {{ $attributes->merge(['class' => "block max-w-sm p-6 border border-sienna rounded-lg shadow-md $classes hover:opacity-90 transition"]) }}>
    
    <h5 class="mb-2 text-2xl font-pacifico tracking-tight">
        {{ $title }}
    </h5>
    
    <p class="font-poppins opacity-90">
        {{ $description }}
    </p>
</a>
