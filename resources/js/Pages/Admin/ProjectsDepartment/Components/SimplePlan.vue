<template>
    <div class="flex flex-col gap-3">
        <div class="text-2xl font-semibold mb-2">
            {{ title }}
        </div>
        <p v-if="plansCount > 1" class="text-red-400">
            Кол-во планов не должно быть больше 1!<br />
            Возможны ошибки в расчётах, удалите ненужные планы.
        </p>
        <div v-for="plan in plans" class="flex gap-4 border-b-2 py-1 pb-3 w-full items-center">
            <form class="flex gap-4" @submit.prevent="updatePlan(plan)">
                <label v-if="hasGoalField" class="flex gap-2 items-center whitespace-nowrap" for="goal">
                    Цель
                    <input v-model="plan.data.goal" class="input" name="goal" type="number"
                        :disabled="!isCurrentMonth" />
                </label>

                <label class="flex gap-2 items-center" for="bonus">
                    Бонус
                    <input v-model="plan.data.bonus" class="input" name="bonus" type="number" step="0.1"
                        :disabled="!isCurrentMonth" />
                </label>

                <button v-if="isCurrentMonth" class="text-blue-400 hover:text-blue-500" type="submit">
                    Изменить
                </button>
            </form>

            <form v-if="isCurrentMonth" @submit.prevent="deletePlan(plan)">
                <button class="text-red-400 hover:text-red-500" type="submit">
                    Удалить
                </button>
            </form>
        </div>
        <form v-if="isCurrentMonth && plansCount == 0" class="flex gap-4 border-b-2 py-1 pb-3"
            @submit.prevent="createPlan">

            <label v-if="hasGoalField" class="flex gap-2 items-center whitespace-nowrap" for="goal">
                Цель
                <input v-model="newPlan.data.goal" class="input" name="goal" type="number" />
            </label>

            <label class="flex gap-2 items-center" for="bonus">
                Бонус
                <input v-model="newPlan.data.bonus" class="input" name="bonus" type="number" />
            </label>
            <button class="text-blue-400 hover:text-blue-500" type="submit">
                Создать
            </button>
        </form>
    </div>
</template>
<script>
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';

export default {
    props: {
        title: {
            type: String,
            required: true,
        },
        plans: {
            type: Object
        },
        planType: {
            type: String,
            required: true,
        },
        hasGoalField: {
            type: Boolean,
            default: false,
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
        return {
            newPlan: {
                data: {
                    goal: null,
                    bonus: null,
                },
                department_id: this.departmentId,
                type: this.planType,
            },
        }
    },
    computed: {
        plansCount() {
            if (this.plans == undefined) {
                return 0
            }
            return this.plans.length;
        },
    },
    methods: {
        updatePlan(plan) {
            let data = {};
            if (this.hasGoalField) {
                data.goal = plan.data.goal;
            }
            data.bonus = plan.data.bonus;

            router.put(route('admin.projects-department.work-plan.update', { workPlan: plan }), {
                'data': data,
                'type': plan.type
            })
        },
        deletePlan(plan) {
            router.delete(route('admin.projects-department.work-plan.destroy', { workPlan: plan }))
        },
        createPlan() {
            let data = {};
            if (this.hasGoalField) {
                data.goal = this.newPlan.data.goal;
            }
            data.bonus = this.newPlan.data.bonus;

            router.post(route('admin.projects-department.work-plan.store'), {
                'data': data,
                'department_id': this.newPlan.department_id,
                'type': this.newPlan.type,
            }, {
                onSuccess: () => {
                    this.newPlan.data.goal = null;
                    this.newPlan.data.bonus = null;
                },
            })
        },
    },
}


</script> 