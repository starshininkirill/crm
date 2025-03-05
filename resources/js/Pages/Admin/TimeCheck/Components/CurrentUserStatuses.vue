<template>
    <div v-if="!todayReport.length" class="text-2xl font-semibold">
        Информации не найдено
    </div>
    <div v-else class="flex flex-col gap-3">
        <div class="text-xl font-semibold">
            Текущий статус сотрудников
        </div>
        <table
            class="shadow-md max-w-2xl overflow-hidden rounded-md sm:rounded-lg w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3">Сотрудник</th>
                    <th scope="col" class="px-6 py-3 w-36">Статус</th>
                    <th scope="col" class="px-6 py-3">Управление Статусом</th>
                </tr>
            </thead>
            <tbody v-for="mainDepartment in todayReport">
                <tr class="text-xs text-gray-700 text-center uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <td colspan="3" class="px-6 py-3 bg-gray-900 text-white font-semibold">{{
                        mainDepartment.name }}</td>
                </tr>

                <CurrentUserRow v-if="mainDepartment.users" v-for="user in mainDepartment.users" :key="user.id"
                    :user="user" />

                <template v-for="department in mainDepartment.child_departments">
                    <tr
                        class="text-xs text-gray-700 text-center uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                        <td colspan="3" class="px-6 py-3 font-semibold">{{ department.name }}</td>
                    </tr>

                    <CurrentUserRow v-if="department.users" v-for="user in department.users" :key="user.id"
                        :user="user" />
                </template>
            </tbody>
        </table>
    </div>
</template>
<script>
import CurrentUserRow from './CurrentUserRow.vue';

export default {
    components: {
        CurrentUserRow,
    },
    props: {
        todayReport: {
            type: Array,
            required: true,
        }
    },
}


</script>