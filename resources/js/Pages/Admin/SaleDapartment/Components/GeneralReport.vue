<template>
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
                {{ formatPrice(generalPlan.monthPlan) }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1">
                {{ formatPrice(generalPlan.needOnDay) }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1">
                {{ formatPrice(generalPlan.faktOnDay) }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1">
                {{ formatPrice(generalPlan.difference) }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1">
                {{ formatPrice(generalPlan.prognosis) }}
            </td>
        </tr>
    </tbody>

</template>
<script>
export default {
    props: {
        generalPlan: {
            type: Object,
            required: true,
        }
    },
}


</script>