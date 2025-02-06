<template>

    <Head title="Неразобранные платежи (РС)" />
    <div class="contract-page-wrapper flex flex-col relative">
        <h1 class="text-4xl font-semibold mb-6">Платежи</h1>
        <div v-if="$page.props.errors && Object.keys($page.props.errors).length">
            <ul class="flex flex-col gap-1 mb-4">
                <li v-for="(messages, field) in $page.props.errors" :key="field">
                    <span v-for="message in messages" :key="message" class="text-xl text-red-400">{{ message }}</span>
                </li>
            </ul>
        </div>

        <div class="">
            <table v-if="payments.length" class="min-w-full border border-gray-300">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th v-for="header in headers" :key="header" class="border border-gray-300 px-4 py-2 text-left">
                            {{ header }}
                        </th>
                    </tr> 
                </thead>
                <tbody>
                    <PaymentRow v-for="(payment, index) in payments" :key="index" :payment="payment" />
                </tbody>
            </table>
            <h2 v-else>Платежей не найдено</h2>
        </div>
    </div>
</template>

<script>
import { Head, router } from '@inertiajs/vue3';
import PaymentLayout from '../Layouts/PaymentLayout.vue';
import FormInput from '../../../Components/FormInput.vue';
import PaymentRow from './Components/PaymentRow.vue';

export default {
    components: {
        Head,
        FormInput,
        PaymentRow
    },
    props: {
        payments: {
            type: Array
        },
        paymentStatuses: {
            type: Object
        },
    },
    layout: PaymentLayout,
    data() {
        return {
            headers: ['ИП', 'ID платежа', 'Сумма', 'Обоснование', 'ИНН', 'Дата', 'Прикрепить', 'Разделить']
        };
    }
}

</script>