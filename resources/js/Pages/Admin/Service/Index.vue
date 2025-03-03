<template>
    <ServiceLayout>
    <Head title="Услуги" />
    <div class="contract-page-wrapper flex flex-col">
        <h1 class="text-4xl font-semibold mb-6">Услуги</h1>
        <div class="grid grid-cols-3 gap-8">
            <ServiceCreateForm :categories="categories" />
            <div class=" col-span-2">
                <h2 v-if="!services.length" class="text-xl">Услуг не найдено</h2>

                <div v-if="services.length">
                    <table
                        class="shadow-md  overflow-hidden rounded-md sm:rounded-lg w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    Услуга
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Категория
                                </th>
                                <th scope="col" class="px-6 py-3 text-right w-12">
                                    Редактировать
                                </th>
                                <th scope="col" class="px-6 py-3 text-right w-12">
                                    Удалить
                                </th>
                            </tr>
                        </thead>
                        <tbody>

                            <tr v-for="service in services" :key="service.id"
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ service.name }}
                                </th>
                                <td class="px-6 py-4">
                                    <Link
                                        :href="route('admin.service.index', { serviceCategory: service.category.id })">
                                    {{ service.category.name }}
                                    </Link>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <Link :href="route('admin.service.edit', { service: service.id })"
                                        class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                    Редактировать
                                    </Link>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button @click="deleteService(service.id)"
                                        class="font-medium text-red-600 dark:text-red-500 hover:underline">
                                        Удалить
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </ServiceLayout>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import ServiceLayout from '../Layouts/ServiceLayout.vue';
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js';
import ServiceCreateForm from './Components/ServiceCreateForm.vue';

export default {
    components: {
        Head,
        ServiceCreateForm,
        ServiceLayout
    },
    props: {
        services: {
            type: Array,
            required: true,
        },
        categories: {
            type: Array,
            required: true,
        },
    },
    methods: {
        deleteService(id) {
            if (confirm('Вы уверены, что хотите удалить эту Услугу?')) {
                router.delete(route('admin.service.destroy', id));
            }
        },
    },
}


</script>