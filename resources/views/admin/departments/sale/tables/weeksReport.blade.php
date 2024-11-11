<table class="min-w-full border border-gray-300">
    <thead>
        <tr class="bg-gray-800">
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white" style="width:12.5%">Неделя</th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white" style="width:12.5%">NEW $</th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white" style="width:12.5%">OLD $</th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white" style="width:12.5%">Инд. сайт</th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white" style="width:12.5%">Гот. сайт</th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white" style="width:12.5%">РК</th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white" style="width:12.5%">SEO</th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white" style="width:12.5%">Иное</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($weeks['weeksPlan'] as $week)
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
                {{ number_format($weeks['totalValues']['newMoney'], 0, ' ', ' ') }} ₽</td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1">
                {{ number_format($weeks['totalValues']['oldMoney'], 0, ' ', ' ') }} ₽</td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1">
                {{ $weeks['totalValues'][$serviceCategoryModel::INDIVIDUAL_SITE] }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1">
                {{ $weeks['totalValues'][$serviceCategoryModel::READY_SITE] }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1">
                {{ $weeks['totalValues'][$serviceCategoryModel::RK] }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1">
                {{ $weeks['totalValues'][$serviceCategoryModel::SEO] }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1">
                {{ $weeks['totalValues'][$serviceCategoryModel::OTHER] }}
            </td>
        </tr>
    </tbody>
</table>