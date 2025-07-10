<template>
    <UsersLayout>

        <Head title="Кадровый табель" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Кадровый табель</h1>
        </div>

        <Modal :modalClass="'overflow-y-auto'" :open="isOpenModal" @close="closeModal()">
            <UserAdjustment v-if="activeUser" :user="activeUser" :half="activeHalf" :date="selectedDate"
                @user-updated="handleUserUpdate" />
        </Modal>

        <Modal :open="isExportModalOpen" @close="closeExportModal()">
            <ExportSalaryModal
                v-if="isExportModalOpen"
                :employment-types="employmentTypes"
                :date="selectedDate"
                :department_id="selectedDepartment"
                :half="selectedHalfForExport"
                @close="closeExportModal()"
            />
        </Modal>

        <div class="flex gap-3 max-w-3xl w-full mb-4 h-fit self-end">
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
        <div class="flex justify-between gap-3">
            <HelpStatusLegend />
            <div class="flex flex-col justify-end">
                <h3 class="font-semibold text-lg mb-3">Сформировать выплату</h3>

                <div class="flex gap-2">
                    <div @click="openExportModal(1)" class="btn !w-fit h-fit">
                        за 1-14 число
                    </div>
                    <div @click="openExportModal(2)" class="btn !w-fit h-fit">
                        за 15-31 число
                    </div>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto w-[calc(100vw-260px)] bg-white rounded-lg  pt-16">
            <table v-if="Object.keys(localUsersReport).length"
                class="shadow-md border-collapse rounded-md sm:rounded-lg text-sm text-left rtl:text-right text-gray-500 whitespace-nowrap w-full border table-fixed">
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
                        <td class="px-2 py-2 bg-zinc-600 border-r w-6 text-center">

                        </td>
                        <th class="px-2 py-2 border-r whitespace-normal w-20">
                            Ставка
                        </th>
                        <th class="px-2 py-2 border-r whitespace-normal w-20">
                            Премия
                        </th>
                        <th class="px-2 py-2 border-r whitespace-normal w-20 relative">
                            Доп часы
                            <span class=" absolute text-xl font-semibold text-black -top-10 whitespace-nowrap">
                                выплаты за 1-14
                            </span>
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
                        <th class="px-2 py-2 border-r whitespace-normal w-24">
                            Итого c компенс...
                        </th>
                        <td class="px-2 py-2 bg-zinc-600 border-r w-6 text-center">

                        </td>
                        <th class="px-2 py-2 border-r whitespace-normal w-20">
                            Ставка
                        </th>
                        <th class="px-2 py-2 border-r whitespace-normal w-20">
                            Премия
                        </th>
                        <th class="px-2 py-2 border-r whitespace-normal w-20 relative">
                            Доп часы
                            <span class=" absolute text-xl font-semibold text-black -top-10 whitespace-nowrap">
                                выплаты за 15-31
                            </span>
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
                        <th class="px-2 py-2 border-r whitespace-normal w-24">
                            Итого c компенс...
                        </th>
                    </tr>
                </thead>
                <tbody v-for="department, key in localUsersReport">
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
                        <td class="px-2 py-2 bg-zinc-600 border-r w-6 text-center">

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
                        <td class="px-2 py-2 border-r w-20 text-center cursor-pointer"
                            @click="openModal(user, 'first_half')">
                            {{ formatPrice(user.first_half_adjustments) }}
                        </td>
                        <td class="px-2 py-2 border-r w-20 text-center">
                            {{ formatPrice(user.amount_first_half_salary) }}
                        </td>
                        <td class="px-2 py-2 border-r w-20 text-center">
                            {{ formatPrice(user.amount_first_half_salary_with_compensation) }}
                        </td>
                        <td class="px-2 py-2 bg-zinc-600 border-r w-6 text-center">

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
                        <td class="px-2 py-2 border-r w-20 text-center cursor-pointer"
                            @click="openModal(user, 'second_half')">
                            {{ formatPrice(user.second_half_adjustments) }}
                        </td>
                        <td class="px-2 py-2 border-r w-20 text-center">
                            {{ formatPrice(user.amount_second_half_salary) }}
                        </td>
                        <td class="px-2 py-2 border-r w-20 text-center">
                            {{ formatPrice(user.amount_second_half_salary_with_compensation) }}
                        </td>
                    </tr>
                </tbody>
            </table>
            <h1 v-else class="text-4xl font-semibold mb-6">
                Нет данных для расчёта
            </h1>
        </div>
    </UsersLayout>
</template>

<script>
import { Head, router } from '@inertiajs/vue3';
import UsersLayout from '../../Layouts/UsersLayout.vue';
import VueSelect from 'vue-select';
import VueDatePicker from '@vuepic/vue-datepicker'
import { route } from 'ziggy-js';
import HelpStatusLegend from './HelpStatusLegend.vue';
import Modal from '../../../../Components/Modal.vue';
import UserAdjustment from './UserAdjustment.vue';
import ExportSalaryModal from './ExportSalaryModal.vue';

export default {
    components: {
        Head,
        UsersLayout,
        VueSelect,
        VueDatePicker,
        HelpStatusLegend,
        Modal,
        UserAdjustment,
        ExportSalaryModal
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
        employmentTypes: {
            type: Object,
            required: true,
        }
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
            isOpenModal: false,
            activeUser: null,
            activeHalf: null,
            localUsersReport: { ...this.usersReport },
            isExportModalOpen: false,
            selectedHalfForExport: null,
        }
    },
    methods: {
        handleUserUpdate(updatedUser) {
            this.activeUser = updatedUser;
            for (const departmentName in this.localUsersReport) {
                const userIndex = this.localUsersReport[departmentName].findIndex(user => user.id === updatedUser.id);
                if (userIndex !== -1) {
                    this.localUsersReport[departmentName][userIndex] = updatedUser;
                    break;
                }
            }
        },
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

            if (user && day.date >= user.fired_at && day.isWorkingDay) {
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
        openModal(user, half) {
            this.isOpenModal = true;
            this.activeUser = user;
            this.activeHalf = half;
        },
        closeModal() {
            this.isOpenModal = false;
            this.activeUser = null;
            this.activeHalf = null;
        },
        openExportModal(half) {
            this.selectedHalfForExport = half;
            this.isExportModalOpen = true;
        },
        closeExportModal() {
            this.isExportModalOpen = false;
            this.selectedHalfForExport = null;
        }
    }
}


</script>