<template>
    <ServiceLayout>

        <Head title="Услуги" />
        <Modal :open="openModal" @close="openModal = false">
            <ServiceCreateForm @success="openModal = false" :categories="categories" />
        </Modal>
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Услуги</h1>
            <div class="flex mb-4 items-center justify-between">
                <div class="flex items-center gap-3">
                    <VueSelect v-model="selectedCategory" :options="categoriesOptions" :reduce="service => service.id"
                        label="name" class="full-vue-select min-w-[360px]" />
                    <input v-model="search" type="text" class="input min-w-[300px]" placeholder="Поиск...">
                    <div class="btn !w-fit" @click="resetFilter">
                        Сбросить
                    </div>
                </div>
                <div class="btn !w-fit ml-auto" @click="openModal = true">
                    Создать
                </div>
            </div>
            <div>
                <div>
                    <h2 v-if="!services.data.length" class="text-xl">Услуг не найдено</h2>

                    <div v-if="services.data.length">
                        <table
                            class="shadow-md overflow-hidden rounded-md sm:rounded-lg w-full text-sm text-left rtl:text-right text-gray-500 ">
                            <thead class="thead  ">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        Услуга
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Категория
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Цена
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
                                <tr v-for="service in services.data" :key="service.id" class="table-row ">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                                        {{ service.name }}
                                    </th>
                                    <td class="px-6 py-4">
                                        <Link
                                            :href="route('admin.service.index', { serviceCategory: service.category.id })">
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
                            </tbody>
                        </table>

                        <Pagination :links="services.links" />
                    </div>
                </div>
            </div>
        </div>
    </ServiceLayout>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3';
import ServiceLayout from '../Layouts/ServiceLayout.vue';
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js';
import ServiceCreateForm from './Components/ServiceCreateForm.vue';
import VueSelect from 'vue-select';
import Modal from '../../../Components/Modal.vue';
import Pagination from '../../../Components/Pagination.vue';

export default {
    components: {
        Head,
        ServiceCreateForm,
        ServiceLayout,
        VueSelect,
        Modal,
        Pagination
    },
    props: {
        services: {
            type: Object,
            required: true,
        },
        categories: {
            type: Array,
            required: true,
        },
        selectedCategoryId: {
            type: Number,
            default: null,
        },
        filters: {
            type: Object,
            default: () => ({})
        }
    },
    data() {
        return {
            openModal: false,
            categoriesOptions: [
                { id: null, name: 'Все категории' },
                ...this.categories
            ],
            selectedCategory: this.filters.category || null,
            search: this.filters.name || '',
            searchTimeout: null,
        };
    },
    methods: {
        deleteService(id) {
            if (confirm('Вы уверены, что хотите удалить эту Услугу?')) {
                router.delete(route('admin.service.destroy', id));
            }
        },
        updateServices() {
            const params = {};
            if (this.selectedCategory !== null) params.category = this.selectedCategory;
            if (this.search !== null) params.name = this.search;

            router.get(route('admin.service.index'), params, {
                preserveState: true,
                preserveScroll: true,
                replace: true
            });
        },
        handleSearchInput() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.updateServices();
            }, 300);
        },
        resetFilter() {
            this.search = '';
            this.selectedCategory = null
        }
    },
    watch: {
        selectedCategory(newVal, oldVal) {
            if (newVal !== oldVal) {
                this.updateServices();
            }
        },
        search(newVal, oldVal) {
            if (newVal !== oldVal) {
                this.handleSearchInput();
            }
        }
    },
}


</script>