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
                        <th class="border border-gray-300 px-4 py-2 text-left text-white">Разделить</th>
                        <th class="border border-gray-300 px-4 py-2 text-left text-white">Прикрепить</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="payment in payments" :key="payment.id">
                        <td class="border border-gray-300 px-4 py-2">

                        </td>
                        <td class="border border-gray-300 px-4 py-2">
                            <Link :href="route('admin.payment.show', { payment: payment.id })"
                                class=" text-blue-700 underline">
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
                        <td class="border border-gray-300 px-4 py-2">
                            Прикрепить
                        </td>
                        <td class="border border-gray-300 px-4 py-2">
                            Прикрепить
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import PaymentLayout from '../Layouts/PaymentLayout.vue';

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
}

</script>