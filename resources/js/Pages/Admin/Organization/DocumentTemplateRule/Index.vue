<template>
    <OrganizationLayout>

        <Head title="Привязать шаблон" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Привязать шаблон</h1>
            <div class="grid grid-cols-3 gap-8">
                <form @submit.prevent="submitForm" method="POST" enctype="multipart/form-data"
                    class="flex flex-col gap-3">
                    <div class="text-3xl font-semibold">
                        Связать
                    </div>

                    <Error />

                    <div class=" flex flex-col gap-2">
                        <div>
                            <div class="label">
                                Шаблон документа
                            </div>
                            <VueSelect v-model="form.document_template_id"
                                :reduce="documentTemplates => documentTemplates.id" label="name"
                                :options="documentTemplates">
                            </VueSelect>
                        </div>
                        <div>
                            <div class="label">
                                Тип
                            </div>
                            <VueSelect v-model="form.type" :reduce="documentRuleTypes => documentRuleTypes.value"
                                label="name" :options="documentRuleTypes">
                            </VueSelect>
                        </div>
                        <div class="mb-2">
                            <div class="flex flex-col gap-2">
                                <div v-for="(service, idx) in selectedServices">
                                    <div class="label">
                                        Услуга {{ idx + 1 }}
                                    </div>
                                    <div class="flex gap-3 items-center w-full">
                                        <VueSelect v-model="selectedServices[idx]" label="name"
                                            :options="filtredServices" class="full-vue-select"
                                            @update:modelValue="selectService">
                                        </VueSelect>
                                        <div @click="removeService(idx)"
                                            class="flex items-center justify-center rounded-full w-8 h-8 bg-red-400 flex-shrink-0 cursor-pointer">
                                            <span class="w-1/4 h-0.5 bg-white"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-sm text-green-500 font-semibold cursor-pointer mt-2" @click="addService">
                                Добавить услугу
                            </div>
                        </div>
                        <button type="submit" class="btn !h-auto w-full-" data-ripple-light="true">
                            Создать
                        </button>
                    </div>
                </form>
                <div class=" col-span-2">
                    <h2 v-if="!documentRules.length" class="text-xl">Привязанных шаблонов не найдено</h2>
                    <div v-if="documentRules.length" class="relative">
                        <div class="mb-2 font-semibold flex gap-3  items-center justify-between mb-3">
                            <span>
                                Тут будет фильтр
                            </span>
                            <div class="btn !w-fit" @click="openModal">
                                Проверить
                            </div>
                        </div>
                        <table
                            class="w-full text-sm text-left rtl:text-right text-gray-500 overflow-x-auto shadow-md sm:rounded-lg">
                            <thead class="thead">
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
                                <tr v-for="documentRule in documentRules" :key="documentRule.id"
                                    class="table-row">
                                    <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                        {{ documentRule.id }}
                                    </td>
                                    <td class="px-6 py-4 ">
                                        <Link class=" text-blue-500 font-semibold"
                                            :href="route('admin.document-template.show', { documentTemplate: documentRule.document_template.id })">
                                        {{ documentRule.document_template.name }}
                                        </Link>
                                    </td>
                                    <td class="px-6 py-4">
                                        <Link class=" text-blue-500 font-semibold"
                                            :href="route('admin.organization.show', { organization: documentRule.document_template.organization.id })">
                                        {{ documentRule.document_template.organization.short_name }}
                                        </Link>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div v-if="documentRule.services.length">
                                            <Link v-for="(service, idx) in documentRule.services"
                                                class=" text-blue-500 font-semibold hover:underline"
                                                :href="route('admin.service.show', { service: service.id })">
                                            {{ service.name }}<span v-if="idx !== documentRule.services.length - 1">,
                                            </span>
                                            </Link>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ documentRule.type }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        Редактировать
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button @click="deleteRule(documentRule.id)"
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

            <Modal :open="isModalOpen" @close="isModalOpen = false">
                <CheckRule :services="services" />
            </Modal>
        </div>
    </OrganizationLayout>
</template>

<script>
import { Head, useForm } from '@inertiajs/vue3';
import OrganizationLayout from '../../Layouts/OrganizationLayout.vue';
import FormInput from '../../../../Components/FormInput.vue';
import Info from '../../../../Components/Info.vue';
import VueSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';
import Error from '../../../../Components/Error.vue';
import CheckRule from './CheckRule.vue';
import Modal from '../../../../Components/Modal.vue';

export default {
    components: {
        Head,
        FormInput,
        VueSelect,
        OrganizationLayout,
        Error,
        Info,
        CheckRule,
        Modal,
    },
    props: {
        documentTemplates: {
            type: Array,
        },
        services: {
            type: Array,
        },
        organizations: {
            type: Array,
        },
        documentRules: {
            type: Array,
        },
        documentRuleTypes: {
            type: Array,
        }
    },
    data() {
        return {
            form: useForm({
                document_template_id: null,
                services: [],
                type: null,
            }),
            filtredServices: this.services,
            selectedServices: [null],
            isModalOpen: false,
        };
    },
    methods: {
        submitForm() {
            this.form.services = this.selectedServices.map(service => service?.id);
            this.form.post(route('admin.document-selection-rule.store'), {
                onSuccess: () => {
                    this.form.document_template_id = null;
                    this.form.services = [null];
                    this.form.type = null;
                    this.selectedServices = [null];

                    this.filtredServices = this.services;
                },
            });
        },
        deleteRule(id) {
            if (confirm('Вы уверены, что хотите удалить правило?')) {
                router.delete(route('admin.document-selection-rule.destroy', id));
            }
        },
        addService() {
            this.selectedServices.push(null);
        },
        removeService(idx) {
            if (idx > 0 && idx < this.selectedServices.length) {
                this.selectedServices.splice(idx, 1);
            }
        },
        selectService(newId) {
            this.filtredServices = this.filtredServices.filter(function (el) {
                return el.id != newId.id;
            })
        },
        openModal() {
            this.isModalOpen = true;
        },
    }
};
</script>