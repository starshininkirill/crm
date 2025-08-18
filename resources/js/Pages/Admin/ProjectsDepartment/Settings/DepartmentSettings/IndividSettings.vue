<template>
    <div class="flex flex-col gap-3">
        <div class="text-2xl font-semibold mb-2">
            Категории идущие в индивид
        </div>
        <form class="flex flex-col gap-2" @submit.prevent="submitForm(plan)">

            <ServiceCategoriesSelector
                :initial-categories="includedCategoryIds.map(id => allCategories.find(c => c.id === id))"
                :all-categories="allCategories" :is-editable="isCurrentMonth"
                @update:selected-categories="updateIncludedCategoryIds" />

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
import ServiceCategoriesSelector from '../../../../../Components/PlanSettings/ServiceCategoriesSelector.vue';

export default {
    components: {
        FormInput,
        ServiceCategoriesSelector,
    },
    props: {
        allCategories: {
            type: Array,
            required: true,
        },
        isCurrentMonth: {
            type: Boolean,
            required: true,
        },
        propPlan: {
            type: Object,
        },
        departmentId: {
            type: Number,
            required: true,
        },
    },
    data() {
        let plan = {
            data: {
                'categoryIds': [],
            },
            'type': 'individCategoryIds',
        };
        let create = true;
        if (this.propPlan && this.propPlan.length >= 1) {
            plan = this.propPlan[0];
            create = false;
        }

        return {
            plan,
            create,
        };
    },
    methods: {
        updateIncludedCategoryIds(ids) {
            this.plan.data.categoryIds = ids;
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
            router.post(route('admin.projects-department.work-plan.store'), {
                'data': {
                    'categoryIds': this.plan.data.categoryIds,
                },
                'type': 'individCategoryIds',
                'department_id': this.departmentId,
            }, {
                onSuccess() {
                    this.create = false;
                },
            });
        },
        updatePlan() {
            router.put(route('admin.projects-department.work-plan.update', { workPlan: this.plan }), {
                'data': {
                    'categoryIds': this.plan.data.categoryIds,
                },
                'type': 'individCategoryIds',
                'department_id': this.departmentId,
            });
        },
    },
    computed: {
        includedCategoryIds() {
            return this.plan.data.categoryIds || [];
        },
        isSaveButtonDisabled() {
            return this.includedCategoryIds.length === 0;
        },
    },
};
</script>