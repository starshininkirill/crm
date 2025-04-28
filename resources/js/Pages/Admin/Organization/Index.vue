<template>
    <OrganizationLayout>

        <Head title="Организации" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Организации</h1>
            <div class="grid grid-cols-3 gap-8">
                <OrganizationCreateForm />
                <div class=" col-span-2">
                    <h2 v-if="!organizations.length" class="text-xl">Организаций не найдено</h2>
                    <div v-if="organizations.length" class="">
                        <table
                            class="table">
                            <thead class="thead">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        Название
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        ИНН
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
                                    class="table-row">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                        {{ organization.short_name }}
                                    </th>
                                    <td class="px-6 py-4">
                                        {{ organization.inn }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <Link
                                            :href="route('admin.organization.edit', { organization: organization.id })"
                                            class="font-medium text-blue-600  hover:underline">
                                        Редактировать
                                        </Link>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button @click="deleteOrganization(organization.id)"
                                            class="font-medium text-red-600 hover:underline">
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
    </OrganizationLayout>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import OrganizationLayout from '../Layouts/OrganizationLayout.vue';
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js';
import OrganizationCreateForm from './Components/OrganizationCreateForm.vue';

export default {
    components: {
        Head,
        OrganizationCreateForm,
        OrganizationLayout
    },
    props: {
        organizations: {
            type: Array,
        },
    },
    methods: {
        deleteOrganization(id) {
            if (confirm('Вы уверены, что хотите удалить эту организацию?')) {
                router.delete(route('admin.organization.destroy', id));
            }
        },
    },
}

</script>