<template>
    <div class="flex flex-col gap-3">
        <div class="text-2xl font-semibold mb-2">
            Б1 План
        </div>
        <form class="flex flex-col gap-4" @submit.prevent="submitForm(plan)">
            <label class="grid grid-cols-2 gap-2 items-center whitespace-nowrap" for="avgDurationCalls">
                Среднее время разговора
                <input v-model="plan.data.avgDurationCalls" class="input" name="avgDurationCalls" type="number"
                    :disabled="!isCurrentMonth" />
            </label>

            <label class="grid grid-cols-2 gap-2 items-center" for="avgCountCalls">
                Среднее кол-во звонков
                <input v-model="plan.data.avgCountCalls" class="input" name="avgCountCalls" type="number"
                    :disabled="!isCurrentMonth" />
            </label>

            <label class="grid grid-cols-2 gap-2 items-center" for="goal">
                План новыми
                <input v-model="plan.data.goal" class="input" name="goal" type="number" :disabled="!isCurrentMonth" />
            </label>

            <label class="grid grid-cols-2 gap-2 items-center" for="bonus">
                Бонус (%)
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
            'type': 'b1Plan',
        }
        let create = true
        if (this.propPlan && this.propPlan.length >= 1) {
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
            router.post(route('admin.sale-department.work-plan.store'), {
                'data': {
                    'avgDurationCalls': this.plan.data.avgDurationCalls,
                    'avgCountCalls': this.plan.data.avgCountCalls,
                    'goal': this.plan.data.goal,
                    'bonus': this.plan.data.bonus,
                },
                'type': 'b1Plan',
                'department_id': this.departmentId,
            }, {
                onSuccess() {
                    this.create = false
                },
            })
        },
        updatePlan() {
            router.put(route('admin.sale-department.work-plan.update', { workPlan: this.plan }), {
                'data': {
                    'avgDurationCalls': this.plan.data.avgDurationCalls,
                    'avgCountCalls': this.plan.data.avgCountCalls,
                    'goal': this.plan.data.goal,
                    'bonus': this.plan.data.bonus,
                },
                'type': 'b1Plan',
            })
        },
    }
}


</script>