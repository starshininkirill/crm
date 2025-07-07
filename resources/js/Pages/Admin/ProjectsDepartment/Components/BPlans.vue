<template>
    <div class="flex flex-col max-w-2xl gap-8">
        <SimplePlan 
            title="Б1 План"
            :plans="plans.b1Plan"
            planType="b1Plan"
            :hasGoalField="true"
            :isCurrentMonth="isCurrentMonth"
            :departmentId="departmentId"
        />
        <SimplePlan 
            title="Б2 План"
            :plans="plans.b2Plan"
            planType="b2Plan"
            :hasGoalField="true"
            :isCurrentMonth="isCurrentMonth"
            :departmentId="departmentId"
        />
        <SimplePlan 
            title="Б3 План"
            :plans="plans.b3Plan"
            planType="b3Plan"
            :hasGoalField="true"
            :isCurrentMonth="isCurrentMonth"
            :departmentId="departmentId"
        />
        <B4PlanSettings 
            :propPlan="plans.b4Plan"
            :isCurrentMonth="isCurrentMonth"
            :departmentId="departmentId"
        />
    </div>
</template>
<script>
import SimplePlan from './SimplePlan.vue';
import B4PlanSettings from './B4PlanSettings.vue';

export default {
    components: {
        SimplePlan,
        B4PlanSettings,
    },
    props: {
        plans: {
            type: Object,
            required: true,
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
}
</script> 