@props(['active' => false])

@php
    $classes = $active
        ? 'flex items-center px-4 py-2 bg-sienna text-cream rounded-md transition'
        : 'flex items-center px-4 py-2 text-sage hover:bg-sienna hover:text-cream rounded-md transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} @click="openSidebar = false">
    {{ $slot }}
</a>
