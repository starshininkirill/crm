<template>
    <AdvertisingDepartmentLayout>

        <Head title="Отчёт отдела Рекламы" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">
                Отчёт отдела Рекламы
            </h1>

            <table class="table table-fixed col-span-2 mb-20">
                <thead class="thead">
                    <tr>
                        <th scope="col" class="px-2 py-2 border-r w-52">
                            Сотрудник
                        </th>
                        <th scope="col" class="px-2 py-2 border-r">
                            ALl с Перерасч
                        </th>
                        <th scope="col" class="px-2 py-2 border-r">
                            ALl без Перерасч
                        </th>
                        <th scope="col" class="px-2 py-2 border-r">
                            Оплачен
                        </th>
                        <th scope="col" class="px-2 py-2 border-r">
                            $
                        </th>
                        <th scope="col" class="px-2 py-2 border-r">
                            Пауза
                        </th>
                        <th scope="col" class="px-2 py-2 border-r">
                            $
                        </th>
                        <th scope="col" class="px-2 py-2 border-r">
                            СРЧЕК
                        </th>
                        <th scope="col" class="px-2 py-2 border-r">
                            Тариф
                        </th>
                        <th scope="col" class="px-2 py-2 border-r">
                            Б1
                        </th>
                        <th scope="col" class="px-2 py-2 border-r">
                            Б2
                        </th>
                        <th scope="col" class="px-2 py-2 border-r">
                            Б3
                        </th>
                        <th scope="col" class="px-2 py-2 border-r">
                            Продажи
                        </th>
                        <th scope="col" class="px-2 py-2 border-r">
                            Настройки
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users_report" :key="user.id" class="table-row">
                        <th scope="row" class="px-2 border-x py-2 font-medium text-gray-900 whitespace-nowrap ">
                            {{ user.full_name }}
                        </th>
                        <td class="px-2 border-r py-2 text-black ">
                            {{ user.current_months_count }}
                        </td>
                        <td class="px-2 border-r py-2 text-black ">
                            {{ user.current_months_count }}
                        </td>
                        <td class="px-2 border-r py-2 text-black ">
                            {{ user.next_months_count }}
                        </td>
                        <td class="px-2 border-r py-2 text-black ">
                            {{ formatPrice(user.next_months_amount) }}
                        </td>
                        <td class="px-2 border-r py-2 text-black ">
                            ?
                        </td>
                        <td class="px-2 border-r py-2 text-black ">
                            ?
                        </td>
                        <td class="px-2 border-r py-2 text-black ">
                            {{ formatPrice(user.next_months_average) }}
                        </td>
                        <td class="px-2 border-r py-2 text-black ">
                            {{ user.new_tarif_count }}
                        </td>
                        <td class="px-2 border-r py-2 text-black ">
                            -
                        </td>
                        <td class="px-2 border-r py-2 text-black ">
                            -
                        </td>
                        <td class="px-2 border-r py-2 text-black ">
                            -
                        </td>
                        <td class="px-2 border-r py-2 text-black ">
                            -
                        </td>
                        <td class="px-2 border-r py-2 text-black ">
                            -
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="table table-fixed col-span-2">
                <thead class="thead">
                    <tr>
                        <th scope="col" class="px-2 py-2  border-x w-24">
                            Дата оплаты
                        </th>
                        <th scope="col" class="px-2 py-2  border-r">
                            Сотрудник
                        </th>
                        <th scope="col" class="px-2 py-2  border-r w-32">
                            Сделка
                        </th>
                        <th scope="col" class="px-2 py-2  border-r w-56">
                            Сумма ведения
                        </th>
                        <th scope="col" class="px-2 py-2  border-r">
                            Тариф
                        </th>
                        <th scope="col" class="px-2 py-2  border-r w-24">
                            Месяц ведения
                        </th>
                        <th scope="col" class="px-2 py-2  border-r w-24">
                            Результат
                        </th>
                        <th scope="col" class="px-2 py-2  border-r w-20">
                            Тп
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="pair in pairs" :key="pair.id" class="table-row "
                        :class="{ '!bg-green-300': pair.next?.payment, '': !pair.next?.payment }">
                        <th scope="row" class="px-2 border-x py-2 font-medium text-gray-900 whitespace-nowrap ">
                            {{ pair.prev.payment?.created_at ? pair.prev.payment.created_at.substring(0, 10) :
                                'Неоплачен' }}
                        </th>
                        <td class="px-2 border-r py-2 text-black ">
                            <a :href="route('admin.user.show', pair.prev.user.id)" class="" target="_blank"
                                rel="noopener">
                                {{ pair.prev.user.full_name }}
                            </a>
                        </td>
                        <td class="px-2 border-r py-2 text-black ">
                            <a :href="route('admin.contract.show', pair.prev.contract.id)"
                                class="text-blue-500 hover:text-blue-600 font-semibold " target="_blank" rel="noopener">
                                Сделка №{{ pair.prev.contract.number }}
                            </a>
                        </td>
                        <td class="px-2 border-r py-2 text-black ">
                            {{ formatPrice(pair.prev.price) }}
                            {{ pair.next?.month ? `-> ${formatPrice(pair.next.price)}` : '' }}
                        </td>
                        <td class="px-2 border-r py-2 text-black ">
                            {{ pair.prev.tarif.name }}
                            {{ pair.next?.tarif ? `-> ${pair.next.tarif.name}` : '' }}
                        </td>
                        <td class="px-2 border-r py-2 text-black ">
                            {{ pair.prev.month }}
                            {{ pair.next?.month ? `-> ${pair.next.month}` : '' }}
                        </td>
                        <td class="px-2 border-r py-2 text-black ">
                            {{ pair.next?.payment ? 'Оплачен' : 'Ожидает' }}
                        </td>
                        <td class="px-2 border-r py-2 text-black ">
                            {{ pair.is_new_tarif ? 'Да' : 'Нет' }}
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
        pairs: {
            type: Array,
        },
        users_report: {
            type: Object,
        }
    },
}


</script>