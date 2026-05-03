@props([
    'title' => 'Default Title', 
    'description' => 'Default Description', 
    'link' => '#', 
    'color' => 'terracotta'
])

@php
    $palette = [
        'terracotta' => 'bg-terracotta text-cream border-terracotta-dark',
        'sage' => 'bg-sage text-cream border-sage-dark',
        'sienna' => 'bg-sienna text-cream border-sienna-dark',
        'cream' => 'bg-cream text-sienna border-sienna border-opacity-30',
    ];

    $classes = $palette[$color] ?? $palette['terracotta'];
@endphp

<a href="{{ $link }}" 
   {{ $attributes->merge(['class' => "block max-w-sm p-8 border rounded-2xl shadow-rustic $classes hover:shadow-rustic-lg hover:-translate-y-1 transition-all duration-300"]) }}>
    
    <h5 class="mb-3 text-2xl font-lora font-bold tracking-tight">
        {{ $title }}
    </h5>
    
    <p class="font-inter opacity-90 leading-relaxed">
        {{ $description }}
    </p>
</a>
