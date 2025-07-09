<template>
    <div class="flex flex-col max-w-2xl gap-8">
        <SimplePlan 
            title="Б1 План"
            :plans="plans.headB1Plan"
            planType="headB1Plan"
            :hasGoalField="true"
            :isCurrentMonth="isCurrentMonth"
            :departmentId="departmentId"
        />
        <SimplePlan 
            title="Б2 План"
            :plans="plans.headB2Plan"
            planType="headB2Plan"
            :hasGoalField="true"
            :isCurrentMonth="isCurrentMonth"
            :departmentId="departmentId"
        />
        <B3Plan 
            title="Б3 План"
            :plans="plans.headB3Plan"
            planType="headB3Plan"
            :hasGoalField="true"
            :isCurrentMonth="isCurrentMonth"
            :departmentId="departmentId"
        />
        <SimplePlan 
            title="Б4 План"
            :plans="plans.headB4Plan"
            planType="headB4Plan"
            :hasGoalField="true"
            :isCurrentMonth="isCurrentMonth"
            :departmentId="departmentId"
        />
        <SimplePlan 
            title="План допродаж"
            :plans="plans.headUpsalePlan"
            planType="headUpsalePlan"
            :hasGoalField="true"
            :isCurrentMonth="isCurrentMonth"
            :departmentId="departmentId"
        />
    </div>
</template>
<script>
import SimplePlan from '../../Components/SimplePlan.vue';
import B3Plan from './B3Plan.vue';

export default {
    components: {
        SimplePlan,
        B3Plan
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