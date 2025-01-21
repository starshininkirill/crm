<template>

    <Head title="Отчёт Менеджеров продаж" />
    <div class="contract-page-wrapper flex flex-col">
        <h1 class="text-4xl font-semibold mb-6">Отчёт Менеджеров продаж</h1>

        <UserSaleReportSelect :action="route('admin.sale-department.user-report')"
            :departments="departments" :users="users"
            :initial-department="selectedDepartment" :initial-user="user"
            :initial-date="date">
        </UserSaleReportSelect>

        <div class="text-2xl font-semibold">
            Тут будет отчёт
        </div>
    </div>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import SaleDepartmentLayout from '../Layouts/SaleDepartmentLayout.vue';
import UserSaleReportSelect from '../../../Components/UserSaleReportSelect.vue';

export default {
    components: {
        Head,
        UserSaleReportSelect
    },
    props: {
        departments: {
            type: Array,
        },
        users: {
            type: Array,
        },
        user: {
            type: Object,
        },
        selectedDepartment: {
            type: Object
        },
        date: {
            type: String
        }
    },
    layout: SaleDepartmentLayout,
}


</script>