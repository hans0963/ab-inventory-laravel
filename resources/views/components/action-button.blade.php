@props(['route', 'label', 'color'])

<a href="{{ route($route) }}" 
   class="bg-{{ $color }} hover:opacity-90 text-cream font-inter px-6 py-3 rounded-lg shadow-md transition text-center">
    {{ $label }}
</a>
