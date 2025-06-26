<template>
    <TimeCheckLayout>

        <Head title="Кадровый табель" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Кадровый табель</h1>
        </div>

        <div class="flex gap-3 max-w-3xl mb-4">
            <div class=" w-2/4 flex flex-col">
                <label class="label">Отдел</label>
                <VueSelect class="full-vue-select h-full" v-model="selectedDepartment"
                    :reduce="department => department.id" label="name" :options="departmentOptions">
                </VueSelect>
            </div>
            <div class=" w-2/4 flex flex-col">
                <label class="label">Статус</label>
                <VueSelect class="full-vue-select h-full" v-model="selectedStatus" :reduce="status => status.value"
                    label="name" :options="statuses">
                </VueSelect>
            </div>
            <div class="w-1/4 flex flex-col">
                <label class="label">Дата</label>
                <VueDatePicker v-model="selectedDate" model-type="yyyy-MM" :auto-apply="true" month-picker locale="ru"
                    class="h-full" />
            </div>

            <div @click="updateDate" class="btn h-fit mt-auto !w-fit">
                Выбрать
            </div>
        </div>
        <div class="overflow-x-auto w-full max-w-[1660px] bg-white rounded-lg shadow-md">
            <table v-if="Object.keys(usersReport).length"
                class="shadow-md border-collapse rounded-md sm:rounded-lg text-sm text-left rtl:text-right text-gray-500 whitespace-nowrap table-fixed w-full">
                <thead class="thead ">
                    <tr>
                        <th class="px-2 py-2 border-r w-20">
                            Ставка
                        </th>
                        <th class="px-2 py-2 border-r w-16">
                            Час
                        </th>
                        <th class="px-2 py-2 border-r w-60">
                            Должность
                        </th>
                        <th class="px-2 py-2 border-r w-60">
                            ФИО
                        </th>
                        <th v-for="(day, idx) in days" v-show="idx == 1 || showAllDates" :key="idx"
                            class="px-1 py-2 border-r text-center w-8" :class="{ 'cursor-pointer': idx == 1 }"
                            @click="idx == 1 ? toggleDates() : null">
                            <span>
                                {{ idx }}
                            </span>
                            <span v-if="idx == 1" class="w-fit ml-1">
                                {{ showAllDates ? '▼' : '▲' }}
                            </span>
                        </th>
                        <th class="px-2 py-2 border-r whitespace-normal w-20">
                            Ставка
                        </th>
                        <th class="px-2 py-2 border-r whitespace-normal w-20">
                            Премия
                        </th>
                        <th class="px-2 py-2 border-r whitespace-normal w-20">
                            Доп часы
                        </th>
                        <th class="px-2 py-2 border-r whitespace-normal w-20">
                            Часы
                        </th>
                        <th class="px-2 py-2 border-r whitespace-normal w-20">
                            Доп + и лишения
                        </th>
                        <th class="px-2 py-2 border-r whitespace-normal w-20">
                            Итого к выплате
                        </th>
                        <th class="px-2 py-2 border-r whitespace-normal w-20">
                            Ставка
                        </th>
                        <th class="px-2 py-2 border-r whitespace-normal w-20">
                            Премия
                        </th>
                        <th class="px-2 py-2 border-r whitespace-normal w-20">
                            Доп часы
                        </th>
                        <th class="px-2 py-2 border-r whitespace-normal w-20">
                            Часы
                        </th>
                        <th class="px-2 py-2 border-r whitespace-normal w-20">
                            Доп + и лишения
                        </th>
                        <th class="px-2 py-2 border-r whitespace-normal w-20">
                            Итого к выплате
                        </th>
                    </tr>
                </thead>
                <tbody v-for="department, key in usersReport">
                    <tr class="text-xs text-gray-700 text-center uppercase bg-gray-50">
                        <td colspan="100%" class="px-2 py-2 bg-gray-800 text-white font-semibold">
                            {{ key == '' ? 'Без отдела' : key }}
                        </td>
                    </tr>
                    <tr v-for="user in department" :key="user.id" class="table-row ">
                        <td class="px-2 py-3 border-r">
                            {{ formatPrice(user.salary) }}
                        </td>
                        <td class="px-2 py-3 border-r">
                            {{ formatPrice(user.hour_salary) }}
                        </td>
                        <th scope="row" class="px-2 py-3 font-medium text-gray-900 whitespace-nowrap border-r">
                            {{ user.position?.name ?? 'Не указана' }}
                        </th>
                        <th scope="row" class="px-2 py-3 font-medium text-gray-900 whitespace-nowrap border-r">
                            {{ user.full_name }}
                        </th>
                        <td v-for="(day, index) in user.days"
                            class="px-2 py-3 border-r text-center cursor-pointer relative group"
                            v-show="showAllDates || index == 1">
                            <div class="absolute inset-0 flex z-0">
                                <div v-for="color in getActionColor(day, user)" :class="color" class="h-full"></div>
                            </div>
                            <span class=" relative z-10" :class="getActionColor(day, user).length ? 'text-white' : ''">
                                {{ day.hours == 0 ? '' : day.hours }}
                                {{ day.date == user.fired_at ? 'Ув' : '' }}
                            </span>

                            <div v-if="day.statuses.length || day.timeCheckHours != null || day.date == user.fired_at"
                                class="absolute hidden group-hover:block z-20 bg-white shadow-lg rounded-md p-2 border border-gray-200 min-w-[200px] left-2/4 transform -translate-x-full mt-2 pointer-events-none">
                                <div class="flex flex-col gap-1">
                                    <div v-if="day.timeCheckHours != null" class="flex justify-between items-center">
                                        <span class="font-medium text-gray-600">Отработно часов: </span>
                                        <span class="text-gray-600">
                                            {{ day.timeCheckHours }} ч
                                        </span>
                                    </div>
                                    <div v-if="day.date == user.fired_at" class="flex justify-between items-center">
                                        <span class="font-medium text-gray-600">Уволен </span>
                                    </div>
                                    <div v-for="status in day.statuses" class="flex justify-between items-center">
                                        <span class="font-medium text-gray-600">{{ status.work_status.name }}:</span>
                                        <span v-if="status.work_status.type != 'late'" class="text-gray-600">
                                            <span v-if="status.status == 'approved'">
                                                {{ status.hours ?? 0 }}
                                            </span>
                                            <span v-else>
                                                0
                                            </span>
                                            ч
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td class="px-2 py-2 border-r w-20 text-center">
                            {{ formatPrice(user.part_salary) }}
                        </td>
                        <td class="px-2 py-2 border-r w-20 text-center">
                            {{ formatPrice(user.bonuses) }}
                        </td>
                        <td class="px-2 py-2 border-r w-20 text-center">
                            {{ formatPrice(user.first_half_hours_money) }}
                        </td>
                        <td class="px-2 py-2 border-r w-20 text-center">
                            {{ user.first_half_hours }}
                        </td>
                        <td class="px-2 py-2 border-r w-20 text-center">

                        </td>
                        <td class="px-2 py-2 border-r w-20 text-center">
                            {{ formatPrice(user.amount_first_half_salary) }}
                        </td>
                        <td class="px-2 py-2 border-r w-20 text-center">
                            {{ formatPrice(user.part_salary) }}
                        </td>
                        <td class="px-2 py-2 border-r w-20 text-center">
                            0 ₽
                        </td>
                        <td class="px-2 py-2 border-r w-20 text-center">
                            {{ formatPrice(user.second_half_hours_money) }}
                        </td>
                        <td class="px-2 py-2 border-r w-20 text-center">
                            {{ user.second_half_hours }}
                        </td>
                        <td class="px-2 py-2 border-r w-20 text-center">

                        </td>
                        <td class="px-2 py-2 border-r w-20 text-center">
                            {{ formatPrice(user.amount_second_half_salary) }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <h1 v-else class="text-4xl font-semibold mb-6">
                Нет данных для расчёта
            </h1>
        </div>

        <HelpStatusLegend />
    </TimeCheckLayout>
</template>

<script>
import { Head, router } from '@inertiajs/vue3';
import TimeCheckLayout from '../../Layouts/TimeCheckLayout.vue';
import VueSelect from 'vue-select';
import VueDatePicker from '@vuepic/vue-datepicker'
import { route } from 'ziggy-js';
import HelpStatusLegend from './HelpStatusLegend.vue';

export default {
    components: {
        Head,
        TimeCheckLayout,
        VueSelect,
        VueDatePicker,
        HelpStatusLegend
    },
    props: {
        days: {
            type: Object,
            required: true,
        },
        departments: {
            type: Array,
            required: true,
        },
        department: {
            type: Object,
        },
        date: {
            type: String,
            required: true,
        },
        usersReport: {
            type: Object,
            required: true,
        },
        status: {
            type: String,
            required: true,
        },
    },
    data() {
        let statuses = [
            {
                'name': 'Все',
                'value': 'all'
            },
            {
                'name': 'Активные',
                'value': 'active'
            },
            {
                'name': 'Уволенные',
                'value': 'fired'
            }
        ]

        return {
            departmentOptions: [
                { id: null, name: 'Все' },
                ...this.departments
            ],
            statuses,
            selectedStatus: this.status ?? statuses[0],
            selectedDate: this.date,
            selectedDepartment: this.department?.id ?? null,
            showAllDates: false,
        }
    },
    methods: {
        getActionColor(day, user) {
            let colors = [];

            if (day.status) {
                if (day.status.work_status?.type == 'late') {
                    colors.push('bg-red-500');
                }

                if (day.status.work_status?.type == "sick_leave" || day.status.work_status?.type == "own_day") {
                    colors.push('bg-cyan-400');
                }

                if (day.status.work_status?.type == "homework" || day.status.work_status?.type == "part_time_day") {
                    colors.push('bg-orange-400')
                }

                if (day.status.work_status?.type == "vacation") {
                    colors.push('bg-cyan-500')
                }
            }

            if (!day.isWorkingDay) {
                colors.push('bg-gray-400')
            }

            if (user && day.date >= user.fired_at &&  day.isWorkingDay) {
                console.log('test');
                
                colors.push('bg-red-700');
            }

            if (day.isLate) {
                colors.push('bg-red-500');
            }
            

            colors = [...new Set(colors)];

            return colors.map(color => `${color} flex-1`);
        },
        updateDate() {
            router.get(route('admin.time-sheet'), {
                date: this.selectedDate,
                department_id: this.selectedDepartment,
                status: this.selectedStatus,
            })
        },
        toggleDates() {
            this.showAllDates = !this.showAllDates;
        },
    }
}


</script>