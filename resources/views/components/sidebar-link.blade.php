@props(['active' => false])

@php
    $classes = $active
        ? 'flex items-center px-4 py-2 bg-gray-700 text-white rounded-md'
        : 'flex items-center px-4 py-2 hover:bg-gray-700 text-gray-300 rounded-md';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
