<template>
    <DocumentTemplateLayout>

        <Head title="Шаблоны документов" />

        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Шаблоны документов</h1>

            <Modal :open="idOpenModal" @close="idOpenModal = false">
                <EditForm @closeModal="idOpenModal = false" :docuementTemplate="currentTemplate" />
            </Modal>

            <div class="grid grid-cols-3 gap-8">
                <form @submit.prevent="submitForm" method="POST" enctype="multipart/form-data"
                    class="flex flex-col gap-3">
                    <div class="text-3xl font-semibold">
                        Создать Шаблон документа
                    </div>

                    <Error />

                    <!-- Добавленные строки для быстрого заполнения -->
                    <div class="flex flex-col gap-2">
                        <div class="text-sm font-medium text-gray-900">Быстрый выбор:</div>
                        <div class="flex flex-wrap gap-2">
                            <span v-for="template in quickTemplates" :key="template"
                                @click="form.result_name = template"
                                class="px-3 py-1 bg-gray-100 hover:bg-gray-200 rounded-md cursor-pointer text-sm">
                                {{ template }}
                            </span>
                        </div>
                    </div>

                    <FormInput v-model="form.result_name" type="text" name="name" placeholder="Разработка сайта"
                        label="Название файла после генерации" autocomplete="name" required />

                    <FormInput v-model="form.template_id" type="number" name="name" placeholder="id шаблона"
                        label="id шаблона" autocomplete="name" required />

                    <label class="text-sm font-medium leading-6 text-gray-900 flex flex-col gap-1" for="file">
                        Файл
                        <input type="file" id="file" name="file" class="form-input cursor-pointer"
                            @change="handleFileChange" />
                    </label>

                    <button type="submit" class="btn w-full" data-ripple-light="true">
                        Создать
                    </button>
                </form>
                <div class="col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <input v-model="search" type="text" class="input max-w-[300px]" placeholder="Поиск...">
                    </div>
                    <h2 v-if="!documentTemplates.data.length" class="text-xl">Шаблонов документов не найдено</h2>
                    <div v-if="documentTemplates.data.length" class="relative">
                        <table class="w-full table">
                            <thead class="thead border-b">
                                <tr>
                                    <th scope="col" class="px-6 py-3 border-x">
                                        id Шаблона
                                    </th>
                                    <th scope="col" class="px-6 py-3 border-r">
                                        Название файла после генерации
                                    </th>
                                    <th scope="col" class="px-6 py-3 border-r">
                                        Файл
                                    </th>
                                    <th scope="col" class="px-6 py-3 border-r ">
                                        Скачать
                                    </th>
                                    <th scope="col" class="px-6 py-3 border-r text-right w-12">
                                        Редактировать
                                    </th>
                                    <th scope="col" class="px-6 py-3 border-x text-right w-12">
                                        Удалить
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="documentTemplate in documentTemplates.data" :key="documentTemplate.id"
                                    class="table-row">
                                    <td scope="row"
                                        class="px-6 py-4 border-x font-medium text-gray-900 whitespace-nowrap">
                                        {{ documentTemplate.template_id }}
                                    </td>
                                    <td class="px-6 py-4 border-r ">
                                        {{ documentTemplate.result_name }}
                                    </td>
                                    <td class="px-6 py-4 border-r break-all">
                                        {{ documentTemplate.file_name }}
                                    </td>
                                    <td class="px-6 py-4 border-r">
                                        <a download class="font-medium text-blue-600  hover:underline"
                                            :href="documentTemplate.file_path">
                                            Скачать
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 border-r text-right">
                                        <div @click="openModal(documentTemplate)"
                                            class="font-medium text-blue-600  hover:underline cursor-pointer">
                                            Редактировать
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 border-x text-right">
                                        <button @click="deleteDocumentTemplate(documentTemplate)"
                                            class="font-medium text-red-600 hover:underline">
                                            Удалить
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <Pagination :links="documentTemplates.links" />
                    </div>
                </div>
            </div>

        </div>
    </DocumentTemplateLayout>
</template>

<script>
import { Head, useForm } from '@inertiajs/vue3';
import DocumentTemplateLayout from '../Layouts/DocumentTemplateLayout.vue';
import FormInput from '../../../Components/FormInput.vue';
import VueSelect from 'vue-select';
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';
import Error from '../../../Components/Error.vue'
import Modal from '../../../Components/Modal.vue';
import EditForm from './Components/EditForm.vue';
import Pagination from '../../../Components/Pagination.vue';

export default {
    components: {
        Head,
        FormInput,
        Error,
        DocumentTemplateLayout,
        VueSelect,
        Modal,
        EditForm,
        Pagination
    },
    props: {
        documentTemplates: {
            type: Object,
        },
        filters: {
            type: Object,
            default: () => ({})
        }
    },
    data() {
        return {
            idOpenModal: false,
            currentTemplate: null,
            search: this.filters.template_id || '',
            quickTemplates: [
                "Разработка сайта",
                "Настройка контекстной рекламы",
                "SEO оптимизация",
                "Счёт и акт"
            ]
        }
    },
    setup(props) {
        const form = useForm({
            'template_id': null,
            'file': null,
            'result_name': null,
        });

        const submitForm = () => {
            form.post(route('admin.document-generator.store'), {
                onSuccess: () => {
                    form.template_id = '';
                    form.file = '';
                    form.result_name = '';

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
                router.delete(route('admin.document-generator.destroy', id));
            }
        },
        openModal(documentTemplate) {
            this.idOpenModal = true;
            this.currentTemplate = documentTemplate;
        },
        updateDocuments() {
            const params = {};
            if (this.search !== null) params.template_id = this.search;

            router.get(route('admin.document-generator.templates'), params, {
                preserveState: true,
                preserveScroll: true,
                replace: true
            });
        },
        handleSearchInput() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.updateDocuments();
            }, 300);
        },
    },
    watch: {
        search(newVal, oldVal) {
            if (newVal !== oldVal) {
                this.handleSearchInput();
            }
        }
    },
}
</script>