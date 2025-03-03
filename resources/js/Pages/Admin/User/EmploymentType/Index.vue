<template>
    <UserLayout>

        <Head title="Типы устройства" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Типы устройства</h1>

            <div class="grid grid-cols-3 gap-8">
                <form @submit.prevent="sumbitForm" class="flex flex-col gap-2">
                    <Error />

                    <span class="text-lg font-semibold">
                        Основная информация
                    </span>

                    <FormInput v-model="form.name" name="name" label="Название" placeholder="Название" type="text" />

                    <FormInput v-model="form.compensation" name="compensation" label="Компенсация (%)"
                        placeholder="Компенсация" type="number" />

                    <div class="flex flex-col gap-1">
                        <span class="text-lg font-semibold mb-2">
                            Дополнительные поля
                        </span>
                        <div v-for="(field, idx) in form.fields" class="grid grid-cols-3 gap-2">
                            <FormInput v-model="form.fields[idx].name" name="name" label="Название (Английское)"
                                placeholder="Например: inn" type="text" />
                            <FormInput v-model="form.fields[idx].readName" name="readName" label="Название (Русское)"
                                placeholder="Например: ИНН" type="text" />
                            <div>
                                <div class="label">
                                    Тип поля
                                </div>
                                <VueSelect v-model="form.fields[idx].type" :options="inputTypes"
                                    :reduce="type => type.value" label="name" class="full-vue-select" />
                            </div>
                        </div>
                        <div class="flex w-full items-center justify-between gap-2">
                            <div class="text-sm text-green-500 font-semibold cursor-pointer" @click="addField()">
                                Добавить поле
                            </div>
                            <div v-if="form.fields.length > 1" class="text-sm text-red-500 font-semibold cursor-pointer"
                                @click="removeField()">
                                Удалить поле
                            </div>
                        </div>
                    </div>

                    <button class="btn">
                        Создать
                    </button>
                </form>
                <div class="flex flex-col gap-3 col-span-2">
                    <h2 class="text-xl font-semibold">{{ employmentTypes == [] ? 'Нет созданных типов' : 'Типы' }}</h2>

                    <table v-if="employmentTypes.length"
                        class="shadow-md overflow-hidden rounded-md sm:rounded-lg w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    Название
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Компенсация
                                </th>
                                <th scope="col" class="px-6 py-3 ">
                                    Поля
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

                            <tr v-for="type in employmentTypes" :key="type.id"
                                class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <th scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ type.name }}
                                </th>
                                <td class="px-6 py-4">
                                    {{ type.compensation }} %
                                </td>
                                <td class="px-6 py-4 ">
                                    {{type.fields.map(field => field.readName).join(', ')}}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <Link href="#" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                    Редактировать
                                    </Link>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div @click="deleteType(type.id)"
                                        class="font-medium cursor-pointer text-red-600 dark:text-red-500 hover:underline">
                                        Удалить
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script>
import { Head, useForm } from '@inertiajs/vue3';
import UserLayout from '../../Layouts/UserLayout.vue';
import FormInput from '../../../../Components/FormInput.vue';
import Error from '../../../../Components/Error.vue';
import VueSelect from 'vue-select';
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

export default {
    components: {
        Head,
        FormInput,
        Error,
        VueSelect,
        UserLayout
    },
    props: {
        employmentTypes: {
            type: Array,
            required: true,
        },
    },
    data() {
        let form = useForm({
            'name': null,
            'compensation': 0,
            'fields': [
                {
                    'name': null,
                    'readName': null,
                    'type': 'text',
                }
            ]
        })
        let inputTypes = [
            {
                'name': 'Тектовое',
                'value': 'text',
            },
            {
                'name': 'Число',
                'value': 'number',
            },
        ]
        return {
            form,
            inputTypes
        }
    },
    methods: {
        deleteType(id) {
            if (confirm('Вы уверены, что хотите удалить?')) {
                router.delete(route('admin.employment-type.destroy', id));
            }
        },
        addField() {
            this.form.fields.push({
                'name': null,
                'readName': null,
                'type': 'text',
            })
        },
        removeField() {
            if (this.form.fields.length > 1) {
                this.form.fields.pop();
            }
        },
        sumbitForm() {
            let th = this;
            this.form.post(route('admin.employment-type.store'), {
                onSuccess() {
                    th.form.name = null;
                    th.form.compensation = 0;
                    th.form.fields = [
                        {
                            'name': null,
                            'readName': null,
                            'type': 'text',
                        }
                    ]
                },
            })
        }
    }
}


</script>