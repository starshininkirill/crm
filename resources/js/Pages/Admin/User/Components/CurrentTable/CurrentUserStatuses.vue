<template>
    <div v-if="!todayReport.length" class="text-2xl font-semibold">
        Информации не найдено
    </div>
    <div v-else class="flex flex-col gap-3">
        <Error />
        <table class="table">
            <thead class="thead">
                <tr>
                    <th scope="col" class="px-2 py-2 w-48 border-x">Сотрудник</th>
                    <th scope="col" class="px-2 py-2 border-r">Статус</th>
                    <th scope="col" class="px-2 py-2 border-r">Начало дня</th>
                    <th scope="col" class="px-2 py-2 border-r">Конец дня</th>
                    <th scope="col" class="px-2 py-2 border-r">Рабочее время</th>
                    <th scope="col" class="px-2 py-2 border-r">Перерывы</th>
                    <th scope="col" class="px-2 py-2 w-64 border-x">Управление Статусом</th>
                </tr>
            </thead>
            <tbody v-for="mainDepartment in todayReport">
                <tr class="text-xs text-gray-700 text-center uppercase bg-gray-50">
                    <td colspan="7" class="px-2 py-2 bg-gray-900 text-white font-semibold">{{
                        mainDepartment.name }}</td>
                </tr>

                <UserRow v-if="mainDepartment.users" v-for="user in mainDepartment.users" :key="user.id"
                    :workStatuses="workStatuses" :user="user" :date="date" />

                <template v-for="department in mainDepartment.child_departments">
                    <tr class="text-xs text-gray-700 text-center uppercase bg-gray-200">
                        <td colspan="7" class="px-2 py-2 font-semibold">{{ department.name }}</td>
                    </tr>

                    <UserRow v-if="department.users" v-for="user in department.users" :key="user.id"
                        :workStatuses="workStatuses" :user="user" :date="date" />
                </template>
            </tbody>
        </table>
    </div>
</template>
<script>
import Error from '../../../../../Components/Error.vue';
import UserRow from './UserRow.vue';

export default {
    components: {
        UserRow,
        Error
    },
    props: {
        todayReport: {
            type: Array,
            required: true,
        },
        workStatuses: {
            type: Array,
            required: true,
        },
        date: {
            type: String,
            required: true,
        }
    },
}


</script>