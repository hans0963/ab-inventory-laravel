@props(['label', 'name', 'type' => 'text', 'required' => false, 'placeholder' => ''])

<div class="mb-4">
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
        {{ $label }} @if($required) <span class="text-red-500">*</span> @endif
    </label>
    <input id="{{ $name }}" type="{{ $type }}" name="{{ $name }}"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
        placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }}>
</div>
