@extends('admin.layouts.organization')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Организации</h1>

    @if ($organizations->isEmpty())
        <h2 class="text-xl">Организаций не найдено</h2>
    @else
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Название
                        </th>
                        <th scope="col" class="px-6 py-3">
                            ИНН
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Статус
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
                    @foreach ($organizations as $organization)
                        <tr
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <th scope="row"
                                class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $organization->short_name }}
                            </th>
                            <td class="px-6 py-4">
                                {{ $organization->inn }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($organization->active)
                                    <span class=" text-green-500">
                                        Активен
                                    </span>
                                @else
                                    <span class=" text-red-500">
                                        Не активен
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.organization.edit', $organization->id) }}"
                                    class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                    Редактировать
                                </a>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('organization.destroy', $organization->id) }}"
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
