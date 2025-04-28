<template>
    <ContractLayout>

        <Head title="Договоры" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Договоры</h1>
            <h2 v-if="!contracts.length">Договоров не найдено</h2>
            <div v-if="contracts.length" class="overflow-x-auto">
                <table
                    class="table">
                    <thead class="thead">
                        <tr>
                            <th scope="col" class="px-2 py-2 border-x">
                                Дата
                            </th>
                            <th scope="col" class="px-2 py-2 border-r">
                                Сотрудник
                            </th>
                            <th scope="col" class="px-2 py-2 border-r">
                                №
                            </th>
                            <th scope="col" class="px-2 py-2 border-r">
                                Компания
                            </th>
                            <th scope="col" class="px-2 py-2 border-r">
                                Номер телефона
                            </th>
                            <th scope="col" class="px-2 py-2 border-r">
                                Услуги
                            </th>
                            <th scope="col" class="px-2 py-2 border-r w-20">
                                Общая стоимость
                            </th>
                            <th scope="col" class="px-2 py-2 border-r w-20">
                                1-й
                            </th>
                            <th scope="col" class="px-2 py-2 border-r w-20">
                                2-й
                            </th>
                            <th scope="col" class="px-2 py-2 border-r w-20">
                                3-й
                            </th>
                            <th scope="col" class="px-2 py-2 border-r w-20">
                                4-й
                            </th>
                            <th scope="col" class="px-2 py-2 border-r w-20">
                                5-й
                            </th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr v-for="contract in contracts" :key="contract.id"
                            class="table-row ">
                            <th scope="row" class="px-2 border-x py-4 font-medium text-gray-900 whitespace-nowrap ">
                                {{ contract.created_at }}
                            </th>
                            <td class="px-2 border-r py-4">
                                <span v-if="contract.saller">
                                    {{ contract.saller.first_name }} {{ contract.saller.last_name }}
                                </span>
                                <span v-else>
                                    Не прикреплён
                                </span>
                            </td>
                            <td class="px-2 border-r py-4">
                                <span v-if="contract.parent.id">
                                    <Link class="text-blue-700"
                                        :href="route('admin.contract.show', { contract: contract.id })">
                                    {{ contract.number }}
                                    </Link>
                                    <br>
                                    Родитель:
                                    <Link class="text-blue-700"
                                        :href="route('admin.contract.show', { contract: contract.parent.id })">
                                    № {{ contract.parent.number }}
                                    </Link>
                                </span>
                                <span v-else>
                                    <Link class="text-blue-700"
                                        :href="route('admin.contract.show', { contract: contract.id })">
                                    Договор: №{{ contract.number }}
                                    </Link>
                                </span>
                            </td>
                            <td class="px-2 border-r py-4">
                                <span v-if="contract.client">
                                    {{ contract.client && contract.client.organization_name
                                        ? contract.client.organization_name
                                        : (contract.client ? contract.client.fio : 'Нет данных') }}
                                </span>
                            </td>
                            <td class="px-2 border-r py-4 whitespace-nowrap">
                                {{ contract.phone }}
                            </td>
                            <td class="px-2 border-r py-4">
                                <span v-for="(service, index) in contract.services" :key="service.id">
                                    {{ service.name }}<span v-if="index !== contract.services.length - 1">, </span>
                                </span>
                            </td>
                            <td class="px-2 border-r py-4">
                                {{ formatPrice(contract.price) }}
                            </td>
                            <td v-for="payment in contract.payments" :key="payment.id"
                                class="px-2 border-r py-4 whitespace-nowrap"
                                :class="{ 'bg-green-500 text-white': payment.status === paymentStatuses.close }">
                                <Link :href="route('admin.payment.show', { payment: payment.id })">
                                {{ formatPrice(payment.value) }}
                                </Link>
                            </td>
                            <td v-for="i in 5 - contract.payments.length" :key="'empty-' + i" class="px-2 border-r py-4">

                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </ContractLayout>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import ContractLayout from '../Layouts/ContractLayout.vue';

export default {
    components: {
        Head,
        ContractLayout
    },
    props: {
        contracts: {
            type: Array
        },
        paymentStatuses: {
            type: Object
        },
    },
}

</script>