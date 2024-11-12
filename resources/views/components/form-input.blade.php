<div>
    <label class="block text-sm font-medium leading-6 text-gray-900">{{ $label }}</label>
    <div class="mt-1">
        <input type="{{ $type }}" name="{{ $name }}" placeholder="{{ $placeholder }}"
            value="{{ old($name, $value) }}" @if ($required) required @endif
            class="block w-full rounded-md border-0 py-1.5 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
    </div>
</div>
