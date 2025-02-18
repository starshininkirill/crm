<template>
    <thead>
        <tr class="bg-gray-800">
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">Неделя</th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">NEW $</th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">OLD $</th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">Инд</th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">Гот</th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">РК</th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">SEO</th>
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">Иное</th>
        </tr>
    </thead>
    <tbody>
        <tr v-for="week in weeks">
            <td class="border text-xs border-gray-300 text-md px-1 py-1">
                {{ week.start }}-
                {{ week.end }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1">
                {{ formatPrice(week.newMoney) }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1">
                {{ formatPrice(week.oldMoney) }}
            </td>
            <td v-for="service in week.servicesByCatsCount" class="border text-xs border-gray-300 text-md px-1 py-1">
                {{ service }}
            </td>
        </tr>
        <tr v-if="totalValues">
            <td class="border text-xs border-gray-300 text-md px-1 py-1">
                Итого
            </td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1">
                  {{ formatPrice(totalValues.newMoney) }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1">
                  {{ formatPrice(totalValues.oldMoney) }}
            </td>
            <td v-for="service in totalValues.servicesByCatsCount" class="border text-xs border-gray-300 text-md px-1 py-1">
                {{ service }}
            </td>
        </tr>
    </tbody>
</template>
<script>
export default {
    props: {
        weeks: {
            type: Object,
            required: true,
        },
        totalValues: {
            type: Object,
        }
    },
}


</script>