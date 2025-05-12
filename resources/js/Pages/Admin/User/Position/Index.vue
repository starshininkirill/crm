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
                        <ToggleSwitch class=" col-span-2" v-model="form.has_probation"
                            label="Имеет испытательный срок" />
                    </div>

                    <button class="btn">
                        Создать
                    </button>
                </form>
                <div class=" col-span-2">
                    <h2 v-if="!positions.length" class="text-xl">Должностей не найдено</h2>
                    <div v-else class="flex flex-col gap-5">
                        <table class="table">
                            <thead class="thead  ">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        Название
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Ставка
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Имеет испытательный срок
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="position in positions" :key="position.id" class="table-row ">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                                        {{ position.name }}
                                    </th>
                                    <td class="px-6 py-4">
                                        {{ formatPrice(position.salary) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ position.has_probation ? 'Да' : 'Нет' }}
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
            'has_probation': false,
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
                    th.form.has_probation = false;
                },
            });
        },
    }
}


</script>