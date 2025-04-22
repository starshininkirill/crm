<template>
    <TimeCheckLayout>

        <Head title="Кадровый табель" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Кадровый табель</h1>
        </div>

        <div class="flex gap-3 max-w-xl mb-4">
            <div class=" w-2/4 flex flex-col">
                <label class="label">Отдел</label>
                <VueSelect class="full-vue-select h-full" v-model="selectedDepartment"
                    :reduce="department => department.id" label="name" :options="departmentOptions">
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

        <table
            class="shadow-md border-collapse rounded-md sm:rounded-lg w-full text-sm text-left rtl:text-right text-gray-500 ">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50  ">
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
                <tr v-for="user in usersReport" :key="user.id" class="bg-white border-b   hover:bg-gray-50 ">
                    <td class="px-2 py-4 border-r">
                        {{ formatPrice(user.salary) }}
                    </td>
                    <td class="px-2 py-4 border-r">
                        {{ formatPrice(user.hour_salary) }}
                    </td>
                    <th scope="row" class="px-2 py-4 font-medium text-gray-900 whitespace-nowrap border-r">
                        {{ user.position?.name ?? 'Не указана' }}
                    </th>
                    <th scope="row" class="px-2 py-4 font-medium text-gray-900 whitespace-nowrap border-r">
                        {{ user.full_name }}
                    </th>
                    <td v-for="day in user.days" class="px-2 py-4 border-r text-center cursor-pointer relative group"
                        :class="getActionColor(day)">
                        {{ day.hours == 0 ? '' : day.hours }}
                        {{ day.date == user.fired_at ? 'Уволен' : '' }}

                        <!-- Всплывающее окно -->
                        <div v-if="day.statuses.length"
                            class="absolute hidden group-hover:block z-10 bg-white shadow-lg rounded-md p-2 border border-gray-200 min-w-[150px] left-0 transform -translate-x-3/4 mt-2">
                            <div class="flex flex-col gap-1">
                                <div v-for="status in day.statuses" class="flex justify-between items-center">
                                    <span class="font-medium text-gray-600">{{ status.work_status.name }}:</span>
                                    <span class="text-gray-600">{{ status.hours ?? 0 }} ч</span>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
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
        }
    },
    data() {
        return {
            departmentOptions: [
                { id: null, name: 'Все' },
                ...this.departments
            ],
            selectedDate: this.date,
            selectedDepartment: this.department?.id ?? null,
        }
    },
    methods: {
        getActionColor(day) {
            if (day.status) {
                if (day.status.work_status?.type == 'late') {
                    return 'bg-red-500 text-white';
                }

                if (day.status.work_status?.type == "sick_leave" || day.status.work_status?.type == "own_day") {
                    return 'bg-cyan-400 text-white';
                }

                if (day.status.work_status?.type == "homework") {
                    return 'bg-orange-400 text-white'
                }

                if (day.status.work_status?.type == "vacation") {
                    return 'bg-stone-400 text-white'
                }
            }

            if (!day.isWorkingDay) {
                return 'bg-gray-400'
            }

            return ''
        },
        updateDate() {
            router.get(route('admin.time-sheet'), {
                date: this.selectedDate,
                department_id: this.selectedDepartment
            })
        },
        getDailyStatusName(statusId) {

        }
    }
}


</script>