<template>
    <TimeCheckLayout>

        <Head title="Time Check" />
        <div class="flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Time Check</h1>
            <div v-if="!mainDepartments.length" class="text-2xl font-semibold">
                Информации не найдено
            </div>
            <div v-else>
                <table
                    class="shadow-md max-w-xl overflow-hidden rounded-md sm:rounded-lg w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Сотрудник</th>
                            <th scope="col" class="px-6 py-3">Статус</th>
                            <th scope="col" class="px-6 py-3">Управление Статусом</th>
                        </tr>
                    </thead>
                    <tbody v-for="mainDepartment in mainDepartments">
                        <tr
                            class="text-xs text-gray-700 text-center uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <td colspan="3" class="px-6 py-3 bg-gray-900 text-white font-semibold">{{ mainDepartment.name }}</td>
                        </tr>

                        <tr v-if="mainDepartment.users" v-for="user in mainDepartment.users" :key="user.id"
                        class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4">{{ user.full_name }}</td>
                            <td class="px-6 py-4">{{ user.status }}</td>
                            <td class="px-6 py-4">
                                <button >Изменить статус</button>
                            </td>
                        </tr>

                        <template v-for="department in mainDepartment.child_departments">
                            <tr
                                class="text-xs text-gray-700 text-center uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <td colspan="3" class="px-6 py-3 font-semibold">{{ department.name }}</td>
                            </tr>
                            <tr v-for="user in department.users" :key="user.id"
                            class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-4">{{ user.full_name }}</td>
                                <td class="px-6 py-4">{{ user.status }}</td>
                                <td class="px-6 py-4">
                                    <button >Изменить статус</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </TimeCheckLayout>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import TimeCheckLayout from '../Layouts/TimeCheckLayout.vue';

export default {
    components: {
        Head,
        TimeCheckLayout
    },
    props: {
        mainDepartments: {
            type: Array,
            required: true,
        }
    }
}


</script>