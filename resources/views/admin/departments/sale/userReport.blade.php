@extends('admin.layouts.departments.sale')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">
        Отчёт по Сотрудникам
    </h1>
    <form action="{{ route('admin.department.sale.user-report') }}" method="GET" class="flex w-6/12 gap-3 mb-6">
        <input type="date" name="date" class="border px-3 py-2"
            value="{{ $date != null ? $date->format('Y-m-d') : now()->format('Y-m-d') }}">
        <select class="select max-w-md" name="user" id="">
            <option disabled {{ $user != null ? '' : 'selected' }} value="">
                Выберите сотрудника
            </option>
            @foreach ($users as $optionUser)
                <option {{ optional($user)->id == $optionUser->id ? 'selected' : '' }} value="{{ $optionUser->id }}">
                    {{ $optionUser->first_name }} {{ $optionUser->last_name }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn">
            Выбрать
        </button>

    </form>
    <div class="reports">
        @if (empty($report))
            <h2>Данные для отчёта не найдены</h2>
        @else
            <div class="overflow-x-auto w-1/2">
                <table class="min-w-full border border-gray-300">
                    <thead>
                        <tr class="bg-gray-800">
                            <th class="border border-gray-300 text-md px-2 py-1 text-left text-white">Дата</th>
                            <th class="border border-gray-300 text-md px-2 py-1 text-left text-white">NEW $</th>
                            <th class="border border-gray-300 text-md px-2 py-1 text-left text-white">OLD $</th>
                            <th class="border border-gray-300 text-md px-2 py-1 text-left text-white">Инд</th>
                            <th class="border border-gray-300 text-md px-2 py-1 text-left text-white">Гот</th>
                            <th class="border border-gray-300 text-md px-2 py-1 text-left text-white">РК</th>
                            <th class="border border-gray-300 text-md px-2 py-1 text-left text-white">SEO</th>
                            <th class="border border-gray-300 text-md px-2 py-1 text-left text-white">Допы</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($report as $data)
                            <tr>
                                <td class="border border-gray-300 text-md px-2 py-1">
                                    {{ \Carbon\Carbon::parse($data['date'])->format('d.m.y') }}</td>
                                <td class="border border-gray-300 text-md px-2 py-1">
                                    {{ number_format($data['newMoney'], 0, ' ', ' ') }} ₽</td>
                                <td class="border border-gray-300 text-md px-2 py-1">
                                    {{ number_format($data['oldMoney'], 0, ' ', ' ') }} ₽</td>
                                <td class="border border-gray-300 text-md px-2 py-1">{{ $data['individualSites'] }}</td>
                                <td class="border border-gray-300 text-md px-2 py-1">{{ $data['readiesSites'] }}</td>
                                <td class="border border-gray-300 text-md px-2 py-1">{{ $data['rk'] }}</td>
                                <td class="border border-gray-300 text-md px-2 py-1">{{ $data['seo'] }}</td>
                                <td class="border border-gray-300 text-md px-2 py-1">{{ $data['other'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
