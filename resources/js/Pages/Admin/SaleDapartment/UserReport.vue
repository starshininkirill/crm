<template>
    <SaleDepartmentLayout>

        <Head title="Отчёт Менеджеров продаж" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Отчёт Менеджеров продаж</h1>

            <SelectForm :departments="departments" :users="users" :initial-department="selectedDepartment"
                :initial-user="selectUser" :initial-date="date" />
            <Error />

            <template v-if="!report?.motivationReport?.weeksPlan">
                <div class="text-2xl font-semibold">
                    Данные для отчёта не найдены
                </div>
            </template>

            <div v-else class="flex gap-4">
                <table class="reports w-1/2">
                    <DailyReport v-if="report.daylyReport?.length" :report="report.daylyReport" />
                    <WeeksReport v-if="report.motivationReport?.weeksPlan"
                        :totalValues="report.motivationReport.totalValues" :weeks="report.motivationReport.weeksPlan" />
                    <MotivationReport v-if="report.motivationReport?.weeksPlan"
                        :motivationReport="report.motivationReport" />
                </table>
                <table class="pivot-reports w-1/2 h-fit">
                    <DailyReport v-if="report.pivotDaily?.length" :report="report.pivotDaily" />
                    <WeeksReport v-if="report.pivotWeeks?.weeksPlan" :weeks="report.pivotWeeks.weeksPlan"
                        :totalValues="report.pivotWeeks.totalValues" />
                    <GeneralReport v-if="report.generalPlan && Object.keys(report.generalPlan).length > 0"
                        :generalPlan="report.generalPlan" />
                </table>
            </div>

            <div v-if="report.pivotUsers" class="w-100 mt-6">
                <PivotUsersReport :pivotUsers="report.pivotUsers" :unusedPayments="report.unusedPayments || []" />
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
        report: {
            type: Object
        },
    },
}
</script>