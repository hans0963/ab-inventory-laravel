@props([
    'title' => 'Default Title', 
    'description' => 'Default Description', 
    'link' => '#', 
    'color' => 'blue' // Default to blue if no color is provided
])

@php
    $bgColor = "bg-{$color}-500";
    $hoverColor = "hover:bg-{$color}-600";
    $textColor = "text-white";
@endphp

<a href="{{ $link }}" {{ $attributes->merge(['class' => "block max-w-sm p-6 border border-gray-200 rounded-lg shadow-sm $bgColor $hoverColor dark:border-gray-700"]) }}>
    
    <h5 class="mb-2 text-2xl font-bold tracking-tight {{ $textColor }}">
        {{ $title }}
    </h5>
    
    <p class="font-normal text-white opacity-90">
        {{ $description }}
    </p>

</a>
