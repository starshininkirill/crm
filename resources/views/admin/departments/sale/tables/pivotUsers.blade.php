<table class="min-w-full border border-gray-300">
    <thead>
        <tr class="bg-gray-800">
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                Менеджер
            </th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                NEW $
            </th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                OLD $
            </th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                Инд
            </th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                Гот
            </th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                РК
            </th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                СЕО
            </th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                Иное
            </th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                План Месяц
            </th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                Двойной план
            </th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                Бонус план
            </th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                Супер план
            </th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                Б1
            </th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                Б2
            </th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                Б3
            </th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                Б4
            </th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                Итого
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($pivotUsers as $user)
            <tr>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $user['name'] }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ number_format($user['totalValues']['newMoney'], 0, ' ', ' ') }} ₽</td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ number_format($user['totalValues']['oldMoney'], 0, ' ', ' ') }} ₽</td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $user['totalValues'][$serviceCategoryModel::INDIVIDUAL_SITE] }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $user['totalValues'][$serviceCategoryModel::READY_SITE] }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $user['totalValues'][$serviceCategoryModel::RK] }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $user['totalValues'][$serviceCategoryModel::SEO] }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $user['totalValues'][$serviceCategoryModel::OTHER] }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $user['mounthPlan']['completed'] ? 'Да' : 'Нет' }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $user['doublePlan']['completed'] ? 'Да' : 'Нет' }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $user['bonusPlan']['completed'] ? 'Да' : 'Нет' }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $user['superPlan']['completed'] ? 'Да' : 'Нет' }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $user['b1']['completed'] ? 'Да' : 'Нет' }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $user['b2']['completed'] ? 'Да' : 'Нет' }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $user['b3']['completed'] ? 'Да' : 'Нет' }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $user['b4']['completed'] ? 'Да' : 'Нет' }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ number_format($user['salary']['amount'], 0, ' ', ' ') }} ₽
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
