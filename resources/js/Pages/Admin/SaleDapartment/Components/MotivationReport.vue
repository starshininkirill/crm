<template>
    <thead>
        <tr class="bg-gray-800">
            <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                План недели
            </th>
            <th v-for="(week, idx) in motivationReport.weeksPlan"
                class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                неделя {{ idx + 1 }}
            </th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="border text-xs border-gray-300 text-md px-1 py-1">
                {{ formatPrice(motivationReport.weeksPlan[0].goal) }}
            </td>
            <td v-for="week in motivationReport.weeksPlan" class="border text-xs border-gray-300 text-md px-1 py-1"
                :class="week.completed ? ' bg-green-400' : 'bg-red-400'">
                {{ formatPrice(week.newMoney) }}
            </td>
        </tr>
    </tbody>

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
                {{ formatPrice(motivationReport.monthPlan.goal) }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1" :class="motivationReport.doublePlan.completed ? ' bg-green-400' : 'bg-red-400'">
                {{ motivationReport.doublePlan.completed ? 'Да' : 'Нет' }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1" :class="motivationReport.bonusPlan.completed ? ' bg-green-400' : 'bg-red-400'">
                {{ motivationReport.bonusPlan.completed ? 'Да' : 'Нет' }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1" :class="motivationReport.superPlan.completed ? ' bg-green-400' : 'bg-red-400'">
                {{ motivationReport.superPlan.completed ? 'Да' : 'Нет' }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1" :class="motivationReport.b1.completed ? ' bg-green-400' : 'bg-red-400'">
                {{ motivationReport.b1.completed ? 'Да' : 'Нет' }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1" :class="motivationReport.b2.completed ? ' bg-green-400' : 'bg-red-400'">
                {{ motivationReport.b2.completed ? 'Да' : 'Нет' }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1" :class="motivationReport.b3.completed ? ' bg-green-400' : 'bg-red-400'">
                {{ motivationReport.b3.completed ? 'Да' : 'Нет' }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1" :class="motivationReport.b4.completed ? ' bg-green-400' : 'bg-red-400'">
                {{ motivationReport.b4.completed ? 'Да' : 'Нет' }}
            </td>
        </tr>
    </tbody>

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
                {{  motivationReport.salary.percentage }} %
            </td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1">
                {{ formatPrice(motivationReport.salary.newMoney) }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1">
                {{ formatPrice(motivationReport.salary.oldMoney) }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1">
                {{ formatPrice(motivationReport.salary.bonuses)  }}
            </td>
            <td class="border text-xs border-gray-300 text-md px-1 py-1">
                {{ formatPrice(motivationReport.salary.amount) }}
            </td>
        </tr>
    </tbody>

</template>
<script>
export default {
    props: {
        motivationReport: {
            type: Object,
            required: true,
        },
    },
}


</script>