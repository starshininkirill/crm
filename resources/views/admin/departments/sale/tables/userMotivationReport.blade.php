<div class="flex flex-col">
    <table class="min-w-full border border-gray-300">
        <thead>
            <tr class="bg-gray-800">
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">Неделя</th>
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">NEW $</th>
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">OLD $</th>
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">Инд. сайт</th>
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">Гот. сайт</th>
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">РК</th>
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">SEO</th>
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">Иное</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($motivationReport['weeksPlan'] as $week)
                <tr>
                    <td class="border text-xs border-gray-300 text-md px-1 py-1">{{ $week['start'] }}-
                        {{ $week['end'] }}
                    </td>
                    <td class="border text-xs border-gray-300 text-md px-1 py-1">
                        {{ number_format($week['newMoney'], 0, ' ', ' ') }} ₽</td>
                    <td class="border text-xs border-gray-300 text-md px-1 py-1">
                        {{ number_format($week['oldMoney'], 0, ' ', ' ') }} ₽</td>
                    <td class="border text-xs border-gray-300 text-md px-1 py-1">
                        {{ $week[$serviceCategoryModel::INDIVIDUAL_SITE] }}
                    </td>
                    <td class="border text-xs border-gray-300 text-md px-1 py-1">
                        {{ $week[$serviceCategoryModel::READY_SITE] }}
                    </td>
                    <td class="border text-xs border-gray-300 text-md px-1 py-1">
                        {{ $week[$serviceCategoryModel::RK] }}
                    </td>
                    <td class="border text-xs border-gray-300 text-md px-1 py-1">
                        {{ $week[$serviceCategoryModel::SEO] }}
                    </td>
                    <td class="border text-xs border-gray-300 text-md px-1 py-1">
                        {{ $week[$serviceCategoryModel::OTHER] }}
                    </td>
                </tr>
            @endforeach
            <tr>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    Итого
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ number_format($motivationReport['totalValues']['newMoney'], 0, ' ', ' ') }} ₽</td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ number_format($motivationReport['totalValues']['oldMoney'], 0, ' ', ' ') }} ₽</td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $motivationReport['totalValues'][$serviceCategoryModel::INDIVIDUAL_SITE] }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $motivationReport['totalValues'][$serviceCategoryModel::READY_SITE] }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $motivationReport['totalValues'][$serviceCategoryModel::RK] }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $motivationReport['totalValues'][$serviceCategoryModel::SEO] }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $motivationReport['totalValues'][$serviceCategoryModel::OTHER] }}
                </td>
            </tr>
        </tbody>
    </table>
    <table class="min-w-full border border-gray-300">
        <thead>
            <tr class="bg-gray-800">
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">План недели
                </th>
                @foreach ($motivationReport['weeksPlan'] as $key => $week)
                    <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                        неделя {{ $key + 1 }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ number_format($week['goal'], 0, ' ', ' ') }} ₽
                </td>
                @foreach ($motivationReport['weeksPlan'] as $week)
                    <td class="border text-xs border-gray-300 text-md px-1 py-1">
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
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">План месяца
                </th>
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">Двойной план
                </th>
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">Бонус план
                </th>
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">Супер план
                </th>
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">Б1</th>
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">Б2</th>
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">Б3</th>
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">Б4</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $motivationReport['mounthPlan']['completed'] ? 'Да' : 'Нет' }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $motivationReport['doublePlan']['completed'] ? 'Да' : 'Нет' }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $motivationReport['bonusPlan']['completed'] ? 'Да' : 'Нет' }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $motivationReport['superPlan']['completed'] ? 'Да' : 'Нет' }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $motivationReport['b1']['completed'] ? 'Да' : 'Нет' }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $motivationReport['b2']['completed'] ? 'Да' : 'Нет' }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $motivationReport['b3']['completed'] ? 'Да' : 'Нет' }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $motivationReport['b4']['completed'] ? 'Да' : 'Нет' }}
                </td>
            </tr>
        </tbody>
    </table>
    <table class="min-w-full border border-gray-300">
        <thead>
            <tr class="bg-gray-800">
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                    %
                </th>
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                    % От Новых
                </th>
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                    % От Старых
                </th>
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                    Бонусы
                </th>
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                    Итого
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $motivationReport['salary']['percentage'] }} %
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ number_format($motivationReport['salary']['newMoney'], 0, ' ', ' ') }} ₽
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ number_format($motivationReport['salary']['oldMoney'], 0, ' ', ' ') }} ₽
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ number_format($motivationReport['salary']['bonuses'], 0, ' ', ' ') }} ₽
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ number_format($motivationReport['salary']['amount'], 0, ' ', ' ') }} ₽
                </td>
            </tr>
        </tbody>
    </table>
</div>