<template>
    <div class="flex flex-col gap-3">
        <div class="text-2xl font-semibold mb-2 flex items-center gap-2">
            {{ title }}
            <Info v-if="info" :text="info" />
        </div>
        <p v-if="plansCount > 1" class="text-red-400">
            Кол-во планов не должно быть больше 1!<br />
            Возможны ошибки в расчётах, удалите ненужные планы.
        </p>
        <div v-for="plan in plans" class="flex gap-4 border-b-2 py-1 pb-3 w-full items-center">
            <form class="flex gap-4" @submit.prevent="updatePlan(plan)">

                <label class="flex flex-col gap-2" for="goal">
                    Цель (%)
                    <input v-model="plan.data.goal" class="input" name="goal" type="number"
                        :disabled="!isCurrentMonth" />
                </label>

                <label class="flex flex-col gap-2" for="goal">
                    Цель повышенная (%)
                    <input v-model="plan.data.max_goal" class="input" name="goal" type="number"
                        :disabled="!isCurrentMonth" />
                </label>

                <label class="flex flex-col gap-2" for="goal">
                    Минимальное количество клиентов
                    <input v-model="plan.data.goal" class="input" name="goal" type="number"
                        :disabled="!isCurrentMonth" />
                </label>

                <label class="flex flex-col gap-2" for="goal">
                    Порог для повышенного %
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
        <form v-if="isCurrentMonth && plansCount == 0" class="flex flex-col gap-4 border-b-2 py-1 pb-3"
            @submit.prevent="createPlan">

            <label class="flex flex-col gap-2 whitespace-nowrap" for="goal">
                Цель минимум (%)
                <input v-model="newPlan.data.goal" class="input" name="goal" type="number"
                    :disabled="!isCurrentMonth" />
            </label>

            <label class="flex flex-col gap-2 whitespace-nowrap" for="goal">
                Цель повышенная (%)
                <input v-model="newPlan.data.max_goal" class="input" name="goal" type="number"
                    :disabled="!isCurrentMonth" />
            </label>

            <label class="flex flex-col gap-2 whitespace-nowrap" for="goal">
                Минимальное количество клиентов
                <input v-model="newPlan.data.goal" class="input" name="goal" type="number"
                    :disabled="!isCurrentMonth" />
            </label>

            <label class="flex flex-col gap-2" for="goal">
                Порог для повышенного %
                <input v-model="newPlan.data.goal" class="input" name="goal" type="number" :disabled="!isCurrentMonth" />
            </label>

            <label class="flex flex-col gap-2" for="bonus">
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
import Info from '../../../../Components/Info.vue';

export default {
    components: {
        Info
    },
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
        isCurrentMonth: {
            type: Boolean,
            default: false
        },
        departmentId: {
            type: Number,
            required: true,
        },
        info: {
            type: String,
            required: false
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

            router.put(route('admin.advertising-department.work-plan.update', { workPlan: plan }), {
                'data': data,
                'type': plan.type
            })
        },
        deletePlan(plan) {
            router.delete(route('admin.advertising-department.work-plan.destroy', { workPlan: plan }))
        },
        createPlan() {
            let data = {};
            if (this.hasGoalField) {
                data.goal = this.newPlan.data.goal;
            }
            data.bonus = this.newPlan.data.bonus;

            router.post(route('admin.advertising-department.work-plan.store'), {
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