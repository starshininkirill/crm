<template>
    <PaymentLayout>
        <Head title="Платежи" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Платежи</h1>
            <div class="">
                <h2 v-if="!payments.length">Платежей не найдено</h2>

                <table v-else
                    class="table">
                    <thead class="thead">
                        <tr>
                            <th scope="col" class="px-4 py-3 border-x">
                                Номер
                            </th>
                            <th scope="col" class="px-4 py-3 border-r">
                                Дата
                            </th>
                            <th scope="col" class="px-4 py-3 border-r">
                                Договор
                            </th>
                            <th scope="col" class="px-4 py-3 border-r">
                                Сумма
                            </th>
                            <th scope="col" class="px-4 py-3 border-r">
                                ИНН
                            </th>
                            <th scope="col" class="px-4 py-3 border-r">
                                Статус
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="payment in payments" :key="payment.id" class="table-row">
                            <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap border-x">
                                <Link :href="route('admin.payment.show', { payment: payment.id })"
                                    class=" text-blue-700 underline">
                                № {{ payment.id }}
                                </Link>
                            </th>
                            <td class="px-4 py-3 border-r">
                                {{ payment.created_at }}
                            </td>
                            <td class="px-4 py-3 border-r">
                                <Link v-if="payment.contract"
                                    :href="route('admin.contract.show', { contract: payment.contract.id })"
                                    class="text-blue-700">
                                {{ payment.contract.number }}
                                </Link>
                            </td>
                            <td class="px-4 py-3 border-r">
                                {{ payment.value }}
                            </td>
                            <td class="px-4 py-3 border-r">
                                <template v-if="payment.contract && payment.contract.client">
                                    {{ payment.contract.client.inn }}
                                </template>
                            </td>
                            <td class="px-4 py-3 border-r"
                                :class="{ 'text-green-400 font-bold': payment.status === paymentStatuses.close }">
                                {{ payment.formatStatus }}
                            </td>

                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </PaymentLayout>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import PaymentLayout from '../Layouts/PaymentLayout.vue';

export default {
    components: {
        Head,
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
}

</script>