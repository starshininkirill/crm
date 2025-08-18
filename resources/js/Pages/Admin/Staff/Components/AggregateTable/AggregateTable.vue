<template>
    <table class="table">
        <thead class="thead">
            <tr>
                <th class="px-2 py-2 text-center border-r">
                    Отдел
                </th>
                <th class="px-2 py-2 text-center border-r">
                    Работает
                </th>
                <th class="px-2 py-2 text-center border-r">
                    Перерыв
                </th>
                <th class="px-2 py-2 text-center">
                    Не работат
                </th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="department, key in report"
                :class="department.isMain ? 'text-center uppercase text-xs bg-gray-900 text-white' : 'text-xs text-gray-700 text-center uppercase bg-gray-200'">
                <th class="px-2 py-2 text-center border-r border-b border-white">
                    {{ key }}
                </th>
                <td class="px-2 py-2 text-center border-r border-b border-white">
                    {{ department.work }}
                </td>
                <td class="px-2 py-2 text-center border-r border-b border-white">
                    {{ department.onBreak }}
                </td>
                <td class="px-2 py-2 text-center border-b border-white">
                    {{ department.notWork }}
                </td>
            </tr>
        </tbody>
    </table>
</template>
<script>

export default {
    props: {
        report: {
            type: Object,
            required: true,
        },
    },
}

</script>