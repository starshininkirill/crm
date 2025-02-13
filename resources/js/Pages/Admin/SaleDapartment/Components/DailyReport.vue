<template>
    <thead>
        <tr class="bg-gray-800">
            <th style="width: calc(100% / 8)"
                class="border text-xs border-gray-300 text-md px-2 py-1 text-left text-white">
                Дата</th>
            <th style="width: calc(100% / 8)"
                class="border text-xs border-gray-300 text-md px-2 py-1 text-left text-white">
                NEW $</th>
            <th style="width: calc(100% / 8)"
                class="border text-xs border-gray-300 text-md px-2 py-1 text-left text-white">
                OLD $</th>
            <th style="width: calc(100% / 8)"
                class="border text-xs border-gray-300 text-md px-2 py-1 text-left text-white">
                Инд</th>
            <th style="width: calc(100% / 8)"
                class="border text-xs border-gray-300 text-md px-2 py-1 text-left text-white">
                Гот</th>
            <th style="width: calc(100% / 8)"
                class="border text-xs border-gray-300 text-md px-2 py-1 text-left text-white">
                РК</th>
            <th style="width: calc(100% / 8)"
                class="border text-xs border-gray-300 text-md px-2 py-1 text-left text-white">
                SEO</th>
            <th style="width: calc(100% / 8)"
                class="border text-xs border-gray-300 text-md px-2 py-1 text-left text-white">
                Допы</th>
        </tr>
    </thead>
    <tbody>
        <tr v-for="row in report">
            <td class="border text-xs border-gray-300 text-md px-2 py-1">
                {{ row.date }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-2 py-1">
                {{ formatPrice(row.newMoney) }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-2 py-1">
                {{ formatPrice(row.oldMoney) }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-2 py-1">
                {{ row.individualSites }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-2 py-1">
                {{ row.readiesSites }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-2 py-1">
                {{ row.rk }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-2 py-1">
                {{ row.seo }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-2 py-1">
                {{ row.other }}
            </td>
        </tr>
    </tbody>
</template>
<script>
export default {
    props: {
        report: {
            type: Array,
        },
    },
}


</script>