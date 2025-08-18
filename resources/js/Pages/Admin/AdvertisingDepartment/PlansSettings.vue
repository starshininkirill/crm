<template>
    <AdvertisingDepartmentLayout>

        <Head title="Настройка планов Отдел Рекламы" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Настройка планов Отдел Рекламы</h1>

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

            <div v-if="activePlan" :class="[activePlan.key === 'percent_ladder' ? '' : 'mb-4']">
                <keep-alive>
                    <component class="max-w-2xl" :is="activePlan.component" v-bind="activePlan.props" />
                </keep-alive>
            </div>

        </div>
    </AdvertisingDepartmentLayout>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import AdvertisingDepartmentLayout from '../Layouts/AdvertisingDepartmentLayout.vue';
import Error from '../../../Components/Error.vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';
import BPlans from './Components/BPlans.vue';

export default {
    components: {
        Head,
        AdvertisingDepartmentLayout,
        Error,
        VueDatePicker,
        BPlans
    },
    props: {
        dateProp: {
            type: String,
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
            activeTab: 'b_plans',
        }
    },
    computed: {
        planComponents() {
            return [
                {
                    key: 'b_plans',
                    title: 'Планы сотрудников',
                    component: BPlans,
                    props: {
                        departmentId: this.departmentId,
                        isCurrentMonth: this.isCurrentMonth,
                        plans: this.plans,
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
            router.get(route('admin.advertising-department.plansSettings'), {
                date: this.date,
            })
        }
    }
}


</script>