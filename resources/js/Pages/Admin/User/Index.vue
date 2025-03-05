<template>
    <UserLayout>

        <Head title="Сотрудники" />
        <div class="flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Сотрудники</h1>
            <div class="grid grid-cols-3 gap-8">
                <CreateForm :positions="positions" :departments="departments" :employmentTypes="employmentTypes" />
                <div class=" col-span-2">
                    <!-- <div class="max-w-xs mb-4">
                    <div class="label">
                        Отдел
                    </div>
                    <VueSelect :options="departments" :reduce="department => department.id" label="name"
                        @update:model-value="updateUsers" />
                </div> -->
                    <h2 v-if="!users.length" class="text-xl">Сотрудников не найдено</h2>
                    <div v-else class="flex flex-col gap-5">
                        <table
                            class="shadow-md  overflow-hidden rounded-md sm:rounded-lg w-full text-sm text-left rtl:text-right text-gray-500 ">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50  ">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        Имя
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Почта
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Должность
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Отдел
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Дата устройства
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users" :key="user.id" class="bg-white border-b   hover:bg-gray-50 ">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                                        <Link :href="route('admin.user.show', user.id)">
                                        {{ user.full_name }}
                                        </Link>
                                    </th>
                                    <td class="px-6 py-4">
                                        {{ user.email }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ user.position?.name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ user.department?.name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ user.formatted_created_at }}
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
import { Head } from '@inertiajs/vue3';
import UserLayout from '../Layouts/UserLayout.vue';
import CreateForm from './Components/CreateForm.vue';
import VueSelect from 'vue-select';
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

export default {
    components: {
        Head,
        CreateForm,
        VueSelect,
        UserLayout
    },
    props: {
        users: {
            type: Array,
        },
        positions: {
            type: Array,
            required: true,
        },
        departments: {
            type: Array,
            required: true,
        },
        employmentTypes: {
            type: Array,
            required: true,
        },
    },
    methods: {
        updateUsers(selectedDepartmentId) {
            router.get(route('admin.user.index'), {
                'department': selectedDepartmentId,
            });
        }
    }
}


</script>