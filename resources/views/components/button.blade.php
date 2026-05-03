@props(['type' => 'button', 'color' => 'terracotta'])

@php
    $colors = [
        'terracotta' => 'bg-terracotta hover:bg-terracotta-dark text-cream',
        'sage' => 'bg-sage hover:bg-sage-dark text-cream',
        'sienna' => 'bg-sienna hover:bg-sienna-dark text-cream',
        'cream' => 'bg-cream hover:bg-cream-dark text-sienna border border-sienna',
    ];
    $colorClass = $colors[$color] ?? $colors['terracotta'];
@endphp

<button type="{{ $type }}"
    {{ $attributes->merge(['class' => "inline-flex items-center px-6 py-2.5 rounded-lg font-inter font-medium shadow-md transition-all duration-200 active:scale-95 $colorClass"]) }}>
    {{ $slot }}
</button>