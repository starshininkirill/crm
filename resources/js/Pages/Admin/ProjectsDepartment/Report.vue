<template>
    <ProjectsLayout>

        <Head title="Отчёт отдела Сопровождения" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Отчёт отдела Сопровождения</h1>

            <SelectForm :users="users" :initial-user="selectUser" :initial-date="date" />

            <Error />

            <table v-if="report.length != 0" class="overflow-hidden table table-fixed">
                <thead class="thead border-b">
                    <tr>
                        <th scope="col" class="px-3 py-2 border-x w-60">
                            Сотрудник
                        </th>
                        <th scope="col" class="px-3 py-2 text-center border-x">
                            Дз
                        </th>
                        <th scope="col" class="px-3 py-2 text-center border-x">
                            %
                        </th>
                        <th scope="col" class="px-3 py-2 text-center border-x">
                            % от ДЗ
                        </th>
                        <th scope="col" class="px-3 py-2 text-center border-x">
                            Допродажи
                        </th>
                        <th scope="col" class="px-3 py-2 text-center border-x">
                            % от допродаж
                        </th>
                        <th scope="col" class="px-3 py-2 text-center border-x">
                            Закрыто проектов
                        </th>
                        <th scope="col" class="px-3 py-2 text-center border-x">
                            Уник
                        </th>
                        <th scope="col" class="px-3 py-2 text-center border-x">
                            Готовый
                        </th>
                        <th scope="col" class="px-3 py-2 text-center border-x">
                            Комплекс
                        </th>
                        <th scope="col" class="px-3 py-2 text-center border-x">
                            Б1
                        </th>
                        <th scope="col" class="px-3 py-2 text-center border-x">
                            Б2
                        </th>
                        <th scope="col" class="px-3 py-2 text-center border-x">
                            Б3
                        </th>
                        <th scope="col" class="px-3 py-2 text-center border-x">
                            Б4
                        </th>
                        <th scope="col" class="px-3 py-2 text-center border-x">
                            Итого бонусов
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in report" :key="row.user.full_name" class="table-row ">
                        <th scope="row" class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap  border-x">
                            {{ row.user.full_name }}
                        </th>
                        <td class="px-4 py-2 text-center border-r">
                            {{ formatPrice(row.accounts_receivable) }}
                        </td>
                        <td class="px-4 py-2 text-center border-r">
                            {{ row.percent_ladder }} %
                        </td>
                        <td class="px-4 py-2 text-center border-r">
                            {{ formatPrice(row.accounts_receivable_percent) }}
                        </td>
                        <td class="px-4 py-2 text-center border-r">
                            {{ formatPrice(row.upsells) }}
                        </td>
                        <td class="px-4 py-2 text-center border-r">
                            {{ formatPrice(row.upsells_bonus) }}
                        </td>
                        <td class="px-4 py-2 text-center border-r">
                            {{ row.close_contracts }}
                        </td>
                        <td class="px-4 py-2 text-center border-r">
                            {{ row.individual_sites }}
                        </td>
                        <td class="px-4 py-2 text-center border-r">
                            {{ row.ready_sites }}
                        </td>
                        <td class="px-4 py-2 text-center border-r">
                            {{ row.compexes }}
                        </td>
                        <td class="px-4 py-2 text-center border-r font-semibold"
                            :class="{ 'text-green-500': row.b1.completed, 'text-red-500': !row.b1.completed }">
                            {{ row.b1.completed ? 'Да' : 'Нет' }}
                        </td>
                        <td class="px-4 py-2 text-center border-r font-semibold"
                            :class="{ 'text-green-500': row.b2.completed, 'text-red-500': !row.b2.completed }">
                            {{ row.b2.completed ? 'Да' : 'Нет' }}
                        </td>
                        <td class="px-4 py-2 text-center border-r font-semibold"
                            :class="{ 'text-green-500': row.b3.completed, 'text-red-500': !row.b3.completed }">
                            {{ row.b3.completed ? 'Да' : 'Нет' }}
                        </td>
                        <td class="px-4 py-2 text-center border-r font-semibold"
                            :class="{ 'text-green-500': row.b4.completed, 'text-red-500': !row.b4.completed }">
                            {{ row.b4.completed ? 'Да' : 'Нет' }}
                        </td>
                        <td class="px-4 py-2 text-center border-r">
                            {{ formatPrice(row.bonuses) }}
                        </td>
                    </tr>
                </tbody>
            </table>


        </div>
    </ProjectsLayout>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import ProjectsLayout from '../Layouts/ProjectsLayout.vue';
import SelectForm from './Components/SelectForm.vue';
import Error from '../../../Components/Error.vue';

export default {
    components: {
        Head,
        ProjectsLayout,
        SelectForm,
        Error
    },
    props: {
        department: {
            type: Object,
        },
        users: {
            type: Array,
        },
        selectUser: {
            type: Object,
        },
        date: {
            type: String,
        },
        report: {
            type: Array,
        },
    },
}


</script>