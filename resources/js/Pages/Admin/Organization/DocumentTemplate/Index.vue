<template>
    <OrganizationLayout>

        <Head title="Шаблоны документов" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Шаблоны документов</h1>
            <div class="grid grid-cols-3 gap-8">
                <form @submit.prevent="submitForm" method="POST" enctype="multipart/form-data"
                    class="flex flex-col gap-3">
                    <div class="text-3xl font-semibold">
                        Создать Шаблон документа
                    </div>

                    <Error />

                    <FormInput v-model="form.name" type="text" name="name" placeholder="Название шаблона"
                        label="Название шаблона" autocomplete="name" required />

                    <div>
                        <div class="label">
                            Организация
                        </div>
                        <VueSelect v-model="form.organization_id" :reduce="organization => organization.id" label="short_name" :options="organizations"
                            class="full-vue-select" />
                    </div>

                    <label class="text-sm font-medium leading-6 text-gray-900 flex flex-col gap-1" for="file">
                        Файл
                        <input type="file" id="file" name="file" class="form-input cursor-pointer"
                            @change="handleFileChange" />
                    </label>

                    <button type="submit" class="btn w-full" data-ripple-light="true">
                        Создать
                    </button>
                </form>
                <div class=" col-span-2">
                    <h2 v-if="!documentTemplates.length" class="text-xl">Шаблонов документов не найдено</h2>
                    <div v-if="documentTemplates.length" class="relative">
                        <div class="mb-2 font-semibold">
                            Тут будет фильтр
                        </div>
                        <Error />
                        <table
                            class="w-full table">
                            <thead class="thead">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        Название
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Файл
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Организация
                                    </th>
                                    <th scope="col" class="px-6 py-3 ">
                                        Скачать
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
                                <tr v-for="documentTemplate in documentTemplates" :key="documentTemplate.id"
                                    class="table-row">
                                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                        {{ documentTemplate.name }}
                                    </td>
                                    <td class="px-6 py-4 ">
                                        {{ documentTemplate.file_name }}
                                    </td>
                                    <td class="px-6 py-4 ">
                                        <Link class=" text-blue-500 font-semibold"
                                            :href="route('admin.organization.show', { organization: documentTemplate.organization.id })">
                                        {{ documentTemplate.organization.short_name }}
                                        </Link>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a download class="font-medium text-blue-600  hover:underline"
                                            :href="documentTemplate.file_path">
                                            Скачать
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <Link
                                            :href="route('admin.document-template.edit', { documentTemplate: documentTemplate })"
                                            class="font-medium text-blue-600  hover:underline">
                                        Редактировать
                                        </Link>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button @click="deleteDocumentTemplate(documentTemplate.id)"
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
import { Head, useForm } from '@inertiajs/vue3';
import OrganizationLayout from '../../Layouts/OrganizationLayout.vue';
import FormInput from '../../../../Components/FormInput.vue';
import VueSelect from 'vue-select';
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';
import Error from '../../../../Components/Error.vue'

export default {
    components: {
        Head,
        FormInput,
        Error,
        OrganizationLayout,
        VueSelect
    },
    props: {
        documentTemplates: {
            type: Array,
        },
        organizations: {
            type: Array,
        }
    },
    setup(props) {

        const form = useForm({
            'name': null,
            'file': null,
            'organization_id': null,
        });

        const submitForm = () => {
            console.log(form);
            
            form.post(route('admin.document-template.store'), {
                onSuccess: () => {
                    form.name = '';
                    form.file = '';
                    form.organization_id = null;

                    const fileInput = document.querySelector('input[type="file"]');
                    if (fileInput) {
                        fileInput.value = '';
                    }
                },
            });
        };

        return {
            form,
            submitForm
        }
    },
    methods: {
        handleFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.form.file = file;
            } else {
                this.form.file = null;
            }
        },
        deleteDocumentTemplate(id) {
            if (confirm('Вы уверены, что хотите удалить этот Шаблон документа?')) {
                router.delete(route('admin.document-template.destroy', id));
            }
        },
    }
}

</script>