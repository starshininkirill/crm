<template>
    <DocumentTemplateLayout>

        <Head title="Сгенерированные документы" />

        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Сгенерированные документы</h1>

            <div class="col-span-2">
                <div class="flex items-center gap-3 mb-4">
                    <VueSelect v-model="selectedType" :options="typeOptions" :reduce="type => type.id" label="name"
                        class="full-vue-select max-w-[200px] w-full" />
                    <input v-model="search" type="text" class="input max-w-[300px]" placeholder="Поиск...">
                    <VueDatePicker v-model="selectedDate" class="max-w-[200px] w-full ml-auto" :auto-apply="true"
                        format="yyyy-MM-dd" model-type="yyyy-MM-dd" placeholder="Дата" locale="ru"
                        @update:model-value="updateDocuments" />
                </div>
                <h2 v-if="!documents.data.length" class="text-xl">Сгенерированных документов не найдено</h2>
                <div v-if="documents.data.length" class="relative">
                    <table class="w-full table table-fixed">
                        <thead class="thead border-b">
                            <tr>
                                <th scope="col" class="px-3 py-2 border-x w-36">
                                    Дата
                                </th>
                                <th scope="col" class="px-3 py-2 border-x w-64">
                                    Сотрудник
                                </th>
                                <th scope="col" class="px-3 py-2 border-x w-32">
                                    Сделка
                                </th>
                                <th scope="col" class="px-3 py-2 border-x w-28">
                                    Номер счета
                                </th>
                                <th scope="col" class="px-3 py-2 border-x w-44">
                                    ИНН
                                </th>
                                <th scope="col" class="px-3 py-2 border-x w-40">
                                    Тип документа
                                </th>
                                <th scope="col" class="px-3 py-2 border-x">
                                    Название файла
                                </th>
                                <th scope="col" class="px-3 py-2 border-x w-28">
                                    WORD файл
                                </th>
                                <th scope="col" class="px-3 py-2 border-x w-28">
                                    PDF файл
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="document in documents.data" :key="document.id" class="table-row">
                                <td class="px-3 py-4 border-x font-medium text-gray-900 whitespace-nowrap">
                                    {{ document.date }}
                                </td>
                                <td scope="row" class="px-3 py-4 border-r whitespace-nowrap">
                                    {{ document.creater }}
                                </td>
                                <td scope="row" class="px-3 py-4 border-r w-20">
                                    {{ document.deal }}
                                </td>
                                <td scope="row" class="px-3 py-4 border-r">
                                    {{ document.act_number }}
                                </td>
                                <td scope="row" class="px-3 py-4 border-r">
                                    {{ document?.inn ?? '-' }}
                                </td>
                                <td class="px-3 py-4 border-r ">
                                    {{ document.type }}
                                </td>
                                <td class="px-3 py-4 border-r ">
                                    {{ document.file_name }}
                                </td>
                                <td class="px-3 py-4 border-r text-center">
                                    <a download class="font-medium text-blue-600  hover:underline"
                                        :href="document.word_file">
                                        Скачать
                                    </a>
                                </td>
                                <td class="px-3 py-4 border-r text-center">
                                    <a v-if="document.pdf_file" download
                                        class="font-medium text-blue-600  hover:underline" :href="document.pdf_file">
                                        Скачать
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="text-xl text-black font-semibold mt-4">
                        Всего сгенерированных документов: {{ documentsCount }}
                    </div>

                    <Pagination :links="documents.links" />
                </div>
            </div>
        </div>
    </DocumentTemplateLayout>
</template>

<script>
import { Head, useForm } from '@inertiajs/vue3';
import DocumentTemplateLayout from '../Layouts/DocumentTemplateLayout.vue';
import Pagination from '../../../Components/Pagination.vue';
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';
import VueSelect from 'vue-select';
import VueDatePicker from '@vuepic/vue-datepicker';

export default {
    components: {
        Head,
        DocumentTemplateLayout,
        Pagination,
        VueSelect,
        VueDatePicker,
    },
    props: {
        documents: {
            type: Object,
        },
        filters: {
            type: Object,
            default: () => ({})
        },
        documentsCount: {
            type: Number,
            default: 0
        }
    },
    data() {
        return {
            search: this.filters.name || '',
            selectedType: this.filters.type || '',
            selectedDate: this.filters.date || null,
            searchTimeout: null,
            typeOptions: [
                {
                    'name': "Все",
                    'id': ""
                },
                {
                    'name': "Договор",
                    'id': "deal"
                },
                {
                    'name': "Счёт/Акт",
                    'id': "pay"
                },
                {
                    'name': "Акт",
                    'id': "act"
                },
                {
                    'name': "Счёт/фактура",
                    'id': "invoice"
                }
            ]
        }
    },
    methods: {
        updateDocuments() {
            const params = {};
            if (this.search !== null) params.name = this.search;
            if (this.selectedType !== null) params.type = this.selectedType;
            if (this.selectedDate) params.date = this.selectedDate;

            router.get(route('admin.document-generator.generated-documents'), params, {
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
        },
        selectedType(newVal, oldVal) {
            if (newVal !== oldVal) {
                this.handleSearchInput();
            }
        },
    },
}
</script>