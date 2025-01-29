<template>
    <tr>
        <td class="border px-4 py-2">{{ payment.organization?.short_name }}</td>
        <td class="border px-4 py-2">
            <Link :href="route('admin.payment.show', { payment: payment.id })" class="text-blue-700 underline">
            {{ payment.id }}
            </Link>
        </td>
        <td class="border px-4 py-2">{{ payment.value }}</td>
        <td class="border px-4 py-2">{{ payment.description }}</td>
        <td class="border px-4 py-2">{{ payment.inn }}</td>
        <td class="border px-4 py-2">{{ payment.created_at }}</td>
        <td class="border px-4 py-2 text-blue-700 cursor-pointer" @click="toggleAttach">
            Прикрепить
        </td>
        <td class="border px-4 py-2">Разделить</td>
    </tr>
    <AttachPayment v-if="isActive" :payment="payment" />
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
        }
    }
}
</script>