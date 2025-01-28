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
            <h2 v-if="!payments.length">Платежей не найдено</h2>

            <table v-else class="min-w-full border border-gray-300">
                <thead>
                    <tr class="bg-gray-800">
                        <th class="border border-gray-300 px-4 py-2 text-left text-white">ИП</th>
                        <th class="border border-gray-300 px-4 py-2 text-left text-white">ID платежа</th>
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
                                {{ payment.id }}
                                </Link>
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ payment.value }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{  payment.description  }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ payment.inn }}
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                {{ payment.created_at }}
                            </td>
                            <td @click="toggleAttachMenu(index, payment.id)"
                                class="border border-gray-300 px-4 py-2 cursor-pointer text-blue-700">
                                Прикрепить
                            </td>
                            <td class="border border-gray-300 px-4 py-2">
                                Разделить
                            </td>
                        </tr>

                        <tr v-if="isRowActive(index)">
                            <td class="border border-gray-300 px-4 py-4" colspan="8">
                                <div class="text-xl font-semibold mb-3">Прикрепить платёж</div>
                                <div class="grid grid-cols-2 gap-5">
                                    <div>
                                        Поиск
                                    </div>
                                    <div>
                                        <div class="text-xl mb-3">Шортлист</div>
                                        <table v-if="getActivePayments(index).length > 0"
                                            class="w-full border border-gray-300">
                                            <thead>
                                                <tr class="bg-gray-800">
                                                    <th class="border border-gray-300 px-4 py-2 text-left text-white">
                                                        Сделка
                                                    </th>
                                                    <th class="border border-gray-300 px-4 py-2 text-left text-white">
                                                        Сумма</th>
                                                    <th class="border border-gray-300 px-4 py-2 text-left text-white">
                                                        ИНН</th>
                                                    <th class="border border-gray-300 px-4 py-2 text-left text-white">
                                                        Номер платежа</th>
                                                    <th class="border border-gray-300 px-4 py-2 text-left text-white">
                                                        Прикрепить
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(waitPayment, idx) in getActivePayments(index)" :key="idx">
                                                    <td class="border border-gray-300 px-4 py-2">
                                                        <Link v-if="waitPayment.contract"
                                                            :href="route('admin.contract.show', { contract: waitPayment.contract.id })"
                                                            class="text-blue-700">
                                                        {{ waitPayment.contract.number }}
                                                        </Link>
                                                    </td>
                                                    <td class="border border-gray-300 px-4 py-2">{{ waitPayment.value }}
                                                    </td>
                                                    <td class="border border-gray-300 px-4 py-2">{{ waitPayment.inn }}
                                                    </td>
                                                    <td class="border border-gray-300 px-4 py-2">{{ waitPayment.order }}
                                                    </td>
                                                    <td @click="attachPayment(waitPayment, payment)"
                                                        class="border border-gray-300 px-4 py-2 cursor-pointer text-blue-700">
                                                        Прикрепить</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <div v-else>
                                            Ожиданий не найдено
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>

                    </template>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import { Head, router } from '@inertiajs/vue3';
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
        attachPayment(oldPayment, newPayment) {
            router.post(route('payment.shortlist.attach'), {
                oldPayment: oldPayment.id,
                newPayment: newPayment.id,
            })

        },
        async toggleAttachMenu(index, paymentId) {
            const existingRow = this.activeRows.find((row) => row.index === index);

            if (existingRow) {
                this.activeRows = this.activeRows.filter((row) => row.index !== index);
            } else {
                const payments = await this.getPaymentsForRow(paymentId);
                this.activeRows.push({ index, payments });
            }
        },
        async getPaymentsForRow(paymentId) {
            const response = await axios.get(route('payment.shortlist', { payment: paymentId }));
            let rowPayments = response.data ?? [];

            rowPayments = rowPayments.map(function (payment) {
                return {
                    'id': payment.id,
                    'value': payment.value,
                    'inn': payment.inn,
                    'contract': payment.contract,
                    'order': payment.order
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