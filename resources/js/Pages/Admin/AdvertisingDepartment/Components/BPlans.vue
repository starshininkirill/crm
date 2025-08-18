<template>
    <div class="flex flex-col max-w-2xl gap-8">
        <SimplePlan 
            title="Б1 План"
            :plans="plans.b1Plan"
            planType="b1Plan"
            :hasGoalField="true"
            :isCurrentMonth="isCurrentMonth"
            :departmentId="departmentId"
            info="Перевести указанное кол-во клиентов на ТП"
        />
        <SimplePlan 
            title="Б2 План"
            :plans="plans.b2Plan"
            planType="b2Plan"
            :hasGoalField="true"
            :isCurrentMonth="isCurrentMonth"
            :departmentId="departmentId"
            info="Нужно из всех пришедших настроек, завести в план следующего месяца указанный % сделок."
        />
        <B3Plan
            title="Б3 План"
            :plans="plans.b3Plan"
            planType="b3Plan"
            :isCurrentMonth="isCurrentMonth"
            :departmentId="departmentId"
            info="Описание"
         />
        <SimplePlan 
            title="Бонус за допродажу (%)"
            :plans="plans.upSaleBonus"
            planType="upSaleBonus"
            :hasGoalField="false"
            :isCurrentMonth="isCurrentMonth"
            :departmentId="departmentId"
            info="% который получит сотрудник с допродаж"
        />
    </div>
</template>
<script>
import SimplePlan from './SimplePlan.vue';
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