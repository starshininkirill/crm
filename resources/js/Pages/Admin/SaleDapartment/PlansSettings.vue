<template>

    <Head title="Настройка планов отдела продаж" />
    <div class="contract-page-wrapper flex flex-col">
        <h1 class="text-4xl font-semibold mb-6">Настройка планов отдела продаж</h1>
    </div>

    <div class=" flex gap-3 mb-4">
        <FormInput @change="changeDate" v-model="date" name="date" type="month" />
    </div>

    <ul v-if="$page.props.errors" class="flex flex-col gap-1 mb-4">
        <li v-for="(error, index) in $page.props.errors" :key="index" class="text-red-400">{{ error }}</li>
    </ul>


    <div class="grid grid-cols-3 gap-8">
        <MonthPlan :isCurrentMonth="isCurrentMonth" :monthPlan="plans.monthPlan" :departmentId="departmentId" />
        <div class="flex flex-col gap-4">
            <UniversalPlan :departmentId="departmentId" :isCurrentMonth="isCurrentMonth" :title="'Бонус план'"
                :planType="'bonusPlan'" :plans="plans.bonusPlan" :hasGoalField="true" />
            <UniversalPlan :departmentId="departmentId" :isCurrentMonth="isCurrentMonth" :title="'Двойной план'"
                :planType="'doublePlan'" :plans="plans.doublePlan" />
            <UniversalPlan :departmentId="departmentId" :isCurrentMonth="isCurrentMonth" :title="'План недели'"
                :planType="'weekPlan'" :plans="plans.weekPlan" />
            <UniversalPlan :departmentId="departmentId" :isCurrentMonth="isCurrentMonth" :title="'Супер план'"
                :planType="'superPlan'" :plans="plans.superPlan" :hasGoalField="true" />
        </div>
        <div class="flex flex-col gap-4">
            <B1Plan :propPlan="plans.b1Plan" :isCurrentMonth="isCurrentMonth" :departmentId="departmentId" />
        </div>
    </div>


</template>

<script>
import { Head } from '@inertiajs/vue3';
import SaleDepartmentLayout from '../Layouts/SaleDepartmentLayout.vue';
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';
import FormInput from '../../../Components/FormInput.vue';
import MonthPlan from './Settings/MonthPlan.vue';
import UniversalPlan from './Settings/UniversalPlan.vue';
import B1Plan from './Settings/B1Plan.vue';

export default {
    components: {
        Head,
        FormInput,
        MonthPlan,
        UniversalPlan,
        B1Plan
    }, 
    layout: SaleDepartmentLayout,
    props: {
        dateProp: {
            type: String,
        },
        plans: {
            type: Object,
        },
        isCurrentMonth: {
            type: Boolean
        },
        departmentId: {
            type: Number
        }
    },
    data() {
        return {
            date: this.dateProp
        }
    },
    methods: {
        changeDate() {
            router.get(route('admin.sale-department.plans-settings'), {
                date: this.date,
            })
        }
    }
}


</script>