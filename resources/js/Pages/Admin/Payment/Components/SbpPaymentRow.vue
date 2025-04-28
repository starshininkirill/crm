<template>
    <tr class="table-row">
        <td class="border px-4 py-2">{{ payment.organization?.short_name }}</td>
        <td class="border px-4 py-2">{{ payment.value }}</td>
        <td class="border px-4 py-2">{{ payment.description }}</td>
        <td class="border px-4 py-2">{{ payment.created_at }}</td>
        <td class="border px-4 py-2">
            <img :data-fancybox="`receipt-${payment.id}`" class=" cursor-pointer w-24 h-20 object-cover"
                v-if="payment?.receipt_url" :src="payment.receipt_url" alt="">
        </td>
        <td class="border px-4 py-2 text-blue-700 cursor-pointer" @click="toggleAttach">
            Прикрепить
        </td>
        <td class="border px-4 py-2">Разделить</td>
    </tr>
    <AttachPayment :withShortList="false" v-if="isActive" @close="handleClose" :payment="payment" />
</template>

<script>
import AttachPayment from './AttachPayment.vue';

export default {
    components: { AttachPayment },
    props: { payment: Object },
    data() {
        return { isActive: false };
    },
    methods: {
        toggleAttach() {
            this.isActive = !this.isActive;
        },
        handleClose() {
            console.log('handle');
            
            this.isActive = false
        }
    }
}
</script>