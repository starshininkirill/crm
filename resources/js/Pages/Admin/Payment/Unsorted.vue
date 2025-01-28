<template>

    <Head title="Неразобранные платежи (РС)" />
    <div class="contract-page-wrapper flex flex-col">
        <h1 class="text-4xl font-semibold mb-6">Платежи</h1>
        <div class="">
            <h2 v-if="!payments.length">Платежей не найдено</h2>

            <table v-else class="min-w-full border border-gray-300">
                <thead>
                    <tr class="bg-gray-800">
                        <th class="border border-gray-300 px-4 py-2 text-left text-white">ИП</th>
                        <th class="border border-gray-300 px-4 py-2 text-left text-white">Номер</th>
                        <th class="border border-gray-300 px-4 py-2 text-left text-white">Сумма</th>
                        <th class="border border-gray-300 px-4 py-2 text-left text-white">Обоснование</th>
                        <th class="border border-gray-300 px-4 py-2 text-left text-white">ИНН</th>
                        <th class="border border-gray-300 px-4 py-2 text-left text-white">Дата</th>
                        <th class="border border-gray-300 px-4 py-2 text-left text-white">Прикрепить</th>
                        <th class="border border-gray-300 px-4 py-2 text-left text-white">Разделить</th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="(payment, index) in payments" :key="index">
                        <tr>
                            <td class="border border-gray-300 px-4 py-2">
                                <template v-if="payment.organization">
                                    {{ payment.organization.short_name }}
                                </template>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                <Link :href="route('admin.payment.show', { payment: payment.id })"
                                    class="text-blue-700 underline">
                                № {{ payment.id }}
                                </Link>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ payment.value }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                Описание
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ payment.inn }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ payment.created_at }}
                            </td>
                            <td @click="toggleAttachMenu(index, payment.id)"
                                class="border border-gray-300 px-4 py-2 cursor-pointer">
                                Прикрепить
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                Разделить
                            </td>
                        </tr>

                        <tr v-if="isRowActive(index)">
                            <td class="border border-gray-300 px-4 py-2" colspan="8">
                                <div class="text-xl mb-3">Прикрепить платёж</div>
                                <div class="text-xl mb-3">Шортлист</div>
                                <table class="w-full max-w-2xl border border-gray-300">
                                    <thead>
                                        <tr class="bg-gray-800">
                                            <th class="border border-gray-300 px-4 py-2 text-left text-white">Сделка
                                            </th>
                                            <th class="border border-gray-300 px-4 py-2 text-left text-white">Сумма</th>
                                            <th class="border border-gray-300 px-4 py-2 text-left text-white">ИНН</th>
                                            <th class="border border-gray-300 px-4 py-2 text-left text-white">Прикрепить
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(payment, idx) in getActivePayments(index)" :key="idx">
                                            <td class="border border-gray-300 px-4 py-2">
                                                <Link v-if="payment.contract"
                                                    :href="route('admin.contract.show', { contract: payment.contract.id })"
                                                    class="text-blue-700">
                                                {{ payment.contract.number }}
                                                </Link>
                                            </td>
                                            <td class="border border-gray-300 px-4 py-2">{{ payment.value }}</td>
                                            <td class="border border-gray-300 px-4 py-2">{{ payment.inn }}</td>
                                            <td class="border border-gray-300 px-4 py-2 cursor-pointer">Прикрепить</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>

                    </template>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import PaymentLayout from '../Layouts/PaymentLayout.vue';
import axios from 'axios';
import { route } from 'ziggy-js';

export default {
    components: {
        Head
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
            activeRows: [],
        };
    },
    methods: {
        async toggleAttachMenu(index, paymentId) {
            const existingRow = this.activeRows.find((row) => row.index === index);

            if (existingRow) {
                this.activeRows = this.activeRows.filter((row) => row.index !== index);
            } else {
                const payments = await this.getPaymentsForRow(paymentId);
                console.log(payments);

                this.activeRows.push({ index, payments });
            }
        },
        async getPaymentsForRow(paymentId) {
            const response = await axios.get(route('payment.index'), {
                params: {
                    payment: paymentId
                }
            });
            console.log(response.data);

            let rowPayments = response.data ?? [];

            rowPayments = rowPayments.map(function (payment) {
                return {
                    'id': payment.id,
                    'value': payment.value,
                    'inn': payment.inn,
                    'contract': payment.contract
                }
            });
            return rowPayments;

        },
        isRowActive(index) {
            return this.activeRows.some((row) => row.index === index);
        },
        getActivePayments(index) {
            const row = this.activeRows.find((row) => row.index === index);
            return row ? row.payments : [];
        },
    },
}

</script>