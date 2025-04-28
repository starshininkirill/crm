<template>
    <div v-if="!logReport.length" class="text-2xl font-semibold">
        Информации не найдено
    </div>
    <div v-else class="flex flex-col gap-3">
        <div class="text-xl font-semibold">
            Лог сотрудников
        </div>
        <table
            class="shadow-md overflow-hidden rounded-md sm:rounded-lg w-full text-sm text-left rtl:text-right text-gray-500 ">
            <thead class="thead  ">
                <tr>
                    <th scope="col" class="px-6 py-3">Сотрудник</th>
                    <th scope="col" class="px-6 py-3">Дата действия</th>
                    <th scope="col" class="px-6 py-3">Время действия</th>
                    <th scope="col" class="px-6 py-3">Действие</th>
                </tr>
            </thead>
            <tbody v-for="mainDepartment in logReport">
                <template v-for="user in mainDepartment.users">
                    <tr v-if="user.time_checks && user.time_checks.length > 0" v-for="action in user.time_checks"
                        class="table-row ">
                        <td class="px-6 py-4">
                            <Link :href="route('admin.user.show', user.id)">
                            {{ user.full_name }}
                            </Link>
                        </td>
                        <td class="px-6 py-4 font-semibold">
                            {{ action.formated_date }}
                        </td>
                        <td class="px-6 py-4 font-semibold">
                            {{ action.time }}
                        </td>
                        <td class="px-6 py-4">
                            {{ translateAction(action.action) }}
                        </td>
                    </tr>
                </template>

                <template v-for="department in mainDepartment.child_departments">
                    <template v-for="user in department.users">
                        <tr v-if="user.time_checks && user.time_checks.length > 0" v-for="action in user.time_checks"
                            class="table-row ">
                            <td class="px-6 py-4">
                                <Link :href="route('admin.user.show', user.id)">
                                {{ user.full_name }}
                                </Link>
                            </td>
                            <td class="px-6 py-4 font-semibold">
                                {{ action.formated_date }}
                            </td>
                            <td class="px-6 py-4 font-semibold">
                                {{ action.time }}
                            </td>
                            <td class="px-6 py-4">
                                {{ translateAction(action.action) }}
                            </td>
                        </tr>
                    </template>
                </template>
            </tbody>
        </table>
    </div>
</template>
<script>

export default {
    props: {
        logReport: {
            type: Array,
            required: true,
        }
    },
    methods: {
        translateAction(action) {
            let translations = {
                'start': 'Начало рабочего дня',
                'end': 'Конец рабочего дня',
                'pause': 'Начало перерыва',
                'continue': 'Конец перерыва',
            };
            return translations[action];
        }
    }
}


</script>