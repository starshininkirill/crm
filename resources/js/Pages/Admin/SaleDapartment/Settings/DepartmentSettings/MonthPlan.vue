<template>
    <div class="flex flex-col gap-2 w-fit">
        <div class=" text-2xl font-semibold mb-2">
            План на месяц
        </div>
        <div class="flex flex-col gap-2 mb-4">
            <div v-if="!monthPlan || !Object.keys(monthPlan).length > 0" class=" text-xl mb-4">
                Нет планов
            </div>
            <div v-else v-for="plan in monthPlan" class="plan flex gap-4 border-b-2 py-1 pb-3 w-full items-center">
                <form class="flex gap-4">
                    <label class="flex gap-2 items-center" for="month">
                        Месяц
                        <input v-model="plan.data.month" :disabled="!isCurrentMonth" class="input" name="month"
                            type="number">
                    </label>
                    <label class="flex gap-2 items-center" for="goal">
                        Цель
                        <input v-model="plan.data.goal" :disabled="!isCurrentMonth" class="input" name="goal"
                            type="number">
                    </label>
                    <button v-if="isCurrentMonth" class="text-blue-400 hover:text-blue-500"
                        @click.prevent="updatePlan(plan)">
                        Изменить
                    </button>
                </form>
                <button v-if="isCurrentMonth" class="text-red-400 hover:text-red-500" @click.prevent="deletePlan(plan)">
                    Удалить
                </button>
            </div>
        </div>
        <div v-if="isCurrentMonth">
            <div class="text-xl mb-2">Новый план</div>
            <form @submit.prevent="createPlan" class="grid grid-cols-2 gap-3">
                <label class="flex gap-2 items-center">
                    Месяц
                    <input v-model="newPlan.data.month" class="input" name="month" type="number" required />
                </label>
                <label class="flex gap-2 items-center">
                    Цель
                    <input v-model="newPlan.data.goal" class="input" name="goal" type="number" required />
                </label>
                <button class="btn bg-blue-500 text-white px-4 py-2 mt-2 col-span-2">Создать</button>
            </form>
        </div>
    </div>

</template>
<script>
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';

export default {
    props: {
        monthPlan: {
            type: Object,
            required: true,
            default: () => ({})
        },
        isCurrentMonth: {
            type: Boolean
        },
        departmentId: {
            type: Number
        }
    },
    data() {
        return {
            newPlan: {
                data: {
                    month: null,
                    goal: null,
                },
                department_id: this.departmentId,
                type: 'monthPlan',
            },
        };
    },
    methods: {
        updatePlan(plan) {
            router.put(route('admin.sale-department.work-plan.update', { workPlan: plan }), {
                'data': {
                    'month': plan.data.month,
                    'goal': plan.data.goal
                },
                'type': plan.type,
            })
        },
        deletePlan(plan) {
            router.delete(route('admin.sale-department.work-plan.destroy', { workPlan: plan }))
        },
        createPlan() {
            router.post(route('admin.sale-department.work-plan.store'), {
                'data': {
                    'month': this.newPlan.data.month,
                    'goal': this.newPlan.data.goal,
                },
                'department_id': this.newPlan.department_id,
                'type': this.newPlan.type,
            }, {
                onSuccess: () => {
                    this.newPlan.data.month = '';
                    this.newPlan.data.goal = '';
                },
            })
        },
    }
}


</script>