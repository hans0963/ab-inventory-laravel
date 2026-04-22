@props(['label', 'name', 'options' => [], 'required' => false])

<div class="mb-4">
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 dark:text-gray-200">
        {{ $label }} @if($required) <span class="text-red-500">*</span> @endif
    </label>
    <select id="{{ $name }}" name="{{ $name }}"
        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        <option value="">Select an option</option>
        @foreach ($options as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>
</div>
