<template>
    <AdvertisingDepartmentLayout>

        <Head title="Отчёт отдела Рекламы" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Отчёт отдела Рекламы</h1>

            <table class="table">
                <thead class="thead">
                    <tr>
                        <th scope="col" class="px-2 py-2 border-x">
                            Дата оплаты
                        </th>
                        <th scope="col" class="px-2 py-2 border-r">
                            Сотрудник
                        </th>
                        <th scope="col" class="px-2 py-2 border-r">
                            Сделка
                        </th>
                        <th scope="col" class="px-2 py-2 border-r">
                            Сумма ведения
                        </th>
                        <th scope="col" class="px-2 py-2 border-r">
                            Тариф
                        </th>
                        <th scope="col" class="px-2 py-2 border-r">
                            Месяц ведения
                        </th>
                        <th scope="col" class="px-2 py-2 border-r">
                            Результат
                        </th>
                        <th scope="col" class="px-2 py-2 border-r">
                            ТП
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="serviceMonth in previous_month.services" :key="serviceMonth.id" class="table-row ">
                        <th scope="row" class="px-2 border-x py-2 font-medium text-gray-900 whitespace-nowrap ">
                            {{ serviceMonth.payment_date ?? 'Не оплачен' }}
                        </th>
                        <td class="px-2 border-r py-2">
                            {{ serviceMonth.user.full_name }}
                        </td>
                        <td class="px-2 border-r py-2">
                            Сделка №{{ serviceMonth.contract.number }}
                        </td>
                        <td class="px-2 border-r py-2">
                            {{ formatPrice(serviceMonth.price) }}
                        </td>
                        <td class="px-2 border-r py-2">
                            {{ serviceMonth.tarif.name }}
                        </td>
                        <td class="px-2 border-r py-2">
                            Месяц: {{ serviceMonth.month }}
                        </td>
                        <td class="px-2 border-r py-2">
                            {{ serviceMonth?.payment?.status == 'close' ? 'Оплачен' : 'Ожидает' }}
                        </td>
                        <td class="px-2 border-r py-2">
                            Нет
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdvertisingDepartmentLayout>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import AdvertisingDepartmentLayout from '../Layouts/AdvertisingDepartmentLayout.vue';

export default {
    components: {
        Head,
        AdvertisingDepartmentLayout
    },
    props: {
        previous_month: {
            type: Object,
        },
    },
}


</script>