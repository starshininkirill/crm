<template>
    <div class="flex flex-col gap-3">
        <div class="text-2xl font-semibold mb-2">
            Б2 План
        </div>
        <form class="flex flex-col gap-2" @submit.prevent="submitForm(plan)">
            <div class="grid grid-cols-2 gap-3">
                <FormInput :disabled="!isCurrentMonth" v-model="plan.data.goal" type="number" name="goal"
                    placeholder="8" label="Цель" autocomplete="goal" required />
                <FormInput :disabled="!isCurrentMonth" v-model="plan.data.bonus" type="number" name="bonus"
                    placeholder="10000" label="Бонус" autocomplete="bonus" required />
            </div>

            <!-- Включаемые услуги -->
            <ServiceSelector title="Выберите услуги, которые будут засчитываться в план"
                :initial-services="includeIds.map(id => filtredSeoServices.find(s => s.id === id))"
                :all-options="filtredSeoServices" :is-editable="isCurrentMonth"
                @update:selected-services="updateIncludeIds" />

            <!-- Исключаемые услуги -->
            <ServiceSelector title="Выберите услуги, которые не будут засчитываться в план"
                :initial-services="excludeIds.map(id => filtredServices.find(s => s.id === id))"
                :all-options="filtredServices" :is-editable="isCurrentMonth"
                @update:selected-services="updateExcludeIds" />
                
            <button v-if="isCurrentMonth" class="btn" :class="isSaveButtonDisabled ? 'opacity-60 !cursor-default' : ''">
                Сохранить
            </button>
        </form>
    </div>
</template>

<script>
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';
import VueSelect from 'vue-select';
import FormInput from '../../../../Components/FormInput.vue';
import ServiceSelector from './ServiceSelector.vue';

export default {
    components: {
        VueSelect,
        FormInput,
        ServiceSelector,
    },
    props: {
        propSeoServices: {
            type: Array,
            required: true,
        },
        departmentId: {
            type: Number,
            required: true,
        },
        isCurrentMonth: {
            type: Boolean,
            default: false,
        },
        propPlan: {
            type: Object,
        },
        services: {
            type: Array,
            required: true,
        },
    },
    data() {
        let plan = {
            data: {
                'bonus': null,
                'goal': null,
            },
            'type': 'b2Plan',
        };
        let create = true;

        if (this.propPlan && this.propPlan.length >= 1) {
            plan = this.propPlan[0];
            create = false;
        }

        return {
            filtredSeoServices: this.propSeoServices,
            filtredServices: this.services,
            plan,
            create,
        };
    },
    computed: {
        includeIds() {
            return this.plan.data.includeIds || [];
        },
        excludeIds() {
            return this.plan.data.excludeIds || [];
        },
        isSaveButtonDisabled() {
            return this.includeIds.length === 0;
        },
    },
    methods: {
        submitForm() {
            if (this.isSaveButtonDisabled) return;

            if (this.create) {
                this.createPlan();
            } else {
                this.updatePlan();
            }
        },
        createPlan() {
            router.post(route('admin.sale-department.work-plan.store'), {
                'data': {
                    'includeIds': this.plan.data.includeIds,
                    'excludeIds': this.plan.data.excludeIds,
                    'goal': this.plan.data.goal,
                    'bonus': this.plan.data.bonus,
                },
                'type': 'b2Plan',
                'department_id': this.departmentId,
            }, {
                onSuccess() {
                    this.create = false;
                },
            });
        },
        updatePlan() {

            router.put(route('admin.sale-department.work-plan.update', { workPlan: this.plan }), {
                'data': {
                    'includeIds': this.plan.data.includeIds,
                    'excludeIds': this.plan.data.excludeIds,
                    'goal': this.plan.data.goal,
                    'bonus': this.plan.data.bonus,
                },
                'type': 'b2Plan',
            });
        },
        updateIncludeIds(ids) {
            this.plan.data.includeIds = ids
        },
        updateExcludeIds(ids) {
            this.plan.data.excludeIds = ids
        },
    },
};
</script>