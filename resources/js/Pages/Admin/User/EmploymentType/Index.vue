<template>
    <UserLayout>

        <Head title="Типы устройства" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Типы устройства</h1>

            <div class="grid grid-cols-3 gap-8">
                <form @submit.prevent="submitForm" class="flex flex-col gap-2">
                    <Error />

                    <span class="text-lg font-semibold">
                        Основная информация
                    </span>

                    <FormInput v-model="form.name" name="name" label="Название" placeholder="Название" type="text" />

                    <FormInput v-model="form.compensation" name="compensation" label="Компенсация (%)"
                        placeholder="Компенсация" type="number" />

                    <ToggleSwitch v-model="form.is_another_recipient" label="Перевод на другого самозанятого:" />

                    <div class="flex flex-col gap-1">
                        <span class="text-lg font-semibold mb-2">
                            Дополнительные поля
                        </span>

                        <template v-if="form.is_another_recipient">
                            <div class="grid grid-cols-3 gap-2">
                                <FormInput name="first_name" label="Название (Английское)" value="first_name"
                                    readonly />
                                <FormInput name="first_name_ru" label="Название (Русское)" value="Имя" readonly />
                                <div>
                                    <div class="label">Тип поля</div>
                                    <VueSelect :options="inputTypes" :reduce="type => type.value" :modelValue="'text'"
                                        label="name" class="full-vue-select" disabled />
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                <FormInput name="last_name" label="Название (Английское)" value="last_name" readonly />
                                <FormInput name="last_name_ru" label="Название (Русское)" value="Фамилия" readonly />
                                <div>
                                    <div class="label">Тип поля</div>
                                    <VueSelect :options="inputTypes" :reduce="type => type.value" :modelValue="'text'"
                                        label="name" class="full-vue-select" disabled />
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                <FormInput name="surname" label="Название (Английское)" value="surname" readonly />
                                <FormInput name="surname_ru" label="Название (Русское)" value="Отчество" readonly />
                                <div>
                                    <div class="label">Тип поля</div>
                                    <VueSelect :options="inputTypes" :reduce="type => type.value" :modelValue="'text'"
                                        label="name" class="full-vue-select" disabled />
                                </div>
                            </div>
                        </template>

                        <div v-for="(field, idx) in form.draftFields" class="grid grid-cols-3 gap-2">
                            <FormInput v-model="form.draftFields[idx].name" name="name" label="Название (Английское)"
                                placeholder="Например: inn" type="text" />
                            <FormInput v-model="form.draftFields[idx].readName" name="readName"
                                label="Название (Русское)" placeholder="Например: ИНН" type="text" />
                            <div>
                                <div class="label">Тип поля</div>
                                <VueSelect v-model="form.draftFields[idx].type" :options="inputTypes"
                                    :reduce="type => type.value" label="name" class="full-vue-select" />
                            </div>
                        </div>

                        <div class="flex w-full items-center justify-between gap-2">
                            <div class="text-sm text-green-500 font-semibold cursor-pointer" @click="addField()">
                                Добавить поле
                            </div>
                            <div v-if="form.draftFields.length > 0"
                                class="text-sm text-red-500 font-semibold cursor-pointer" @click="removeField()">
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
                        class="shadow-md overflow-hidden rounded-md sm:rounded-lg w-full text-sm text-left rtl:text-right text-gray-500 ">
                        <thead class="thead  ">
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

                            <tr v-for="type in employmentTypes" :key="type.id" class="table-row ">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                                    {{ type.name }}
                                </th>
                                <td class="px-6 py-4">
                                    {{ type.compensation }} %
                                </td>
                                <td class="px-6 py-4 ">
                                    <span v-if="type.fields">
                                        {{type.fields.map(field => field.readName).join(', ')}}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <Link href="#" class="font-medium text-blue-600  hover:underline">
                                    Редактировать
                                    </Link>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div @click="deleteType(type.id)"
                                        class="font-medium cursor-pointer text-red-600  hover:underline">
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
import ToggleSwitch from '../../../../Components/ToggleSwitch.vue';

export default {
    components: {
        Head,
        FormInput,
        Error,
        VueSelect,
        UserLayout,
        ToggleSwitch
    },
    props: {
        employmentTypes: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            form: useForm({
                name: null,
                compensation: 0,
                is_another_recipient: false,
                draftFields: [],
                fields: [],
            }),
            inputTypes: [
                { name: 'Текстовое', value: 'text' },
                { name: 'Число', value: 'number' },
            ],
        }
    },
    methods: {
        deleteType(id) {
            if (confirm('Вы уверены, что хотите удалить?')) {
                router.delete(route('admin.employment-type.destroy', id));
            }
        },
        addField() {
            this.form.draftFields.push({
                name: null,
                readName: null,
                type: 'text',
            });
        },
        removeField() {
            if (this.form.draftFields.length > 0) {
                this.form.draftFields.pop();
            }
        },
        submitForm() {
            this.form.fields = [...this.form.draftFields]

            if (this.form.is_another_recipient) {
                this.form.fields.unshift(
                    { name: 'first_name', readName: 'Имя', type: 'text' },
                    { name: 'last_name', readName: 'Фамилия', type: 'text' },
                    { name: 'surname', readName: 'Отчество', type: 'text' },
                );
            }

            this.form.post(route('admin.employment-type.store'), {

                onSuccess: () => {
                    this.form.reset();
                    this.form.draftFields = [];
                },
                
                onFinish: () => {
                    this.form.fields = [];
                }
            });
        }
    }
}
</script>