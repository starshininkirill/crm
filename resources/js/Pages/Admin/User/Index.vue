<template>
    <UserLayout>

        <Head title="Сотрудники" />
        <div class="flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Сотрудники</h1>
            <div class="grid grid-cols-3 gap-8">
                <CreateForm :positions="positions" :departments="departments" :employmentTypes="employmentTypes" />
                <div class=" col-span-2">
                    <div class="flex items-center gap-3 mb-4 max-w-64">
                        <VueSelect v-model="filtredDepartment" :options="departmentOptions"
                            :reduce="department => department.id" label="name" class="full-vue-select min-w-[360px]" />
                        <input v-model="search" type="text" class="input min-w-[300px]" placeholder="Поиск...">
                        <div class="btn !w-fit" @click="resetFilter">
                            Сбросить
                        </div>
                    </div>
                    <h2 v-if="!users.data.length" class="text-xl">Сотрудников не найдено</h2>
                    <div v-else class="flex flex-col gap-5">
                        <table class="table">
                            <thead class="thead">
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
                                        Действия
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users.data" :key="user.id" class="table-row">
                                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
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
                                        <button v-if="!user.fired_at" @click="fire(user.id)"
                                            class="font-medium  hover:underline hover:text-red-600">
                                            Уволить
                                        </button>
                                        <div v-else class="font-medium text-red-600">
                                            Уволен
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <Pagination :links="users.links" />
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
import Pagination from '../../../Components/Pagination.vue';

export default {
    components: {
        Head,
        CreateForm,
        VueSelect,
        UserLayout,
        Pagination
    },
    props: {
        users: {
            type: Object,
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
        filters: {
            type: Object,
            default: () => ({})
        }
    },
    data() {
        return {
            filtredDepartment: null,
            search: this.filters.name || '',
            searchTimeout: null,
            departmentOptions: [
                { id: null, name: 'Все отделы' },
                ...this.departments
            ],
        }
    },
    methods: {
        updateUsers(selectedDepartmentId) {
            router.get(route('admin.user.index'), {
                'department': selectedDepartmentId,
            });
        },
        fire(userId) {
            if (confirm('Вы уверены, что хотите уволить этого сотрудника?')) {
                router.post(route('admin.user.fire', userId));
            }
        },
        updateUsers() {
            const params = {};
            if (this.filtredDepartment !== null) params.department = this.filtredDepartment;
            if (this.search !== null) params.name = this.search;

            router.get(route('admin.user.index'), params, {
                preserveState: true,
                preserveScroll: true,
                replace: true
            });
        },
        handleSearchInput() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.updateUsers();
            }, 300);
        },
        resetFilter() {
            this.search = '';
            this.filtredDepartment = null;
        }
    },
    watch: {
        filtredDepartment(newVal, oldVal) {
            if (newVal !== oldVal) {
                this.updateUsers();
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