<template>
    <div class="flex flex-col gap-3">
        <div class="text-2xl font-semibold mb-2">
            Б3 План
        </div>
        <form class="flex flex-col gap-2" @submit.prevent="submitForm(plan)">
            <div class="grid grid-cols-2 gap-3">
                <FormInput :disabled="!isCurrentMonth" v-model="plan.data.goal" type="number" name="goal"
                    placeholder="8" label="Цель" autocomplete="goal" required />
                <FormInput :disabled="!isCurrentMonth" v-model="plan.data.bonus" type="number" name="bonus"
                    placeholder="10000" label="Бонус" autocomplete="bonus" required />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <!-- Выбор услуг -->
                <ServiceSelector title="Выберите услуги, которые будут засчитываться в план"
                    :initial-services="includeIds.map(id => filtredServices.find(s => s.id === id))"
                    :all-options="filtredServices" :is-editable="isCurrentMonth"
                    @update:selected-services="updateIncludeIds" />
                <!-- Выбор категорий -->
                <ServiceCategoriesSelector title="Выберите категории для плана"
                    :initial-categories="includedCategoryIds.map(id => allCategories.find(c => c.id === id))"
                    :all-categories="allCategories" :is-editable="isCurrentMonth"
                    @update:selected-categories="updateIncludedCategoryIds" />
                <div class="col-span-2">
                    <!-- Выбор пар исключений -->
                    <ServicePairsSelector title="Выберите пары услуг, которые будут засчитываться в план"
                        :initial-pairs="formattedExcludeServicePairs" :all-options="services"
                        :is-editable="isCurrentMonth" @update:selected-pairs="handleSelectedPairsUpdate" />
                </div>
            </div>
            <button v-if="isCurrentMonth" class="btn" :class="isSaveButtonDisabled ? 'opacity-60 !cursor-default' : ''">
                Сохранить
            </button>
        </form>
    </div>
</template>

<script>
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';
import FormInput from '../../../../Components/FormInput.vue';
import ServiceSelector from './ServiceSelector.vue';
import ServiceCategoriesSelector from './ServiceCategoriesSelector.vue';
import ServicePairsSelector from './ServicePairsSelector.vue';

export default {
    components: {
        FormInput,
        ServiceSelector,
        ServiceCategoriesSelector,
        ServicePairsSelector
    },
    props: {
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
        serviceCats: {
            type: Array,
            required: true,
        },
    },
    data() {
        let plan = {
            data: {
                'bonus': null,
                'goal': null,
                'includeIds': [],
                'includedCategoryIds': [],
                'excludeServicePairs': [],
            },
            'type': 'b3Plan',
        };
        let create = true;
        if (this.propPlan && this.propPlan.length >= 1) {
            plan = this.propPlan[0];
            create = false;
        }
        return {
            filtredServices: this.services,
            allCategories: this.serviceCats,
            plan,
            create,
        };
    },
    computed: {
        includeIds() {
            return this.plan.data.includeIds || [];
        },
        includedCategoryIds() {
            return this.plan.data.includedCategoryIds || [];
        },
        formattedExcludeServicePairs() {
            return (this.plan.data.excludeServicePairs || []).map(pair => ({
                firstService: this.filtredServices.find(service => service.id === pair[0]) || null,
                secondService: this.filtredServices.find(service => service.id === pair[1]) || null,
            }));
        },
        isSaveButtonDisabled() {
            return this.includeIds.length === 0 || this.includedCategoryIds.length === 0;
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
                    'includedCategoryIds': this.plan.data.includedCategoryIds,
                    'excludeServicePairs': this.plan.data.excludeServicePairs,
                    'goal': this.plan.data.goal,
                    'bonus': this.plan.data.bonus,
                },
                'type': 'b3Plan',
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
                    'includedCategoryIds': this.plan.data.includedCategoryIds,
                    'excludeServicePairs': this.plan.data.excludeServicePairs,
                    'goal': this.plan.data.goal,
                    'bonus': this.plan.data.bonus,
                },
                'type': 'b3Plan',
            });
        },
        updateIncludeIds(ids) {
            this.plan.data.includeIds = ids;
        },
        updateIncludedCategoryIds(ids) {
            this.plan.data.includedCategoryIds = ids;
        },
        handleSelectedPairsUpdate(pairs) {
            this.plan.data.excludeServicePairs = pairs;
        },
    },
};
</script>