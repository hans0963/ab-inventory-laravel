@props(['label', 'name', 'type' => 'text', 'required' => false, 'placeholder' => ''])

<div class="mb-5">
    <label for="{{ $name }}" class="block text-sm font-semibold text-sienna mb-1.5">
        {{ $label }} @if($required) <span class="text-terracotta">*</span> @endif
    </label>
    <input id="{{ $name }}" type="{{ $type }}" name="{{ $name }}"
        {{ $attributes->merge(['class' => "w-full rounded-lg border-sienna border-opacity-30 bg-white py-2.5 px-4 focus:ring-2 focus:ring-terracotta focus:ring-opacity-50 focus:border-terracotta transition-all duration-200 placeholder-sienna placeholder-opacity-40 shadow-sm"]) }}
        placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }}>
</div>
