<template>
    <div class="flex flex-col gap-3">
        <div class="text-2xl font-semibold mb-2">
            Б4 План
        </div>
        <form class="flex flex-col gap-2" @submit.prevent="submitForm(plan)">
            <div class="grid grid-cols-2 gap-3">
                <FormInput :disabled="!isCurrentMonth" v-model="plan.data.goal" type="number" name="goal"
                    placeholder="8" label="Цель" autocomplete="goal" required />
                <FormInput :disabled="!isCurrentMonth" v-model="plan.data.bonus" type="number" name="bonus"
                    placeholder="10000" label="Бонус" autocomplete="bonus" required />
            </div>
            <!-- Используем ServiceSelector для выбора услуг -->
            <ServiceSelector
                title="Выберите услуги, которые будут засчитываться в план"
                :initial-services="includeIds.map(id => filtredServices.find(s => s.id === id))"
                :all-options="filtredServices"
                :is-editable="isCurrentMonth"
                @update:selected-services="updateIncludeIds"
            />
            <button v-if="isCurrentMonth" class="btn" :class="isSaveButtonDisabled ? 'opacity-60 !cursor-default' : ''">
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

export default {
    components: {
        FormInput,
        ServiceSelector,
    },
    props: {
        propServices: {
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
    },
    data() {
        let plan = {
            data: {
                'bonus': null,
                'goal': null,
            },
            'type': 'b4Plan',
        };
        let create = true;

        if (this.propPlan && this.propPlan.length >= 1) {
            plan = this.propPlan[0];
            create = false;
        }

        return {
            filtredServices: this.propServices,
            plan,
            create,
        };
    },
    computed: {
        includeIds() {
            return this.plan.data.includeIds || [];
        },
        isSaveButtonDisabled() {
            return this.includeIds.length === 0 || this.isAddButtonDisabled;
        },
        isAddButtonDisabled() {
            return this.includeIds.some(id => !id);
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
                    'goal': this.plan.data.goal,
                    'bonus': this.plan.data.bonus,
                },
                'type': 'b4Plan',
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
                    'goal': this.plan.data.goal,
                    'bonus': this.plan.data.bonus,
                },
                'type': 'b4Plan',
            });
        },
        updateIncludeIds(ids) {
            this.plan.data.includeIds = ids
        },
    },
};
</script>