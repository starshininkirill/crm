<template>
    <SaleDepartmentLayout>

        <Head title="Руководители отдела продаж" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Руководители отдела продаж</h1>

            <table class="overflow-hidden table ">
                <thead class="thead">
                    <tr>
                        <th scope="col" class="px-6 py-3 border-x">
                            Отдел
                        </th>
                        <th scope="col" class="px-6 py-3 border-r">
                            Руководитель
                        </th>
                        <th scope="col" class="px-6 py-3 border-r">
                            Сотрудников в отделе
                        </th>
                        <th scope="col" class="px-6 py-3 border-r">
                            Выполнили план
                        </th>
                        <th scope="col" class="px-6 py-3 border-r">
                            NEW $
                        </th>
                        <th scope="col" class="px-6 py-3 border-r">
                            Б1
                        </th>
                        <th scope="col" class="px-6 py-3 border-r">
                            Б2
                        </th>
                        <th scope="col" class="px-6 py-3 border-r">
                            Ставка
                        </th>
                        <th scope="col" class="px-6 py-3 border-x">
                            Проценты
                        </th>
                        <th scope="col" class="px-6 py-3 border-x">
                            Итого ЗП
                        </th>
                    </tr>
                </thead>
                <!-- <tbody>
                    <tr v-for="service in services.data" :key="service.id" class="table-row ">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                            {{ service.name }}
                        </th>
                        <td class="px-6 py-4">
                            <Link :href="route('admin.service.index', { serviceCategory: service.category.id })">
                            {{ service.category.name }}
                            </Link>
                        </td>
                        <td class="px-6 py-4">
                            {{ formatPrice(service.price) }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <Link :href="route('admin.service.edit', { service: service.id })"
                                class="font-medium text-blue-600  hover:underline">
                            Редактировать
                            </Link>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button @click="deleteService(service.id)"
                                class="font-medium text-red-600  hover:underline">
                                Удалить
                            </button>
                        </td>
                    </tr>
                </tbody> -->
            </table>
        </div>
    </SaleDepartmentLayout>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import SaleDepartmentLayout from '../Layouts/SaleDepartmentLayout.vue';

export default {
    components: {
        Head,
        SaleDepartmentLayout
    },
    props: {
        department: {
            type: Object,
        },
    },
}


</script>