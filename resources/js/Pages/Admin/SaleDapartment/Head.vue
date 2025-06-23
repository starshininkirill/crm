<template>
    <SaleDepartmentLayout>

        <Head title="Руководители отдела продаж" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Руководители отдела продаж</h1>

            <div class=" max-w-40 flex flex-col mb-4">
                <label class="label">Дата</label>
                <VueDatePicker v-model="modelDate" model-type="yyyy-MM" :auto-apply="true" month-picker locale="ru"
                    class="h-full" @update:modelValue="changeDate" />
            </div>

            <div v-if="error" class="mb-4 text-xl text-red-400">{{ error }}</div>

            <table v-if="Object.keys(report).length != 0" class="overflow-hidden table ">
                <thead class="thead border-b">
                    <tr>
                        <th scope="col" class="px-6 py-3 border-x">
                            Отдел
                        </th>
                        <th scope="col" class="px-6 py-3 border-r">
                            Руководитель
                        </th>
                        <th scope="col" class="px-6 py-3 border-r max-w-36">
                            Сотрудников в отделе
                        </th>
                        <th scope="col" class="px-6 py-3 border-r">
                            Выполнили план
                        </th>
                        <th scope="col" class="px-6 py-3 border-r">
                            NEW $
                        </th>
                        <th scope="col" class="px-6 py-3 border-r">
                            План отдела
                        </th>
                        <th scope="col" class="px-6 py-3 border-r">
                            Б1
                        </th>
                        <th scope="col" class="px-6 py-3 border-r">
                            Б2
                        </th>
                        <th scope="col" class="px-6 py-3 border-x">
                            Бонусы
                        </th>
                        <th scope="col" class="px-6 py-3 border-x">
                            Проценты
                        </th>
                        <th scope="col" class="px-6 py-3 border-r">
                            Ставка
                        </th>
                        <th scope="col" class="px-6 py-3 border-x">
                            Итого ЗП
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in report" :key="row.head.id" class="table-row ">
                        <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap  border-x">
                            {{ row.department.name }}
                        </th>
                        <td class="px-4 py-3 border-r">
                            {{ row.head.full_name }}
                        </td>
                        <td class="px-4 py-3 border-r">
                            {{ row.report.usersCount }}
                        </td>
                        <td class="px-4 py-3 border-r">
                            {{ row.report.completed }}/{{ row.report.usersCount }} ({{ row.report.completedPercent }}%)
                        </td>
                        <td class="px-4 py-3 border-r whitespace-nowrap">
                            {{ formatPrice(row.report.newMoney) }}
                        </td>
                        <td class="px-4 py-3 border-r">
                            {{ formatPrice(row.report.generalPlan, '') }} / {{ formatPrice(row.report.remainingAmount, '') }}
                        </td>
                        <td class="px-4 py-3 border-r whitespace-nowrap"
                            :class="row.report.b1.completed ? ' bg-green-500 text-white' : ''">
                            {{ formatPrice(row.report.b1.goal, '') }} / {{ formatPrice(row.report.b1.remainingAmount,
                                '') }}
                        </td>
                        <td class="px-4 py-3 border-r whitespace-nowrap"
                            :class="row.report.b2.completed ? ' bg-green-500 text-white' : ''">
                            {{ formatPrice(row.report.b2.goal, '') }} / {{ formatPrice(row.report.b2.remainingAmount,
                                '') }}
                        </td>
                        <td class="px-4 py-3 border-r">
                            {{ formatPrice(row.report.bonus) }}
                        </td>
                        <td class="px-4 py-3 border-r">
                            {{ formatPrice(row.report.headBonus) }}
                        </td>
                        <td class="px-4 py-3 border-r whitespace-nowrap">
                            {{ formatPrice(row.head.calculated_salary) }}
                        </td>
                        <td class="px-4 py-3 border-r whitespace-nowrap">
                            {{ formatPrice(row.report.headBonus + row.head.calculated_salary + row.report.bonus) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </SaleDepartmentLayout>
</template>

<script>
import { Head, router } from '@inertiajs/vue3';
import SaleDepartmentLayout from '../Layouts/SaleDepartmentLayout.vue';
import Error from '../../../Components/Error.vue';
import VueDatePicker from '@vuepic/vue-datepicker'
import { route } from 'ziggy-js';

export default {
    components: {
        Head,
        SaleDepartmentLayout,
        Error,
        VueDatePicker,
    },
    props: {
        report: {
            type: Object,
        },
        error: {
            type: String,
        },
        date: {
            type: String
        },
    },
    data() {
        return {
            modelDate: this.date,
        }
    },
    methods: {
        changeDate() {
            router.get(route('admin.sale-department.heads'), {
                date: this.modelDate,
            })
        }
    }
}


</script>