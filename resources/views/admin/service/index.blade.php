@extends('admin.layouts.service')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Услуги</h1>
    
    @if ($services->isEmpty())
        <h2 class="text-xl">Услуг не найдено</h2>
    @else
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Услуга
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Категория
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
                    @foreach ($services as $service)
                        <tr
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $service->name }}
                            </th>
                            <td class="px-6 py-4">
                                @if ($service->category)
                                    <a href="{{ route('admin.service.index', $service->category->id) }}">
                                        {{ $service->category->name }}
                                    </a>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.service.edit', $service->id) }}"
                                    class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                    Редактировать
                                </a>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('service.destroy', $service->id) }}"
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
        </div>
    @endif
@endsection
 