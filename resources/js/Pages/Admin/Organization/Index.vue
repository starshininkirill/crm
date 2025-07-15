<template>
    <OrganizationLayout>

        <Head title="Организации" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Организации</h1>
            <div class="grid grid-cols-12 gap-8">
                <div class="col-span-3">
                    <OrganizationCreateForm />
                </div>
                <div class=" col-span-9">
                    <h2 v-if="!organizations.length" class="text-xl">Организаций не найдено</h2>
                    <div v-if="organizations.length" class="">
                        <table class="table table-fixed">
                            <thead class="thead">
                                <tr class="border-b">
                                    <th scope="col" class="px-3 py-3 border-x w-36">
                                        Краткое название
                                    </th>
                                    <th scope="col" class="px-3 py-3 border-x">
                                        Полное название
                                    </th>
                                    <th scope="col" class="px-3 py-3 border-r w-32">
                                        Нумератор
                                    </th>
                                    <th scope="col" class="px-3 py-3 border-r w-48">
                                        ИНН
                                    </th>
                                    <th scope="col" class="px-3 py-3 border-r w-20">
                                        ID в Wiki
                                    </th>
                                    <th scope="col" class="px-3 py-3 border-r text-center w-32">
                                        Редактировать
                                    </th>
                                    <th scope="col" class="px-3 py-3 border-x text-center  w-32">
                                        Удалить
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="organization in organizations" :key="organization.id" class="table-row border-b">
                                    <th scope="row" class="px-3 py-4 font-medium text-gray-900 whitespace-nowrap border-x">
                                        {{ organization.short_name }}
                                    </th>
                                    <td class="px-3 py-4 border-r" >
                                        {{ organization.name }}
                                    </td>
                                    <td class="px-3 py-4 border-r" >
                                        {{ organization.doc_number ?? 'Глобальный' }}
                                    </td>
                                    <td class="px-3 py-4 border-r">
                                        {{ organization.inn }}
                                    </td>
                                    <td class="px-3 py-4 border-r">
                                        {{ organization.wiki_id }}
                                    </td>
                                    <td class="px-3 py-4  border-r">
                                        <Link   
                                            :href="route('admin.organization.edit', { organization: organization.id })"
                                            class="font-medium text-blue-600  hover:underline">
                                        Редактировать
                                        </Link>
                                    </td>
                                    <td class="px-3 py-4 border-x ">
                                        <button @click="deleteOrganization(organization.id)"
                                            class="font-medium text-red-600 hover:underline w-full text-center">
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