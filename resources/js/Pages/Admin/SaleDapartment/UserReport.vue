<template>
    <SaleDepartmentLayout>

        <Head title="Отчёт Менеджеров продаж" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Отчёт Менеджеров продаж</h1>

            <SelectForm :departments="departments" :users="users" :initial-department="selectedDepartment"
                :initial-user="selectUser" :initial-date="date">
            </SelectForm>

            <Error />

            <template v-if="motivationReport.length == 0 && motivationReport.length == 0">
                <div class="text-2xl font-semibold">
                    Данные для отчёта не найдены
                </div>
            </template>
            <div class="flex gap-4">
                <table class="reports w-1/2">
                    <DailyReport v-if="daylyReport.length" :report="daylyReport" />
                    <WeeksReport v-if="motivationReport && motivationReport.weeksPlan"
                        :totalValues="motivationReport.totalValues" :weeks="motivationReport.weeksPlan" />
                    <MotivationReport v-if="motivationReport && motivationReport.weeksPlan"
                        :motivationReport="motivationReport" />
                </table>
                <table class="pivot-reports w-1/2 h-fit">
                    <DailyReport v-if="pivotDaily.length" :report="pivotDaily" />
                    <WeeksReport v-if="pivotWeeks && pivotWeeks.weeksPlan" :weeks="pivotWeeks.weeksPlan"
                        :totalValues="pivotWeeks.totalValues" />
                    <GeneralReport v-if="generalPlan && Object.keys(generalPlan).length > 0"
                        :generalPlan="generalPlan" />
                </table>
            </div>

            <div class="w-100 mt-6">
                <PivotUsersReport  :pivotUsers="pivotUsers" :unusedPayments="unusedPayments" />
            </div>
        </div>
    </SaleDepartmentLayout>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import SaleDepartmentLayout from '../Layouts/SaleDepartmentLayout.vue';
import SelectForm from './Components/SelectForm.vue';
import DailyReport from './Components/DailyReport.vue';
import WeeksReport from './Components/WeeksReport.vue';
import MotivationReport from './Components/MotivationReport.vue';
import GeneralReport from './Components/GeneralReport.vue';
import PivotUsersReport from './Components/PivotUsersReport.vue';
import Error from '../../../Components/Error.vue';

export default {
    components: {
        Head,
        SelectForm,
        DailyReport,
        WeeksReport,
        MotivationReport,
        GeneralReport,
        PivotUsersReport,
        Error,
        SaleDepartmentLayout
    },
    props: {
        departments: {
            type: Array,
        },
        users: {
            type: Array,
        },
        selectUser: {
            type: Object,
        },
        selectedDepartment: {
            type: Object
        },
        date: {
            type: String
        },
        daylyReport: {
            type: Object
        },
        motivationReport: {
            type: Object,
        },
        pivotDaily: {
            type: Object,
        },
        pivotWeeks: {
            typy: Object
        },
        generalPlan: {
            type: Object,
        },
        pivotUsers: {
            type: Object,
        },
        unusedPayments: {
            type: Object,
        }
    },
}


</script>