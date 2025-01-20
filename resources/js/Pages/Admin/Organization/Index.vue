<template>

    <Head title="Организации" />
    <div class="contract-page-wrapper flex flex-col">
        <h1 class="text-4xl font-semibold mb-6">Организации</h1>
        <h2 v-if="!organizations.length" class="text-xl">Организаций не найдено</h2>
        <div v-if="organizations.length" class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Название
                        </th>
                        <th scope="col" class="px-6 py-3">
                            ИНН
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Статус
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
                    <tr v-for="organization in organizations" :key="organization.id"
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ organization.short_name }}
                        </th>
                        <td class="px-6 py-4">
                            {{ organization.inn }}
                        </td>
                        <td class="px-6 py-4">
                            <span v-if="organization.active" class=" text-green-500">
                                Активен
                            </span>

                            <span v-else class=" text-red-500">
                                Не активен
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <Link :href="route('admin.organization.edit', { organization: organization.id })"
                                class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                            Редактировать
                            </Link>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button @click="deleteOrganization(organization.id)"
                                class="font-medium text-red-600 dark:text-red-500 hover:underline">
                                Удалить
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import OrganizationLayout from '../Layouts/OrganizationLayout.vue';
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js';

export default {
    components: {
        Head
    },
    props: {
        organizations: {
            type: Array,
        },
    },
    layout: OrganizationLayout,
    methods : {
        deleteOrganization(id) {
            if (confirm('Вы уверены, что хотите удалить эту организацию?')) {
                router.delete(route('organization.destroy', id));
            }
        },
    },
}

</script>