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
    <div class="reports w-1/2">
        @if ($daylyReport->isEmpty() && $motivationReport->isEmpty())
            <h2>Данные для отчёта не найдены</h2>
        @endif
        @if (!$daylyReport->isEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300 mb-10">
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
                        @foreach ($daylyReport as $data)
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
        @if (!$motivationReport->isEmpty())
            <table class="min-w-full border border-gray-300">
                <thead>
                    <tr class="bg-gray-800">
                        <th class="border border-gray-300 text-md px-4 py-2 text-left text-white">Неделя</th>
                        <th class="border border-gray-300 text-md px-4 py-2 text-left text-white">Новые</th>
                        <th class="border border-gray-300 text-md px-4 py-2 text-left text-white">Старые</th>
                        <th class="border border-gray-300 text-md px-4 py-2 text-left text-white">Инд. сайт</th>
                        <th class="border border-gray-300 text-md px-4 py-2 text-left text-white">Гот. сайт</th>
                        <th class="border border-gray-300 text-md px-4 py-2 text-left text-white">РК</th>
                        <th class="border border-gray-300 text-md px-4 py-2 text-left text-white">SEO</th>
                        <th class="border border-gray-300 text-md px-4 py-2 text-left text-white">Иное</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($motivationReport['weeksPlan'] as $week)
                        <tr>
                            <td class="border border-gray-300 text-md px-4 py-2">{{ $week['start'] }}- {{ $week['end'] }}
                            </td>
                            <td class="border border-gray-300 text-md px-4 py-2">
                                {{ number_format($week['newMoney'], 0, ' ', ' ') }} ₽</td>
                            <td class="border border-gray-300 text-md px-4 py-2">
                                {{ number_format($week['oldMoney'], 0, ' ', ' ') }} ₽</td>
                            <td class="border border-gray-300 text-md px-4 py-2">
                                {{ $week[$serviceCategoryModel::INDIVIDUAL_SITE] }}
                            </td>
                            <td class="border border-gray-300 text-md px-4 py-2">
                                {{ $week[$serviceCategoryModel::READY_SITE] }}
                            </td>
                            <td class="border border-gray-300 text-md px-4 py-2">
                                {{ $week[$serviceCategoryModel::RK] }}
                            </td>
                            <td class="border border-gray-300 text-md px-4 py-2">
                                {{ $week[$serviceCategoryModel::SEO] }}
                            </td>
                            <td class="border border-gray-300 text-md px-4 py-2">
                                {{ $week[$serviceCategoryModel::OTHER] }}
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            Итого
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ number_format($motivationReport['totalValues']['newMoney'], 0, ' ', ' ') }} ₽</td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ number_format($motivationReport['totalValues']['oldMoney'], 0, ' ', ' ') }} ₽</td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ $motivationReport['totalValues'][$serviceCategoryModel::INDIVIDUAL_SITE] }}
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ $motivationReport['totalValues'][$serviceCategoryModel::READY_SITE] }}
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ $motivationReport['totalValues'][$serviceCategoryModel::RK] }}
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ $motivationReport['totalValues'][$serviceCategoryModel::SEO] }}
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ $motivationReport['totalValues'][$serviceCategoryModel::OTHER] }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="min-w-full border border-gray-300">
                <thead>
                    <tr class="bg-gray-800">
                        <th class="border border-gray-300 text-md px-4 py-2 text-left text-white">План недели</th>
                        @foreach ($motivationReport['weeksPlan'] as $key => $week)
                            <th class="border border-gray-300 text-md px-4 py-2 text-left text-white">
                                неделя {{ $key + 1 }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ number_format( $week['goal'], 0, ' ', ' ') }} ₽
                        </td>
                        @foreach ($motivationReport['weeksPlan'] as $week)
                            <td class="border border-gray-300 text-md px-4 py-2">
                                @if ($week['completed'])
                                    Да
                                @else
                                    Нет
                                @endif
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
            <table class="min-w-full border border-gray-300">
                <thead>
                    <tr class="bg-gray-800">
                        <th class="border border-gray-300 text-md px-4 py-2 text-left text-white">План</th>
                        <th class="border border-gray-300 text-md px-4 py-2 text-left text-white">Цель</th>
                        <th class="border border-gray-300 text-md px-4 py-2 text-left text-white">Выполнен</th>
                        <th class="border border-gray-300 text-md px-4 py-2 text-left text-white">Да/Нет</th>
                        <th class="border border-gray-300 text-md px-4 py-2 text-left text-white">Бонус</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            План месяца
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ number_format($motivationReport['mounthPlan']['goal'], 0, ' ', ' ') }} ₽
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ number_format($motivationReport['mounthPlan']['value'], 0, ' ', ' ') }} ₽
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ $motivationReport['mounthPlan']['completed'] ? 'Да' : 'Нет' }}
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            -
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            Двойной план
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ number_format($motivationReport['doublePlan']['goal'], 0, ' ', ' ') }} ₽
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ number_format($motivationReport['doublePlan']['value'], 0, ' ', ' ') }} ₽
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ $motivationReport['doublePlan']['completed'] ? 'Да' : 'Нет' }}
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{  number_format($motivationReport['doublePlan']['bonus'], 0, ' ', ' ') }} ₽
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            Бонус за 150к
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ isset($motivationReport['bonusPlan']['goal']) ? number_format($motivationReport['bonusPlan']['goal'], 0, ' ', ' ') . ' ₽' : 'План не определён' }}
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ number_format($motivationReport['bonusPlan']['value'], 0, ' ', ' ') }} ₽
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ $motivationReport['bonusPlan']['completed'] ? 'Да' : 'Нет' }}
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{  number_format($motivationReport['bonusPlan']['bonus'], 0, ' ', ' ') }} ₽
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            Супер план
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ isset($motivationReport['superPlan']['goal']) ? number_format($motivationReport['superPlan']['goal'], 0, ' ', ' ') . ' ₽' : 'План не определён' }}
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ isset($motivationReport['superPlan']['value']) ? number_format($motivationReport['superPlan']['value'], 0, ' ', ' ') . ' ₽' : 'Не удалось расчитать' }}
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ $motivationReport['superPlan']['completed'] ? 'Да' : 'Нет' }}
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{  number_format($motivationReport['superPlan']['bonus'], 0, ' ', ' ') }} ₽
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            Б1
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            -
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            -
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ $motivationReport['b1']['completed'] ? 'Да' : 'Нет' }}
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ $motivationReport['b1']['bonus'] }} %
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            Б2
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            -
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            -
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ $motivationReport['b2']['completed'] ? 'Да' : 'Нет' }}
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ $motivationReport['b2']['bonus'] }} %
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            Б3
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ isset($motivationReport['b3']['goal']) ? number_format($motivationReport['b3']['goal'], 0, ' ', ' ') . ' %' : 'План не определён' }}
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ isset($motivationReport['b3']['value']) ? number_format($motivationReport['b3']['value'], 0, ' ', ' ') . ' %' : 'Не удалось расчитать' }}
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ $motivationReport['b3']['completed'] ? 'Да' : 'Нет' }}
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{  number_format($motivationReport['b3']['bonus'], 0, ' ', ' ') }} ₽
                        </td>
                    </tr>
                    <tr>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            Б4
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ isset($motivationReport['b4']['goal']) ? number_format($motivationReport['b4']['goal'], 0, ' ', ' ') : 'План не определён' }}
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ isset($motivationReport['b4']['value']) ? number_format($motivationReport['b4']['value'], 0, ' ', ' ') : 'Не удалось расчитать' }}
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ $motivationReport['b4']['completed'] ? 'Да' : 'Нет' }}
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{  number_format($motivationReport['b4']['bonus'], 0, ' ', ' ') }} ₽
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="min-w-full border border-gray-300">
                <thead>
                    <tr class="bg-gray-800">
                        <th class="border border-gray-300 text-md px-4 py-2 text-left text-white">
                            %
                        </th>
                        <th class="border border-gray-300 text-md px-4 py-2 text-left text-white">
                            % От Новых
                        </th>
                        <th class="border border-gray-300 text-md px-4 py-2 text-left text-white">
                            % От Старых
                        </th>
                        <th class="border border-gray-300 text-md px-4 py-2 text-left text-white">
                            Бонусы
                        </th>
                        <th class="border border-gray-300 text-md px-4 py-2 text-left text-white">
                            Итого
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ $motivationReport['salary']['percentage'] }} %
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ number_format($motivationReport['salary']['newMoney'], 0, ' ', ' ') }} ₽
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ number_format($motivationReport['salary']['oldMoney'], 0, ' ', ' ') }} ₽
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ number_format($motivationReport['salary']['bonuses'], 0, ' ', ' ') }} ₽
                        </td>
                        <td class="border border-gray-300 text-md px-4 py-2">
                            {{ number_format($motivationReport['salary']['amount'], 0, ' ', ' ') }} ₽
                        </td>
                    </tr>
                </tbody>
            </table>
        @endif
    </div>
@endsection
