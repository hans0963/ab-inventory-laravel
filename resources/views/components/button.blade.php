@props(['type' => 'button', 'color' => 'blue'])

<button type="{{ $type }}"
    class="px-4 py-2 text-white bg-{{ $color }}-500 hover:bg-{{ $color }}-600 rounded shadow">
    {{ $slot }}
</button>