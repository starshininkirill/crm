<template>
    <table class="min-w-full border border-gray-300">
        <thead>
            <tr class="bg-gray-800">
                <th style="width: calc(100% / 17)"
                    class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                    Менеджер
                </th>
                <th style="width: calc(100% / 17)"
                    class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                    NEW $
                </th>
                <th style="width: calc(100% / 17)"
                    class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                    OLD $
                </th>
                <th style="width: calc(100% / 17)"
                    class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                    Инд
                </th>
                <th style="width: calc(100% / 17)"
                    class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                    Гот
                </th>
                <th style="width: calc(100% / 17)"
                    class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                    РК
                </th>
                <th style="width: calc(100% / 17)"
                    class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                    СЕО
                </th>
                <th style="width: calc(100% / 17)"
                    class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                    Иное
                </th>
                <th style="width: calc(100% / 17)"
                    class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                    План Месяц
                </th>
                <th style="width: calc(100% / 17)"
                    class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                    Двойной план
                </th>
                <th style="width: calc(100% / 17)"
                    class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                    Бонус план
                </th>
                <th style="width: calc(100% / 17)"
                    class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                    Супер план
                </th>
                <th style="width: calc(100% / 17)"
                    class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                    Б1
                </th>
                <th style="width: calc(100% / 17)"
                    class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                    Б2
                </th>
                <th style="width: calc(100% / 17)"
                    class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                    Б3
                </th>
                <th style="width: calc(100% / 17)"
                    class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                    Б4
                </th>
                <th style="width: calc(100% / 17)"
                    class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                    Итого
                </th>
            </tr>
        </thead>
        <tbody>
            <tr v-if="unusedPayments">
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    GRAMPUS
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ unusedPayments.newMoney }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ unusedPayments.oldMoney }}
                </td>
                <td colspan="5" class="border text-xs border-gray-300 text-md px-1 py-1">
                    -
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    -
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    -
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    -
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    -
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    -
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    -
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    -
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    -
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ unusedPayments.allMoney }}
                </td>
            </tr>
            <tr v-for="user in pivotUsers">
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ user.name }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ formatPrice(user.totalValues.newMoney) }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ formatPrice(user.totalValues.oldMoney) }}
                </td>
                <td v-for="service in user.totalValues.servicesByCatsCount"
                    class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ service }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ user.monthPlan.completed ? 'Да' : 'Нет' }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ user.doublePlan.completed ? 'Да' : 'Нет' }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ user.bonusPlan.completed ? 'Да' : 'Нет' }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ user.superPlan.completed ? 'Да' : 'Нет' }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ user.b1.completed ? 'Да' : 'Нет' }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ user.b2.completed ? 'Да' : 'Нет' }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ user.b3.completed ? 'Да' : 'Нет' }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ user.b4.completed ? 'Да' : 'Нет' }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ formatPrice(user.salary.amount) }}
                </td>
            </tr>
        </tbody>
    </table>

</template>
<script>
export default {
    props: {
        pivotUsers: {
            type: Object,
            required: true,
        },
        unusedPayments: {
            type: Object,
        }
    },
}


</script>