<template>
    <div>
        <div class="grid grid-cols-8">
            <table class="table col-span-2 table-fixed h-fit w-full mb-10">
                <thead class="thead border-b">
                    <tr>
                        <th scope="col" class="px-3 py-2 border-x text-center font-bold text-base">
                            Закрытые сделки
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="px-4 py-2 text-center border-r">
                            Номер договора
                        </td>
                    </tr>
                    <tr v-for="contract in userReport.close_contracts" :key="contract.id" class="table-row ">
                        <th scope="row" class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap  border-x">
                            <a :href="route('admin.contract.show', contract.id)"
                                class="text-blue-500 hover:text-blue-600"
                                target="_blank" rel="noopener">
                            № {{ contract.number }}
                            </a>
                        </th>
                    </tr>
                </tbody>
            </table>
            <table class="table col-span-3 table-fixed h-fit w-full mb-10">
                <thead class="thead border-b">
                    <tr>
                        <th scope="col" colspan="2" class="px-3 py-2 border-x text-center font-bold text-base">
                            Платежи по ДЗ
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="px-4 py-2 text-center border-r">
                            Номер Платежа
                        </td>
                        <td class="px-4 py-2 text-center border-r">
                            Сумма
                        </td>
                    </tr>
                    <tr v-for="payment in userReport.accounts_receivable" :key="payment.id" class="table-row ">
                        <th scope="row" class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap  border-x">
                            <a :href="route('admin.payment.show', payment.id)"
                                class="text-blue-500 hover:text-blue-600"
                                target="_blank" rel="noopener">
                            Платеж № {{ payment.id }}
                            </a>
                            по договору
                            <a :href="route('admin.contract.show', payment.contract.id)"
                                class="text-blue-500 hover:text-blue-600"
                                target="_blank" rel="noopener">
                                № {{ payment.contract.number }}
                            </a>
                        </th>
                        <td class="px-4 py-2 text-center border-r">
                            {{ formatPrice(payment.value) }}
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="table col-span-3 table-fixed h-fit w-full mb-10">
                <thead class="thead border-b">
                    <tr>
                        <th scope="col" colspan="3" class="px-3 py-2 border-x text-center font-bold text-base">
                            Допродажи
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="px-4 py-2 text-center border-r">
                            Номер договора
                        </td>
                        <td class="px-4 py-2 text-center border-r">
                            Услуга
                        </td>
                        <td class="px-4 py-2 text-center border-r">
                            Сумма
                        </td>
                    </tr>
                    <tr v-for="upsale in userReport.upsells" :key="upsale.id" class="table-row ">
                        <td scope="row" class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap  border-x">
                            <a :href="route('admin.contract.show', upsale.contract.id)"
                                class="text-blue-500 hover:text-blue-600"
                                target="_blank" rel="noopener">
                            № {{ upsale.contract.number }}
                            </a>
                        </td>
                        <td class="px-4 py-2 text-center border-r">
                            {{upsale.contract.services.map(service => service.name).join(', ')}}
                        </td>
                        <td class="px-4 py-2 text-center border-r">
                            {{ formatPrice(upsale.value) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
<script>
import { route } from 'ziggy-js';

export default {
    props: {
        userReport: {
            type: Object,
        },
    },
}
</script>
