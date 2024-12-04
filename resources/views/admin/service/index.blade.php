@extends('admin.layouts.service')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Услуги</h1>

    @if ($services->isEmpty())
        <h2 class="text-xl">Услуг не найдено</h2>
    @else
        <table class="border border-gray-300 border-collapse w-full">
            <thead>
                <tr>
                    <td class="text-xl font-bold p-3 border border-gray-300">
                        Услуга
                    </td>
                    <td class="text-xl font-bold p-3 border border-gray-300">
                        Категория
                    </td>
                </tr>
            </thead>
            <tbody>
                @foreach ($services as $service)
                    <tr class="">
                        <td class="p-3 border border-gray-300">
                            <a class="text-xl text-blue-700" href="{{ route('admin.service.edit', $service->id) }}">
                                {{ $service->name }}
                            </a>
                        </td>
                        <td class="p-3 border border-gray-300">
                            <a class="text-blue-700" href="{{ route('admin.service.index', $service->category->id) }}">
                                {{ $service->category->name }}
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
