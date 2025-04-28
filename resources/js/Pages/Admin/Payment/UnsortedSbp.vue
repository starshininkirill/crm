<template>
    <PaymentLayout>

        <Head title="Неразобранные платежи (СБП)" />
        <div class="contract-page-wrapper flex flex-col relative">
            <h1 class="text-4xl font-semibold mb-6">Платежи</h1>

            <Error />

            <div class="">
                <table v-if="payments.length" class="shadow-md  overflow-hidden rounded-md sm:rounded-lg w-full text-sm text-left rtl:text-right text-gray-500">
                    <thead class="thead">
                        <tr>
                            <th v-for="header in headers" :key="header"
                                class="border px-4 py-2 text-left">
                                {{ header }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <SbpPaymentRow v-for="(payment, index) in payments" :key="index" :payment="payment" />
                    </tbody>
                </table>
                <h2 v-else>Платежей не найдено</h2>
            </div>
        </div>
    </PaymentLayout>
</template>

<script>
import { Head, router } from '@inertiajs/vue3';
import PaymentLayout from '../Layouts/PaymentLayout.vue';
import FormInput from '../../../Components/FormInput.vue';
import SbpPaymentRow from './Components/SbpPaymentRow.vue';
import { Fancybox } from '@fancyapps/ui';
import "@fancyapps/ui/dist/fancybox/fancybox.css";
import Error from '../../../Components/Error.vue';

export default {
    components: {
        Head,
        FormInput,
        SbpPaymentRow,
        Error,
        PaymentLayout
    },
    props: {
        payments: {
            type: Array
        },
        paymentStatuses: {
            type: Object
        },
    },
    data() {
        return {
            headers: ['ИП', 'Сумма', 'Описание', 'Дата', 'Чек', 'Прикрепить', 'Разделить']
        };
    },
    mounted() {
        Fancybox.bind("[data-fancybox]", {});
    },
}

</script>