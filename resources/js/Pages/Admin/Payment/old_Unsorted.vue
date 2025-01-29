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
                                {{ payment.description }}
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
                                        <FormInput v-model="searches[index]" :disabled="isSearching"
                                            @input="onSearchInput(index)" name="searches" type="number"
                                            placeholder="Номер договора" label="Поиск по договору" />
                                        <div
                                            v-if="activeRows[index].searchContract && activeRows[index].searchContract.length != 0">
                                            <div class="text-xl font-semibold py-2 mb-2">
                                                Информация успешно найдена!
                                            </div>
                                            <div class="flex flex-col gap-2">
                                                <div class=" border-b pb-1 grid grid-cols-2">
                                                    Номер договора:
                                                    <span class="font-semibold">
                                                        {{ activeRows[index].searchContract.number }}
                                                    </span>
                                                </div>
                                                <div class=" border-b pb-1 grid grid-cols-2">
                                                    Дата договора:
                                                    <span class="font-semibold">
                                                        {{ activeRows[index].searchContract.created_at }}
                                                    </span>
                                                </div>
                                                <div class=" border-b pb-1 grid grid-cols-2">
                                                    Имя клиента/организации:
                                                    <span class="font-semibold">
                                                        {{
                                                            activeRows[index].searchContract.client_name }}
                                                    </span>
                                                </div>
                                                <div class=" border-b pb-1 grid grid-cols-2">
                                                    ИНН:
                                                    <span class="font-semibold">
                                                        {{ activeRows[index].searchContract.inn }}
                                                    </span>
                                                </div>
                                                <div class=" border-b pb-1 grid grid-cols-2">
                                                    Сумма договора:
                                                    <span class="font-semibold">
                                                        {{ activeRows[index].searchContract.amount_price }}
                                                    </span>
                                                </div>
                                                <div class=" border-b pb-1 grid grid-cols-2">
                                                    Услуги:
                                                    <div class="flex flex-wrap">
                                                        <div class="font-semibold w-fit"
                                                            v-if="activeRows[index].searchContract.services.length"
                                                            v-for="(service, idx) in activeRows[index].searchContract.services"
                                                            :key="service.id">
                                                            {{ service.name }}<span
                                                                v-if="idx != activeRows[index].searchContract.services.length - 1">
                                                                ,
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-4">
                                                <div v-if="activeRows[index].searchContract.payments">
                                                    <div class="text-2xl font-semibold mb-2">
                                                        Платежи
                                                    </div>
                                                    <div v-for="searchPayment in activeRows[index].searchContract.payments"
                                                        class=" border-b py-2 grid grid-cols-2 font-semibold">
                                                        {{ searchPayment.value }} ( № {{ searchPayment.order }} )
                                                        <div>
                                                            <div v-if="searchPayment.close">
                                                                Оплачен
                                                            </div>
                                                            <span v-else @click="attachPayment(searchPayment, payment)"
                                                                class="cursor-pointer text-blue-700">
                                                                Прикрепить</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div v-else class="text-xl font-semibold py-2 mb-2">
                                                    Платежей не найдено
                                                </div>
                                            </div>
                                            <div v-if="activeRows[index].searchContract.childs.length" class="mt-4">
                                                <div class="text-xl font-semibold py-2 mb-2">
                                                    Дочерние допродажи
                                                </div>

                                            </div>
                                        </div>
                                        <div v-else class="font-semibold py-2 mb-2">
                                            Договор не найден
                                        </div>
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
import FormInput from '../../../Components/FormInput.vue';

export default {
    components: {
        Head,
        FormInput
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
            searches: {},
            searchTimers: {},
            isSearching: false
        };
    },

    methods: {
        async onSearchInput(index) {
            if (this.searchTimers[index]) {
                clearTimeout(this.searchTimers[index]);
            }

            this.isSearching = false;

            this.searchTimers[index] = setTimeout(() => {
                this.searchContract(index);
            }, 1000);
        },
        async searchContract(index) {
            console.log('Ищем: ' + this.searches[index]);

            this.isSearching = true;

            let response = await axios.get(route('contract.index'), {
                params: {
                    s: this.searches[index]
                }
            })
            if (response.data.error) {
                this.activeRows[index].searchContract = []
            } else {
                this.activeRows[index].searchContract = response.data.contract
                console.log(response.data);

            }

            this.isSearching = false;
        },
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