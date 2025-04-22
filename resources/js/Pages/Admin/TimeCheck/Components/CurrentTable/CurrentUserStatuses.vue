<template>
    <div v-if="!todayReport.length" class="text-2xl font-semibold">
        Информации не найдено
    </div>
    <div v-else class="flex flex-col gap-3">
        <div class="text-xl font-semibold">
            Текущий статус сотрудников
        </div>
        <Error />
        <table class="shadow-md rounded-md sm:rounded-lg w-full text-sm text-left rtl:text-right text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50  ">
                <tr>
                    <th scope="col" class="px-4 py-3 w-56 border-x">Сотрудник</th>
                    <th scope="col" class="px-4 py-3 border-r">Статус</th>
                    <th scope="col" class="px-4 py-3 border-r">Начало рабочего дня</th>
                    <th scope="col" class="px-4 py-3 border-r">Конец рабочего дня</th>
                    <th scope="col" class="px-4 py-3 border-r">Рабочее время</th>
                    <th scope="col" class="px-4 py-3 border-r">Время перерывов</th>
                    <th scope="col" class="px-4 py-3 w-72 border-x">Управление Статусом</th>
                </tr>
            </thead>
            <tbody v-for="mainDepartment in todayReport">
                <tr class="text-xs text-gray-700 text-center uppercase bg-gray-50  ">
                    <td colspan="7" class="px-4 py-3 bg-gray-900 text-white font-semibold">{{
                        mainDepartment.name }}</td>
                </tr>

                <UserRow v-if="mainDepartment.users" v-for="user in mainDepartment.users" :key="user.id"
                    :workStatuses="workStatuses" :user="user" :date="date" />

                <template v-for="department in mainDepartment.child_departments">
                    <tr class="text-xs text-gray-700 text-center uppercase bg-gray-200  ">
                        <td colspan="7" class="px-4 py-3 font-semibold">{{ department.name }}</td>
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