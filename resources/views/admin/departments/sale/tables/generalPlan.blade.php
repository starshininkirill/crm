<thead>
    <tr class="bg-gray-800">
        <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
            План месяца
        </th>
        <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
            Нужно в день
        </th>
        <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
            Факт в день
        </th>
        <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
            Отставание
        </th>
        <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
            Прогноз New
        </th>
    </tr>
</thead>
<tbody>
    <tr>
        <td class="border text-xs border-gray-300 text-md px-1 py-1">
            {{ number_format($generalPlan['mounthPlan'], 0, ' ', ' ') }} ₽
        </td>
        <td class="border text-xs border-gray-300 text-md px-1 py-1">
            {{ number_format($generalPlan['needOnDay'], 0, ' ', ' ') }} ₽
        </td>
        <td class="border text-xs border-gray-300 text-md px-1 py-1">
            {{ number_format($generalPlan['faktOnDay'], 0, ' ', ' ') }} ₽
        </td>
        <td class="border text-xs border-gray-300 text-md px-1 py-1">
            {{ number_format($generalPlan['difference'], 0, ' ', ' ') }} ₽
        </td>
        <td class="border text-xs border-gray-300 text-md px-1 py-1">
            {{ number_format($generalPlan['prognosis'], 0, ' ', ' ') }} ₽
        </td>
    </tr>
</tbody>
