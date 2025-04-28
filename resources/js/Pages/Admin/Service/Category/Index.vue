<template>
    <ServiceLayout>

        <Head title="Категории Услуг" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Категории Услуг</h1>
            <div class="grid grid-cols-3 gap-8">
                <form @submit.prevent="submitForm" method="POST" class="flex flex-col gap-3">
                    <Error />
                    <FormInput v-model="form.name" type="text" name="name" placeholder="Название категории"
                        label="Название категории" autocomplete="name" required />
                    <KeyValueSelectInput v-if="Object.keys(types).length" :options="types"
                        label="Выберите тип категории услуг" name="type" id="type" v-model="form.type" />
                    <button type="submit" class="btn w-full" data-ripple-light="true">
                        Создать
                    </button>
                </form>
                <div class="flex flex-col gap-3 col-span-2">
                    <h2 v-if="!categories.length" class="text-xl">Категорий услуг не найдено</h2>
                    <table v-if="categories.length"
                        class="shadow-md overflow-hidden rounded-md sm:rounded-lg w-full text-sm text-left rtl:text-right text-gray-500 ">
                        <thead class="thead  ">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    Категория
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Тип Категории
                                </th>
                                <th scope="col" class="px-6 py-3 text-right w-12">
                                    Услуги
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

                            <tr v-for="category in categories" :key="category.id"
                                class="table-row ">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                                    {{ category.name }}
                                </th>
                                <td class="px-6 py-4">
                                    {{ category.type }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <Link :href="route('admin.service.index', { serviceCategory: category.id })"
                                        class="font-medium text-blue-600  hover:underline">
                                    {{ category.services_count }}
                                    </Link>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <Link :href="route('admin.service.category.edit', { serviceCategory: category.id })"
                                        class="font-medium text-blue-600  hover:underline">
                                    Редактировать
                                    </Link>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button @click="deleteServiceCategory(category.id)"
                                        class="font-medium text-red-600  hover:underline">
                                        Удалить
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </ServiceLayout>
</template>

<script>
import { Head, useForm } from '@inertiajs/vue3';
import ServiceLayout from '../../Layouts/ServiceLayout.vue';
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js';
import FormInput from '../../../../Components/FormInput.vue';
import KeyValueSelectInput from '../../../../Components/KeyValueSelectInput.vue';
import Error from '../../../../Components/Error.vue';

export default {
    components: {
        Head,
        FormInput,
        KeyValueSelectInput,
        Error,
        ServiceLayout
    },
    props: {
        categories: {
            type: Array,
        },
        types: {
            type: Object,
        }
    },
    methods: {
        deleteServiceCategory(id) {
            if (confirm('Вы уверены, что хотите удалить эту Категорию?')) {
                router.delete(route('admin.service-category.destroy', id));
            }
        },
    },
    setup(props) {
        const firstKey = Object.keys(props.types)[0];

        const form = useForm({
            'name': null,
            'type': firstKey,
        });

        const submitForm = () => {
            form.post(route('admin.service-category.store'), {
                onFinish: () => {
                    form.name = null;
                    form.type = firstKey;
                },
            });
        };

        return {
            form,
            submitForm
        }
    },
}


</script>