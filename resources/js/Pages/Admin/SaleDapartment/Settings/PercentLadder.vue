<template>
    <div>
        <div class="flex flex-col gap-2 w-fit">
            <div class=" text-2xl font-semibold mb-4">
                Процентная лестница
            </div>
        </div>
        <div v-if="percentLadder && percentLadder.length == 0" class=" text-xl mb-4">
            Нет данных
        </div>
        <div v-else class="flex flex-col gap-2">
            <div v-for="plan in percentLadder" class="plan flex gap-4 border-b-2 py-1 pb-3 w-full items-center">
                <form class="flex gap-4">
                    <label class="flex gap-2 items-center" for="goal">
                        До
                        <input v-model="plan.data.goal" :disabled="!isCurrentMonth" class="input" name="goal"
                            type="number">
                    </label>
                    <label class="flex gap-2 items-center" for="bonus">
                        %
                        <input v-model="plan.data.bonus" :disabled="!isCurrentMonth" class="input" name="bonus"
                            type="number" step="0.1">
                    </label>
                    <button v-if="isCurrentMonth" class=" text-blue-400 hover:text-blue-500"
                        @click.prevent="updatePlan(plan)">
                        Изменить
                    </button>
                    <button v-if="isCurrentMonth" class="text-red-400 hover:text-red-500"
                        @click.prevent="deletePlan(plan)">
                        Удалить
                    </button>
                </form>
            </div>
        </div>
        <form class="my-4" v-if="isCurrentMonth" @submit.prevent="createPlan(newPlan)">
            <div class=" text-xl mb-2">
                Новая цель
            </div>
            <div class="grid grid-cols-2 gap-2">
                <label class="flex gap-2 items-center" for="goal">
                    До
                    <input v-model="newPlan.data.goal" class="input" name="goal" type="number">
                </label>
                <label class="flex gap-2 items-center" for="bonus">
                    %
                    <input v-model="newPlan.data.bonus" class="input" name="bonus" type="number" step="0.1">
                </label>
                <button class="btn col-span-2">
                    Создать
                </button>
            </div>
        </form>

        <form @submit.prevent="submitForm(noPercentageMonth)">
            <div class=" text-xl mb-2">
                До какого месяца всегда начисляются проценты
            </div>
            <div class="flex flex-col gap-2">
                <label class="flex gap-2 items-center" for="month">
                    <input :disabled="!isCurrentMonth" v-model="noPercentageMonth.data.goal" class="input" name="month"
                        type="number" step="1" min="0">
                </label>
                <button v-if="isCurrentMonth" class="btn">
                    {{ create ? 'Создать' : 'Изменить' }}
                </button>
            </div>
        </form>
    </div>
</template>
<script>
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';

export default {
    props: {
        percentLadder: {
            type: Array,
            required: true,
            default: () => ([])
        },
        isCurrentMonth: {
            type: Boolean
        },
        propNoPercentageMonth: {
            type: Object,
        },
        departmentId: {
            type: Number
        }
    },
    data() {
        let noPercentageMonth = {
            data: {},
            'type': 'noPercentageMonth',
        }
        let create = true

        if (this.propNoPercentageMonth && this.propNoPercentageMonth.length >= 1) {
            noPercentageMonth = this.propNoPercentageMonth[0];
            create = false
        }

        return {
            newPlan: {
                data: {
                    bonus: null,
                    goal: null,
                },
                department_id: this.departmentId,
                type: 'percentLadder',
            },
            noPercentageMonth,
            create
        };
    },
    methods: {
        submitForm() {
            if (this.create) {
                this.createPlan(this.noPercentageMonth)
            } else {
                this.updatePlan(this.noPercentageMonth)
            }
        },
        updatePlan(plan) {
            router.put(route('admin.sale-department.work-plan.update', { workPlan: plan }), {
                'data': {
                    'bonus': plan.data.bonus,
                    'goal': plan.data.goal
                },
                'type': plan.type,
            })
        },
        deletePlan(plan) {
            router.delete(route('admin.sale-department.work-plan.destroy', { workPlan: plan }))
        },
        createPlan(newPlan) {
            router.post(route('admin.sale-department.work-plan.store'), {
                'data': {
                    'bonus': newPlan.data.bonus,
                    'goal': newPlan.data.goal,
                },
                'department_id': newPlan.department_id,
                'type': newPlan.type,
            }, {
                onSuccess: () => {
                    newPlan.data.bonus = '';
                    newPlan.data.goal = '';
                },
            })
        },
    }
}


</script>