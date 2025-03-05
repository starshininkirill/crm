<template>
    <UserLayout>

        <Head title="Должности" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Должности</h1>

            <div class="grid grid-cols-3 gap-8">
                <form @submit.prevent="submitForm" class="flex flex-col gap-3">
                    <div class="grid grid-cols-2 gap-4">
                        <FormInput type="text" required v-model="form.name" name="name" label="Название"
                            placeholder="Название" />
                        <FormInput type="number" v-model="form.salary" name="salary" label="Ставка"
                            placeholder="Ставка" />
                    </div>

                    <div class="flex flex-col">
                        <span class="label">
                            Отдел
                        </span>
                        <VueSelect v-model="form.department_id" :reduce="department => department.id" label="name"
                            :options="departments">
                        </VueSelect>
                    </div>

                    <button class="btn">
                        Создать
                    </button>
                </form>
                <div class=" col-span-2">
                    <h2 v-if="!positions.length" class="text-xl">Должностей не найдено</h2>
                    <div v-else class="flex flex-col gap-5">
                        <table
                            class="shadow-md  overflow-hidden rounded-md sm:rounded-lg w-full text-sm text-left rtl:text-right text-gray-500 ">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50  ">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        Название
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Ставка
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Отдел
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="position in positions" :key="position.id"
                                    class="bg-white border-b   hover:bg-gray-50 ">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                                        {{ position.name }}
                                    </th>
                                    <td class="px-6 py-4">
                                        {{ formatPrice(position.salary) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ position.department.name }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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
        departments: {
            type: Array,
            required: true,
        },
        positions: {
            type: Array,
            required: true,
        }
    },
    data() {
        let form = useForm({
            'name': null,
            'salary': null,
            'department_id': null,
        })
        return {
            form
        }
    },
    methods: {
        submitForm() {
            let th = this;

            this.form.post(route('admin.position.store'), {
                onSuccess() {
                    th.form.name = null;
                    th.form.salary = null;
                    th.form.department_id = null;
                },
            });
        },
    }
}


</script>