<template>
    <div class="flex flex-col max-w-2xl gap-8">
        <SimplePlan 
            title="Б1 План"
            :plans="plans.b1Plan"
            planType="b1Plan"
            :hasGoalField="true"
            :isCurrentMonth="isCurrentMonth"
            :departmentId="departmentId"
            info="Кол-во закрытых индивидов"
        />
        <SimplePlan 
            title="Б2 План"
            :plans="plans.b2Plan"
            planType="b2Plan"
            :hasGoalField="true"
            :isCurrentMonth="isCurrentMonth"
            :departmentId="departmentId"
            info="Кол-во закрытых Готовых сайтов"
        />
        <SimplePlan 
            title="Б3 План"
            :plans="plans.b3Plan"
            planType="b3Plan"
            :hasGoalField="true"
            :isCurrentMonth="isCurrentMonth"
            :departmentId="departmentId"
            info="Сумма заведённых ДЗ в текущем расчетном периоде"
        />
        <SimplePlan 
            title="Б4 План"
            :plans="plans.b4Plan"
            planType="b4Plan"
            :hasGoalField="true"
            :isCurrentMonth="isCurrentMonth"
            :departmentId="departmentId"
            info="Кол-во закрытых Комплексов"
        />
    </div>
</template>
<script>
import SimplePlan from './SimplePlan.vue';

export default {
    components: {
        SimplePlan,
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