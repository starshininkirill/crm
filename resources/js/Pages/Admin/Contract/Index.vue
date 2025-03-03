<template>
    <ContractLayout>

        <Head title="Договоры" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Договоры</h1>
            <h2 v-if="!contracts.length">Договоров не найдено</h2>
            <div v-if="contracts.length" class="overflow-x-auto">
                <table class="min-w-full border border-gray-300">
                    <thead>
                        <tr class="bg-gray-800">
                            <th class="border border-gray-300 px-2 py-1 text-left text-white">Дата</th>
                            <th class="border border-gray-300 px-2 py-1 text-left text-white">Сотрудник</th>
                            <th class="border border-gray-300 px-2 py-1 text-left text-white">№</th>
                            <th class="border border-gray-300 px-2 py-1 text-left text-white">Компания</th>
                            <th class="border border-gray-300 px-2 py-1 text-left text-white">Номер телефона</th>
                            <th class="border border-gray-300 px-2 py-1 text-left text-white w-64">Услуги</th>
                            <th class="border border-gray-300 px-2 py-1 text-left text-white">Общая стоимость</th>
                            <th class="border border-gray-300 px-2 py-1 text-left text-white whitespace-nowrap">1-й</th>
                            <th class="border border-gray-300 px-2 py-1 text-left text-white whitespace-nowrap">2-й</th>
                            <th class="border border-gray-300 px-2 py-1 text-left text-white whitespace-nowrap">3-й</th>
                            <th class="border border-gray-300 px-2 py-1 text-left text-white whitespace-nowrap">4-й</th>
                            <th class="border border-gray-300 px-2 py-1 text-left text-white whitespace-nowrap">5-й</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="contract in contracts" :key="contract.id">
                            <td class="border border-gray-300 px-2 py-1">{{ contract.created_at }}</td>
                            <td class="border border-gray-300 px-2 py-1">
                                <span v-if="contract.saller">
                                    {{ contract.saller.first_name }} {{ contract.saller.last_name }}
                                </span>
                                <span v-else>
                                    Не прикреплён
                                </span>
                            </td>
                            <td class="border border-gray-300 px-2 py-1">
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
                                    Договор: № {{ contract.number }}
                                    </Link>
                                </span>
                            </td>
                            <td class="border border-gray-300 px-2 py-1">
                                <span v-if="contract.client">
                                    {{ contract.client && contract.client.organization_name
                                        ? contract.client.organization_name
                                        : (contract.client ? contract.client.fio : 'Нет данных') }}
                                </span>
                            </td>
                            <td class="border border-gray-300 px-2 py-1">
                                {{ contract.phone }}
                            </td>
                            <td class="border border-gray-300 px-2 py-1">
                                <span v-for="(service, index) in contract.services" :key="service.id">
                                    {{ service.name }}<span v-if="index !== contract.services.length - 1">, </span>
                                </span>
                            </td>
                            <td class="border border-gray-300 px-2 py-1">
                                {{ contract.price }}
                            </td>
                            <template v-for="payment in contract.payments" :key="payment.id">
                                <td class="border border-gray-300 px-2 py-1 whitespace-nowrap"
                                    :class="{ 'bg-green-500 text-white': payment.status === paymentStatuses.close }">
                                    <Link :href="route('admin.payment.show', { payment: payment.id })">
                                    {{ payment.value }}
                                    </Link>
                                </td>
                            </template>

                            <template v-for="i in 5 - contract.payments.length" :key="'empty-' + i">
                                <td class="border border-gray-300 px-2 py-1"></td>
                            </template>
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