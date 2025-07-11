<template>
    <SaleDepartmentLayout>

        <Head title="Настройка планов отдела продаж" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Настройка планов отдела продаж</h1>
        </div>

        <div class=" flex gap-3 mb-4">
            <div class="w-fit flex flex-col">
                <label class="label">Дата</label>
                <VueDatePicker v-model="date" model-type="yyyy-MM" :auto-apply="true" month-picker locale="ru"
                    @update:modelValue="changeDate" class="h-full" />
            </div>
        </div>

        <Error />

        <div class="grow w-full border-b mb-6">
            <div class="flex gap-3">
                <div @click="isRopActive = false"
                    :class="[{ 'text-white bg-gray-800': !isRopActive }, 'px-4 py-2 border border-y-0 cursor-pointer border-t']">
                    Планы отдела
                </div>
                <div @click="isRopActive = true"
                    :class="[{ 'text-white bg-gray-800': isRopActive }, 'px-4 py-2 border border-y-0 cursor-pointer border-t']">
                    Планы РОП
                </div>
            </div>
            <slot />
        </div>


        <div v-if="!isRopActive" class="grid grid-cols-3 gap-8 mb-4">
            <MonthPlan :isCurrentMonth="isCurrentMonth" :monthPlan="plans.monthPlan" :departmentId="departmentId" />
            <PercentLadder :isCurrentMonth="isCurrentMonth" :percentLadder="plans.percentLadder"
                :departmentId="departmentId" :propNoPercentageMonth="plans.noPercentageMonth" />

            <div class="flex flex-col gap-4">
                <UniversalPlan :departmentId="departmentId" :isCurrentMonth="isCurrentMonth" :title="'Бонус план'"
                    :planType="'bonusPlan'" :plans="plans.bonusPlan" :hasGoalField="true"
                    info="Сумма новых денег в текущем расчетном периоде больше или равна" />
                <UniversalPlan :departmentId="departmentId" :isCurrentMonth="isCurrentMonth" :title="'Двойной план'"
                    :planType="'doublePlan'" :plans="plans.doublePlan"
                    info="Сумма новых денег в текущем расчетном периоде больше или равна удвоенному плану" />
                <UniversalPlan :departmentId="departmentId" :isCurrentMonth="isCurrentMonth" :title="'План недели'"
                    :planType="'weekPlan'" :plans="plans.weekPlan" />
                <UniversalPlan :departmentId="departmentId" :isCurrentMonth="isCurrentMonth" :title="'Супер план'"
                    :planType="'superPlan'" :plans="plans.superPlan" :hasGoalField="true"
                    info="Сумма новых денег в текущем расчетном периоде больше или равна Цели или выполненны все планы недель" />
            </div>

            <B1Plan :propPlan="plans.b1Plan" :isCurrentMonth="isCurrentMonth" :departmentId="departmentId" />

            <B4Plan :propPlan="plans.b4Plan" :propServices="rkServices" :isCurrentMonth="isCurrentMonth"
                :departmentId="departmentId" />
            <div>

            </div>

            <B2Plan :services="services" :propPlan="plans.b2Plan" :propSeoServices="seoServices"
                :isCurrentMonth="isCurrentMonth" :departmentId="departmentId" />


            <div class=" col-span-2">
                <B3Plan :serviceCats="serviceCats" :services="services" :propPlan="plans.b3Plan"
                    :isCurrentMonth="isCurrentMonth" :departmentId="departmentId" />
            </div>

        </div>

        <div v-if="isRopActive" class="grid grid-cols-3 gap-8 mb-4">
            <div class="flex flex-col gap-4">
                <UniversalPlan :departmentId="departmentId" :isCurrentMonth="isCurrentMonth" :title="'Б1 План'"
                    :planType="'headB1Plan'" :plans="plans.headB1Plan" :hasGoalField="true" 
                    info="Сумма всех планов перевыполненная на ... %" />
                <UniversalPlan :departmentId="departmentId" :isCurrentMonth="isCurrentMonth" :title="'Б2 План'"
                    :planType="'headB2Plan'" :plans="plans.headB2Plan" :hasGoalField="true"
                    info="Сумма всех планов перевыполненная на ... %" />
                <UniversalPlan :departmentId="departmentId" :isCurrentMonth="isCurrentMonth" :title="'Процент с продаж'"
                    :planType="'headPercentBonus'" :plans="plans.headPercentBonus" :hasGoalField="false"
                    info="Руководитель отдела продаж получает ...% от новых денег заведенных его отделом" />
                <UniversalPlan :departmentId="departmentId" :isCurrentMonth="isCurrentMonth" :title="'Минимальный бонус'"
                    :planType="'headMinimalBonus'" :plans="plans.headMinimalBonus" :hasGoalField="false" 
                    info="Минимальный % от процента с продаж после вычетов за менеджеров не выполнивших план" />
            </div>
        </div>

    </SaleDepartmentLayout>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import SaleDepartmentLayout from '../Layouts/SaleDepartmentLayout.vue';
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';
import FormInput from '../../../Components/FormInput.vue';
import MonthPlan from './Settings/DepartmentSettings/MonthPlan.vue';
import UniversalPlan from './Settings/DepartmentSettings/UniversalPlan.vue';
import B1Plan from './Settings/DepartmentSettings/B1Plan.vue';
import B2Plan from './Settings/DepartmentSettings/B2Plan.vue';
import B3Plan from './Settings/DepartmentSettings/B3Plan.vue';
import B4Plan from './Settings/DepartmentSettings/B4Plan.vue';
import PercentLadder from './Settings/DepartmentSettings/PercentLadder.vue';
import Error from '../../../Components/Error.vue';
import VueDatePicker from '@vuepic/vue-datepicker'

export default {
    components: {
        Head,
        FormInput,
        MonthPlan,
        UniversalPlan,
        B1Plan,
        B2Plan,
        B3Plan,
        B4Plan,
        PercentLadder,
        Error,
        SaleDepartmentLayout,
        VueDatePicker
    },
    props: {
        dateProp: {
            type: String,
            required: true,
        },
        plans: {
            type: Object,
            required: true,
        },
        isCurrentMonth: {
            type: Boolean,
            required: true,
        },
        departmentId: {
            type: Number,
            required: true,
        },
        rkServices: {
            type: Array,
            required: true,
        },
        seoServices: {
            type: Array,
            required: true,
        },
        services: {
            type: Array,
            required: true,
        },
        serviceCats: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            date: this.dateProp,
            isRopActive: false,
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