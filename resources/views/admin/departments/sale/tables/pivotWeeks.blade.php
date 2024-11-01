<table class="min-w-full border border-gray-300">
    <thead>
        <tr class="bg-gray-800">
            <th style="width: " class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                Неделя
            </th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">NEW $
            </th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                OLD $
            </th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">Инд.
                сайт
            </th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">Гот.
                сайт
            </th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">РК
            </th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">SEO
            </th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">Иное
            </th>
        </tr>
    </thead>
    <tbody>
        @foreach ($pivotWeeks['weeks'] as $week)
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
                {{ number_format($pivotWeeks['totalValues']['newMoney'], 0, ' ', ' ') }} ₽</td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1">
                {{ number_format($pivotWeeks['totalValues']['oldMoney'], 0, ' ', ' ') }} ₽</td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1">
                {{ $pivotWeeks['totalValues'][$serviceCategoryModel::INDIVIDUAL_SITE] }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1">
                {{ $pivotWeeks['totalValues'][$serviceCategoryModel::READY_SITE] }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1">
                {{ $pivotWeeks['totalValues'][$serviceCategoryModel::RK] }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1">
                {{ $pivotWeeks['totalValues'][$serviceCategoryModel::SEO] }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1">
                {{ $pivotWeeks['totalValues'][$serviceCategoryModel::OTHER] }}
            </td>
        </tr>
    </tbody>
</table>
