<template>
    <UserLayout>

        <Head title="Кадровый табель" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Кадровый табель</h1>
        </div>

        <div class="flex gap-3 max-w-xl mb-4">
            <div class=" w-2/4 flex flex-col">
                <label class="label">Отдел</label>
                <VueSelect class="full-vue-select h-full" v-model="selectedDepartment"
                    :reduce="department => department" label="name" :options="departments">
                </VueSelect>
            </div>
            <div class="w-1/4 flex flex-col">
                <label class="label">Дата</label>
                <VueDatePicker v-model="selectedDate" auto-apply="true" month-picker locale="ru" class="h-full" />
            </div>
            <div class="btn h-fit mt-auto !w-fit">
                Выбрать
            </div>
        </div>

        <table
            class="shadow-md border-collapse overflow-hidden rounded-md sm:rounded-lg w-full text-sm text-left rtl:text-right text-gray-500 ">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50  ">
                <tr>
                    <th scope="col" class="px-3 py-3 border-r">
                        Ставка
                    </th>
                    <th scope="col" class="px-3 py-3 border-r">
                        Час
                    </th>
                    <th scope="col" class="px-3 py-3 border-r">
                        Должность
                    </th>
                    <th scope="col" class="px-3 py-3 border-r">
                        ФИО
                    </th>
                    <th v-for="(day, idx) in days" scope="col" class="px-1 py-3 border-r">
                        {{ idx }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="user in usersReport" :key="user.id" class="bg-white border-b   hover:bg-gray-50 ">
                    <td class="px-3 py-4 border-r">
                        0
                    </td>
                    <td class="px-3 py-4 border-r">
                        0
                    </td>
                    <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap border-r">
                        Должность
                    </th>
                    <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap border-r">
                        {{ user.full_name }}
                    </th>
                    <td v-for="day in user.days" class="px-3 py-4 border-r" :class="getActionColor(day)">
                        {{ day.isWorkingDay == false ? '' : '9' }}
                    </td>
                </tr>
            </tbody>
        </table>
    </UserLayout>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import UserLayout from '../../Layouts/UserLayout.vue';
import VueSelect from 'vue-select';
import VueDatePicker from '@vuepic/vue-datepicker'

export default {
    components: {
        Head,
        UserLayout,
        VueSelect,
        VueDatePicker
    },
    props: {
        days: {
            type: Array,
            required: true,
        },
        departments: {
            type: Array,
            required: true,
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
            selectedDate: this.date,
            selectedDepartment: null,
        }
    },
    methods: {
        getActionColor(day) {
            if (day.status) {
                if (day.status.work_status.type == "sick_leave" || day.status.work_status.type == "own_day") {
                    return 'bg-cyan-400';
                }
                if (day.status.work_status.type == "homework") {
                    return 'bg-orange-400'
                }
                if (day.status.work_status.type == "vacation") {
                    return 'bg-stone-400'
                }
            }

            if (!day.isWorkingDay) {
                return 'bg-gray-400'
            }

            return ''

        },
    }
}


</script>