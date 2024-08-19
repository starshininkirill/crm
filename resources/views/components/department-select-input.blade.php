<div>
    <label for="{{ $id }}" class="mb-1 block text-sm font-medium leading-6 text-gray-900">{{ $label }}</label>
    <select id="{{ $id }}" name="{{ $name }}"
        class="block w-full rounded-md border-0 py-2 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
        @foreach ($options as $option)
            <option value="{{ $option->id }}">
                {{ $option->departmentable->name }}
            </option>
        @endforeach
    </select>
</div>