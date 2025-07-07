<template>
    <div class="flex flex-col gap-3">
        <div class="text-2xl font-semibold mb-2">
            Б4 План
        </div>
        <form class="flex flex-col gap-4" @submit.prevent="submitForm">
            <label class="grid grid-cols-2 gap-2 items-center whitespace-nowrap" for="projects">
                Кол-во проектов
                <input v-model="plan.data.projects" class="input" name="projects" type="number"
                    :disabled="!isCurrentMonth" />
            </label>

            <label class="grid grid-cols-2 gap-2 items-center" for="complexes">
                Кол-во комплексов
                <input v-model="plan.data.complexes" class="input" name="complexes" type="number"
                    :disabled="!isCurrentMonth" />
            </label>

            <label class="grid grid-cols-2 gap-2 items-center" for="bonus">
                Бонус
                <input v-model="plan.data.bonus" class="input" name="bonus" type="number" :disabled="!isCurrentMonth" />
            </label>

            <button v-if="isCurrentMonth" class="text-blue-400 hover:text-blue-500" type="submit">
                {{ create ? 'Создать' : 'Изменить' }}
            </button>
        </form>
    </div>
</template>
<script>
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';

export default {
    props: {
        propPlan: {
            type: Object
        },
        isCurrentMonth: {
            type: Boolean,
            default: false
        },
        departmentId: {
            type: Number,
            required: true,
        }
    },
    data() {
        let plan = {
            data: {},
            'type': 'b4Plan',
        }
        let create = true
        if (this.propPlan && this.propPlan.length > 0) {
            plan = this.propPlan[0];
            create = false
        }
        

        return {
            plan,
            create
        }
    },
    methods: {
        submitForm() {
            if (this.create) {
                this.createPlan()
            } else {
                this.updatePlan()
            }
        },
        createPlan() {
            router.post(route('admin.projects-department.work-plan.store'), {
                'data': {
                    'projects': this.plan.data.projects,
                    'complexes': this.plan.data.complexes,
                    'bonus': this.plan.data.bonus,
                },
                'type': 'b4Plan',
                'department_id': this.departmentId,
            }, {
                onSuccess: () => {
                    this.create = false
                },
            })
        },
        updatePlan() {
            router.put(route('admin.projects-department.work-plan.update', { workPlan: this.plan.id }), {
                'data': {
                    'projects': this.plan.data.projects,
                    'complexes': this.plan.data.complexes,
                    'bonus': this.plan.data.bonus,
                },
                'type': 'b4Plan',
            })
        },
    }
}
</script> 