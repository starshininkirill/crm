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

        <div class="mb-4 border-b border-gray-200">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
                <li class="mr-2">
                    <button @click="activeTab = null"
                        :class="['inline-block p-4 border-b-2 rounded-t-lg', activeTab == null ? 'text-blue-600 border-blue-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300']">
                        Планы отдела
                    </button>
                </li>
                <li class="mr-2" v-for="position_id in Object.keys(positionalPlans)" :key="position_id">
                    <button @click="activeTab = position_id"
                        :class="['inline-block p-4 border-b-2 rounded-t-lg', activeTab == position_id ? 'text-blue-600 border-blue-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300']">
                        {{positions.find(p => p.id == position_id)?.name}}
                    </button>
                </li>
                <li class="mr-2">
                    <button @click="activeTab = 'rop'"
                        :class="['inline-block p-4 border-b-2 rounded-t-lg', activeTab == 'rop' ? 'text-blue-600 border-blue-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300']">
                        Планы РОП
                    </button>
                </li>
            </ul>
        </div>


        <div v-if="activeTab == null" class="grid grid-cols-3 gap-8 mb-4">
            <MonthPlan :isCurrentMonth="isCurrentMonth" :monthPlan="departmentPlans.monthPlan"
                :departmentId="departmentId" />
            <PercentLadder :isCurrentMonth="isCurrentMonth" :percentLadder="departmentPlans.percentLadder"
                :departmentId="departmentId" :propNoPercentageMonth="departmentPlans.noPercentageMonth" />

            <div class="flex flex-col gap-4">
                <UniversalPlan :departmentId="departmentId" :isCurrentMonth="isCurrentMonth" :title="'Бонус план'"
                    :planType="'bonusPlan'" :plans="departmentPlans.bonusPlan" :hasGoalField="true"
                    info="Сумма новых денег в текущем расчетном периоде больше или равна" />
                <UniversalPlan :departmentId="departmentId" :isCurrentMonth="isCurrentMonth" :title="'Двойной план'"
                    :planType="'doublePlan'" :plans="departmentPlans.doublePlan"
                    info="Сумма новых денег в текущем расчетном периоде больше или равна удвоенному плану" />
                <UniversalPlan :departmentId="departmentId" :isCurrentMonth="isCurrentMonth" :title="'План недели'"
                    :planType="'weekPlan'" :plans="departmentPlans.weekPlan" />
                <UniversalPlan :departmentId="departmentId" :isCurrentMonth="isCurrentMonth" :title="'Супер план'"
                    :planType="'superPlan'" :plans="departmentPlans.superPlan" :hasGoalField="true"
                    info="Сумма новых денег в текущем расчетном периоде больше или равна Цели или выполненны все планы недель" />
            </div>

            <B1Plan :propPlan="departmentPlans.b1Plan" :isCurrentMonth="isCurrentMonth" :departmentId="departmentId" />

            <B4Plan :propPlan="departmentPlans.b4Plan" :propServices="rkServices" :isCurrentMonth="isCurrentMonth"
                :departmentId="departmentId" />
            <div>

            </div>

            <B2Plan :services="services" :propPlan="departmentPlans.b2Plan" :propSeoServices="seoServices"
                :isCurrentMonth="isCurrentMonth" :departmentId="departmentId" />


            <div class=" col-span-2">
                <B3Plan :serviceCats="serviceCats" :services="services" :propPlan="departmentPlans.b3Plan"
                    :isCurrentMonth="isCurrentMonth" :departmentId="departmentId" />
            </div>
        </div>

        <div v-if="positionalPlans[activeTab]" class="flex flex-col gap-8 mb-4">
            <div class="grid grid-cols-3 gap-8">
                <ServicesPlan title="Б2 План" planType="b2Plan"
                    infoText="Менеджеру необходимо продать N указанных услуг"
                    :propPlan="positionalPlans[activeTab].b2Plan" :allServices="services"
                    :isCurrentMonth="isCurrentMonth" :departmentId="departmentId" :positionId="activeTab" />

                <ServicesPlan title="Б3 План" planType="b3Plan"
                    infoText="Менеджеру необходимо продать N указанных услуг"
                    :propPlan="positionalPlans[activeTab].b3Plan" :allServices="services"
                    :isCurrentMonth="isCurrentMonth" :departmentId="departmentId" :positionId="activeTab" />

                <ServicesPlan title="Б4 План" planType="b4Plan"
                    infoText="Менеджеру необходимо продать N указанных услуг"
                    :propPlan="positionalPlans[activeTab].b4Plan" :allServices="services"
                    :isCurrentMonth="isCurrentMonth" :departmentId="departmentId" :positionId="activeTab" />
            </div>

            <div class="w-1/2">
                <PercentLadder :isCurrentMonth="isCurrentMonth"
                    :percentLadder="positionalPlans[activeTab].percentLadder" :departmentId="departmentId"
                    :propNoPercentageMonth="positionalPlans[activeTab].noPercentageMonth" :positionId="activeTab" />
            </div>
        </div>

        <div v-if="activeTab == 'rop'" class="grid grid-cols-3 gap-8 mb-4">
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
                <UniversalPlan :departmentId="departmentId" :isCurrentMonth="isCurrentMonth"
                    :title="'Минимальный бонус'" :planType="'headMinimalBonus'" :plans="plans.headMinimalBonus"
                    :hasGoalField="false"
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
import ServicesPlan from './Settings/DepartmentSettings/ServicesPlan.vue';

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
        VueDatePicker,
        ServicesPlan
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
        positions: {
            type: Array,
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
            activeTab: null,
        }
    },
    computed: {
        departmentPlans() {
            const result = {};
            for (const key in this.plans) {
                if (Array.isArray(this.plans[key])) {
                    result[key] = this.plans[key].filter(p => !p.position_id);
                } else {
                    result[key] = this.plans[key];
                }
            }
            return result;
        },
        positionalPlans() {
            // Создаем пустой объект для группировки
            const grouped = {};

            // Перебираем все типы планов ('percentLadder', 'b2Plan' и т.д.)
            for (const [planType, plansArray] of Object.entries(this.plans)) {

                // Пропускаем, если это не массив планов
                if (!Array.isArray(plansArray)) continue;

                // Перебираем каждый отдельный план в массиве
                for (const plan of plansArray) {

                    // Если у плана есть position_id - это наш клиент
                    if (plan.position_id) {
                        const posId = plan.position_id;

                        // 1. Убеждаемся, что для этой должности есть "контейнер". Если нет - создаем.
                        if (!grouped[posId]) {
                            grouped[posId] = {};
                        }

                        // 2. Убеждаемся, что в контейнере есть "массив" для этого типа плана. Если нет - создаем.
                        if (!grouped[posId][planType]) {
                            grouped[posId][planType] = [];
                        }

                        // 3. Кладем план в его ячейку.
                        grouped[posId][planType].push(plan);
                    }
                }
            }

            return grouped;
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