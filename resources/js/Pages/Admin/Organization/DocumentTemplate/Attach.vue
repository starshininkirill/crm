<template>

    <Head title="Привязать шаблон" />
    <div class="contract-page-wrapper flex flex-col">
        <h1 class="text-4xl font-semibold mb-6">Привязать шаблон</h1>
        <div class="grid grid-cols-3 gap-8">
            <form @submit.prevent="submitForm" method="POST" enctype="multipart/form-data" class="flex flex-col gap-3">
                <div class="text-3xl font-semibold">
                    Связать
                </div>

                <Error />

                <div class=" flex flex-col gap-2">
                    <div class="flex flex-col gap-1">
                        Шаблон документа
                        <VueSelect v-model="form.document_template_id" :reduce="documetTemplates => documetTemplates.id"
                            label="name" :options="documetTemplates">
                        </VueSelect>
                    </div>
                    <div class="flex flex-col gap-1">
                        Организация
                        <VueSelect v-model="form.organization_id" :reduce="organizations => organizations.id"
                            label="short_name" :options="organizations">
                        </VueSelect>
                    </div>
                    <div class="flex flex-col gap-1">
                        Тип
                        <VueSelect v-model="form.type" :reduce="osdtTypes => osdtTypes.value" label="name"
                            :options="osdtTypes">
                        </VueSelect>
                    </div>
                    <div class="flex flex-col gap-1">
                        Услуга
                        <VueSelect v-model="form.service_id" :reduce="services => services.id" label="name"
                            :options="services">
                        </VueSelect>
                    </div>


                    <button type="submit" class="btn !h-auto w-full" data-ripple-light="true">
                        Создать
                    </button>
                </div>
            </form>
            <div class=" col-span-2">
                <h2 v-if="!osdt.length" class="text-xl">Привязанных шаблонов не найдено</h2>
                <div v-if="osdt.length" class="relative">
                    <div class="mb-2 font-semibold">
                        Тут будет фильтр
                    </div>
                    <table
                        class="w-full text-sm text-left rtl:text-right text-gray-500 overflow-x-auto shadow-md sm:rounded-lg">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-right w-12">
                                    id
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Шаблон
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Организация
                                </th>
                                <th scope="col" class="px-6 py-3 ">
                                    Услуга
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Тип
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
                            <tr v-for="attachedDocument in osdt" :key="attachedDocument.id"
                                class="bg-white border-b hover:bg-gray-50">
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ attachedDocument.id }}
                                </td>
                                <td class="px-6 py-4 ">
                                    <Link class=" text-blue-500 font-semibold"
                                        :href="route('admin.organization.document-template.edit', { documentTemplate: attachedDocument.documentTemplate.id })">
                                    {{ attachedDocument.documentTemplate.name }}
                                    </Link>
                                </td>
                                <td class="px-6 py-4">
                                    <Link class=" text-blue-500 font-semibold"
                                        :href="route('admin.organization.edit', { organization: attachedDocument.organization.id })">
                                    {{ attachedDocument.organization.short_name }}
                                    </Link>
                                </td>
                                <td class="px-6 py-4">
                                    <Link v-if="attachedDocument.service" class=" text-blue-500 font-semibold"
                                        :href="route('admin.service.edit', { service: attachedDocument.service.id })">
                                    {{ attachedDocument.service.name }}
                                    </Link>
                                </td>
                                <td class="px-6 py-4">
                                    {{ attachedDocument.type }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    Редактировать
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button @click="deleteOsdt(attachedDocument.id)"
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
</template>

<script>
import { Head, useForm } from '@inertiajs/vue3';
import OrganizationLayout from '../../Layouts/OrganizationLayout.vue';
import FormInput from '../../../../Components/FormInput.vue';
import VueSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';

export default {
    components: {
        Head,
        FormInput,
        VueSelect
    },
    props: {
        documetTemplates: {
            type: Array,
        },
        services: {
            type: Array,
        },
        organizations: {
            type: Array,
        },
        osdt: {
            type: Array,
        },
        osdtTypes: {
            type: Array,
        }
    },
    layout: OrganizationLayout,
    setup(props) {

        const form = useForm({
            'document_template_id': null,
            'service_id': null,
            'organization_id': null,
            'type': null
        });


        const submitForm = () => {
            form.post(route('osdt.store'), {
                onSuccess: () => {
                    form.document_template_id = null,
                        form.service_id = null,
                        form.organization_id = null,
                        form.type = null
                },
            });
        };

        return {
            form,
            submitForm
        }
    },
    methods: {
        deleteOsdt(id) {
            if (confirm('Вы уверены, что хотите отвязать Шаблон документа?')) {
                router.delete(route('osdt.destroy', id));
            }
        },
    }
}

</script>