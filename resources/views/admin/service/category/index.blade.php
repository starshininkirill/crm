@extends('admin.layouts.service')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Категории услуг</h1>
    <div class="grid grid-cols-3 gap-8">
        <form method="POST" class="" action="{{ route('serviceCategory.store') }}">
            <div class="text-3xl font-semibold mb-6">
                Создать категорию
            </div>
            @if (session('success'))
                <div class="mb-3 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @csrf
            @if ($errors->any())
                <ul class="flex flex-col gap-1 mb-4">
                    @foreach ($errors->all() as $error)
                        <li class="text-red-400">{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <div class="flex flex-col gap-4">
                <x-form-input type="text" name="name" placeholder="Название категории" label="Название категории" />
                @if (!$types->isEmpty())
                    <x-key-value-select-input :options="$types" label="Выберите тип категории услуг" name="type"
                        id="type" />
                @endif
                <button type="submit"
                    class="middle w-full none center mr-4 rounded-lg bg-blue-500 py-3 px-6 font-sans text-xs font-bold uppercase text-white shadow-md shadow-blue-500/20 transition-all hover:shadow-lg hover:shadow-blue-500/40 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                    data-ripple-light="true">
                    Создать
                </button>
            </div>
        </form>
        <div class="flex flex-col gap-3 col-span-2">
            @if ($categories->isEmpty())
                <h2 class="text-xl">Категорий услуг не найдено</h2>
            @else
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Категория
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Тип Категории
                            </th>
                            <th scope="col" class="px-6 py-3 text-right w-12">
                                Услуги
                            </th>
                            <th scope="col" class="px-6 py-3 text-right w-12">
                                Редактировать
                            </th>
                            <th scope="col" class="px-6 py-3 text-right w-12">
                                Удалить
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $category->name }}
                                </th>

                                <td class="px-6 py-4">
                                    {{ $category->readableType() }}
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.service.index', $category->id) }}"
                                        class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                        {{ $category->services_count }}
                                    </a>
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.service.category.edit', $category->id) }}"
                                        class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                        Редактировать
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form method="POST" action="{{ route('serviceCategory.destroy', $category->id) }}"
                                        class="font-medium text-red-600 dark:text-red-500 hover:underline">
                                        @method('DELETE')
                                        @csrf
                                        <button type="submit">
                                            Удалить
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection
