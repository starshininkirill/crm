<template>
    <ProjectsLayout>

        <Head title="Настройка планов отдела сопровождения" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Настройка планов отдела сопровождения</h1>


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
                    <li v-for="plan in planComponents" :key="plan.key" class="mr-2">
                        <button @click="activeTab = plan.key"
                            :class="['inline-block p-4 border-b-2 rounded-t-lg', activeTab === plan.key ? 'text-blue-600 border-blue-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300']">
                            {{ plan.title }}
                        </button>
                    </li>
                </ul>
            </div>

            <div v-if="activePlan"
                :class="[activePlan.key === 'percent_ladder' ? '' : 'grid grid-cols-3 gap-8', 'mb-4']">
                <keep-alive>
                    <component :is="activePlan.component" v-bind="activePlan.props" />
                </keep-alive>
            </div>
        </div>
    </ProjectsLayout>
</template>

<script>
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import ProjectsLayout from '../Layouts/ProjectsLayout.vue';
import VueDatePicker from '@vuepic/vue-datepicker'
import IndividSettings from './Settings/DepartmentSettings/IndividSettings.vue';
import ReadySitesSettings from './Settings/DepartmentSettings/ReadySitesSettings.vue';
import Error from '../../../Components/Error.vue';
import UniversalPlan from './Components/UniversalPlan.vue';
import PercentLadder from './Components/PercentLadder.vue';
import B4Plan from './Components/B4Plan.vue';

export default {
    components: {
        Head,
        ProjectsLayout,
        VueDatePicker,
        IndividSettings,
        ReadySitesSettings,
        Error,
        UniversalPlan,
        PercentLadder,
        B4Plan,
    },
    props: {
        dateProp: {
            type: String,
            required: true,
        },
        serviceCats: {
            type: Array,
            required: true,
        },
        isCurrentMonth: {
            type: Boolean,
            required: true,
        },
        plans: {
            type: Object,
            required: true,
        },
        departmentId: {
            type: Number,
            required: true,
        },
    },
    data() {
        return {
            date: this.dateProp,
            activeTab: 'b1_plan',
        }
    },
    computed: {
        planComponents() {
            return [
                {
                    key: 'b1_plan',
                    title: 'Б1 План',
                    component: UniversalPlan,
                    props: {
                        departmentId: this.departmentId,
                        isCurrentMonth: this.isCurrentMonth,
                        title: 'Б1 План',
                        planType: 'b1Plan',
                        plans: this.plans.b1Plan,
                        hasGoalField: true,
                    }
                },
                {
                    key: 'b2_plan',
                    title: 'Б2 План',
                    component: UniversalPlan,
                    props: {
                        departmentId: this.departmentId,
                        isCurrentMonth: this.isCurrentMonth,
                        title: 'Б2 План',
                        planType: 'b2Plan',
                        plans: this.plans.b2Plan,
                        hasGoalField: true,
                    }
                },
                {
                    key: 'b3_plan',
                    title: 'Б3 План',
                    component: UniversalPlan,
                    props: {
                        departmentId: this.departmentId,
                        isCurrentMonth: this.isCurrentMonth,
                        title: 'Б3 План',
                        planType: 'b3Plan',
                        plans: this.plans.b3Plan,
                        hasGoalField: true,
                    }
                },
                {
                    key: 'b4_plan',
                    title: 'Б4 План',
                    component: B4Plan,
                    props: {
                        departmentId: this.departmentId,
                        isCurrentMonth: this.isCurrentMonth,
                        propPlan: this.plans.b4Plan,
                    }
                },
                {
                    key: 'percent_ladder',
                    title: 'Процентная лестница',
                    component: PercentLadder,
                    props: {
                        departmentId: this.departmentId,
                        isCurrentMonth: this.isCurrentMonth,
                        percentLadder: this.plans.percentLadder,
                    }
                },
                {
                    key: 'individ',
                    title: 'Индивидуальные',
                    component: IndividSettings,
                    props: {
                        departmentId: this.departmentId,
                        propPlan: this.plans.individCategoryIds,
                        allCategories: this.serviceCats,
                        isCurrentMonth: this.isCurrentMonth,
                        plans: this.plans,
                    }
                },
                {
                    key: 'ready_sites',
                    title: 'Готовые сайты',
                    component: ReadySitesSettings,
                    props: {
                        departmentId: this.departmentId,
                        propPlan: this.plans.readySyesCategoryIds,
                        allCategories: this.serviceCats,
                        isCurrentMonth: this.isCurrentMonth,
                        plans: this.plans,
                    }
                },
                {
                    key: 'up_sale_bonus',
                    title: 'Бонус за допродажу (%)',
                    component: UniversalPlan,
                    props: {
                        departmentId: this.departmentId,
                        isCurrentMonth: this.isCurrentMonth,
                        title: 'Бонус за допродажу (%)',
                        planType: 'upSaleBonus',
                        plans: this.plans.upSaleBonus,
                        hasGoalField: false,
                    }
                },
            ]
        },
        activePlan() {
            return this.planComponents.find(p => p.key === this.activeTab);
        }
    },
    methods: {
        changeDate() {
            router.get(route('admin.projects-department.plans-settings'), {
                date: this.date,
            })
        }
    }
}
</script>