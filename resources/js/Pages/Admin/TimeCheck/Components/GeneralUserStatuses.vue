<template>

    <div class="flex flex-col gap-3">
        <div class="flex flex-col gap-3">
            <FormInput v-model="reactiveDate" type="date" class="!w-fit" name="date" label="Дата отображания информации"
                @change="updateDate" />
            <div class="text-xl font-semibold">
                Общий статус сотрудников на {{ date }}
            </div>
            <div v-if="!dateReport.length" class="text-2xl font-semibold">
                Информации не найдено
            </div>
            <table v-else
                class="shadow-md w-2/3 overflow-hidden rounded-md sm:rounded-lg  text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Сотрудник</th>
                        <th scope="col" class="px-6 py-3 w-36">Дата</th>
                        <th scope="col" class="px-6 py-3">Начало рабочего дня</th>
                        <th scope="col" class="px-6 py-3">Конец рабочего дня</th>
                        <th scope="col" class="px-6 py-3">Рабочее время</th>
                        <th scope="col" class="px-6 py-3">Время перерывов</th>
                    </tr>
                </thead>
                <tbody v-for="mainDepartment in dateReport">
                    <tr
                        class="text-xs text-gray-700 text-center uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <td colspan="6" class="px-6 py-3 bg-gray-900 text-white font-semibold">{{
                            mainDepartment.name }}</td>
                    </tr>
                    <GeneralUserRow v-if="mainDepartment.users" v-for="user in mainDepartment.users" :key="user.id"
                        :user="user" :date="date" />

                    <template v-for="department in mainDepartment.child_departments">
                        <tr
                            class="text-xs text-gray-700 text-center uppercase bg-gray-200 dark:bg-gray-700 dark:text-gray-400">
                            <td colspan="6" class="px-6 py-3 font-semibold">{{ department.name }}</td>
                        </tr>
                        <GeneralUserRow v-if="department.users" v-for="user in department.users" :key="user.id"
                        :user="user" :date="date" />
                    </template>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import FormInput from '../../../../Components/FormInput.vue';
import GeneralUserRow from './GeneralUserRow.vue';
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

export default {
    components: {
        FormInput,
        GeneralUserRow
    },
    props: {
        dateReport: {
            type: Array,
            required: true,
        },
        date: {
            type: String,
            required: true,
        }
    },
    data() {
        return {
            reactiveDate: this.date
        }
    },
    methods: {
        updateDate(input) {
            router.get(route('admin.time-check.index'), {
                date: this.reactiveDate,
            })
        },
    }
}

</script>