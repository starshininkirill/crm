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

        <table v-if="usersReport.length"
            class="shadow-md border-collapse rounded-md sm:rounded-lg w-full text-sm text-left rtl:text-right text-gray-500 ">
            <thead class="thead ">
                <tr>
                    <th scope="col" class="px-2 py-2 border-r">
                        Ставка
                    </th>
                    <th scope="col" class="px-2 py-2 border-r">
                        Час
                    </th>
                    <th scope="col" class="px-2 py-2 border-r">
                        Должность
                    </th>
                    <th scope="col" class="px-2 py-2 border-r">
                        ФИО
                    </th>
                    <th v-for="(day, idx) in days" scope="col" class="px-1 py-2 border-r text-center">
                        {{ idx }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in usersReport" :key="user.id" class="table-row ">
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
                    <td v-for="day in user.days" class="px-2 py-3 border-r text-center cursor-pointer relative group">
                        <div class="absolute inset-0 flex z-0">
                            <div v-for="color in getActionColor(day)" :class="color" class="h-full"></div>
                        </div>
                        <span class=" relative z-10" :class="getActionColor(day).length ? 'text-white' : ''">
                            {{ day.hours == 0 ? '' : day.hours }}
                            {{ day.date == user.fired_at ? 'Уволен' : '' }}
                        </span>

                        <div v-if="day.statuses.length || day.timeCheckHours != null"
                            class="absolute hidden group-hover:block z-20 bg-white shadow-lg rounded-md p-2 border border-gray-200 min-w-[200px] left-2/4 transform -translate-x-full mt-2 pointer-events-none">
                            <div class="flex flex-col gap-1">
                                <div v-if="day.timeCheckHours != null" class="flex justify-between items-center">
                                    <span class="font-medium text-gray-600">Отработно часов: </span>
                                    <span class="text-gray-600">
                                        {{ day.timeCheckHours }} ч
                                    </span>
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
                </tr>
            </tbody>
        </table>
        <h1 v-else class="text-4xl font-semibold mb-6">
            Нет данных для расчёта
        </h1>
    </TimeCheckLayout>
</template>

<script>
import { Head, router } from '@inertiajs/vue3';
import TimeCheckLayout from '../../Layouts/TimeCheckLayout.vue';
import VueSelect from 'vue-select';
import VueDatePicker from '@vuepic/vue-datepicker'
import { route } from 'ziggy-js';

export default {
    components: {
        Head,
        TimeCheckLayout,
        VueSelect,
        VueDatePicker
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
            type: Array,
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
        }
    },
    methods: {
        getActionColor(day) {
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
                    colors.push('bg-stone-400')
                }
            }

            if (!day.isWorkingDay) {
                colors.push('bg-gray-400')
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
        getDailyStatusName(statusId) {

        }
    }
}


</script>