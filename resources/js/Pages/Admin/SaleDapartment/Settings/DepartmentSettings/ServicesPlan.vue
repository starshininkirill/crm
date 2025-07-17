<template>
    <div class="flex flex-col gap-3">
        <div class="text-2xl font-semibold mb-2 flex items-center gap-2">
            {{ title }}
            <Info v-if="infoText" :text="infoText" />
        </div>
        <form v-if="plan" class="flex flex-col gap-2" @submit.prevent="submitForm">
            <div class="grid grid-cols-2 gap-3">
                <FormInput :disabled="!isCurrentMonth" v-model="plan.data.goal" type="number" name="goal"
                    placeholder="Цель" label="Цель" autocomplete="goal" required />
                <FormInput :disabled="!isCurrentMonth" v-model="plan.data.bonus" type="number" name="bonus"
                    placeholder="Бонус" label="Бонус" autocomplete="bonus" required />
            </div>

            <ServiceSelector title="Выберите услуги, которые будут засчитываться в план"
                :initial-services="includeIds.map(id => allServices.find(s => s.id === id)).filter(Boolean)"
                :all-options="allServices" :is-editable="isCurrentMonth"
                @update:selected-services="updateIncludeIds" />

            <button v-if="isCurrentMonth" class="btn" :class="isSaveButtonDisabled ? 'opacity-60 !cursor-default' : ''" :disabled="isSaveButtonDisabled">
                Сохранить
            </button>
        </form>
    </div>
</template>

<script>
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';
import FormInput from '../../../../../Components/FormInput.vue';
import ServiceSelector from './ServiceSelector.vue';
import Info from '../../../../../Components/Info.vue';

export default {
    components: {
        FormInput,
        ServiceSelector,
        Info
    },
    props: {
        title: {
            type: String,
            required: true,
        },
        planType: {
            type: String,
            required: true,
        },
        infoText: String,
        departmentId: {
            type: Number,
            required: true,
        },
        positionId: {
            type: [Number, String],
            default: null,
        },
        isCurrentMonth: {
            type: Boolean,
            default: false,
        },
        propPlan: {
            type: Array,
        },
        allServices: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            plan: null,
            create: true,
        };
    },
    computed: {
        includeIds() {
            return this.plan?.data?.includeIds || [];
        },
        isSaveButtonDisabled() {
            return !this.plan?.data?.goal || !this.plan?.data?.bonus || this.includeIds.length === 0;
        },
    },
    methods: {
        initializePlan(planArray) {
            if (planArray && planArray.length > 0) {
                this.plan = JSON.parse(JSON.stringify(planArray[0]));
                if (!this.plan.data) {
                    this.plan.data = {};
                }
                this.create = false;
            } else {
                this.plan = {
                    data: {
                        'bonus': null,
                        'goal': null,
                        'includeIds': [],
                    },
                    'type': this.planType,
                };
                this.create = true;
            }
        },
        submitForm() {
            if (this.isSaveButtonDisabled) return;

            if (this.create) {
                this.createPlan();
            } else {
                this.updatePlan();
            }
        },
        createPlan() {
            const payload = {
                'data': {
                    'includeIds': this.plan.data.includeIds,
                    'goal': this.plan.data.goal,
                    'bonus': this.plan.data.bonus,
                },
                'type': this.planType,
                'department_id': this.departmentId,
            };

            if (this.positionId) {
                payload.position_id = this.positionId;
            }

            router.post(route('admin.sale-department.work-plan.store'), payload, {
                preserveScroll: true,
                onSuccess: () => {
                    router.reload({ only: ['plans'], preserveScroll: true });
                },
            });
        },
        updatePlan() {
            const payload = {
                'data': {
                    'includeIds': this.plan.data.includeIds,
                    'goal': this.plan.data.goal,
                    'bonus': this.plan.data.bonus,
                },
                'type': this.planType,
            };
            router.put(route('admin.sale-department.work-plan.update', { workPlan: this.plan.id }), payload, {
                preserveScroll: true,
            });
        },
        updateIncludeIds(ids) {
            if (this.plan && this.plan.data) {
                this.plan.data.includeIds = ids;
            }
        },
    },
    created() {
        this.initializePlan(this.propPlan);
    },
    watch: {
        propPlan: {
            handler(newPlanArray) {
                this.initializePlan(newPlanArray);
            },
            deep: true
        }
    }
};
</script> 