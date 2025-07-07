<template>
    <ProjectsLayout>

        <Head title="Отчёт отдела Сопровождения" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Отчёт отдела Сопровождения</h1>

            <SelectForm :initial-date="date" />

            <Error />

            <table v-if="report.length != 0" class="overflow-hidden table table-fixed mb-10">
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
                            Допродажи
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
                            % от ДЗ
                        </th>
                        <th scope="col" class="px-3 py-2 text-center border-x">
                            % от допродаж
                        </th>
                        <th scope="col" class="px-3 py-2 text-center border-x">
                            Итого бонусов
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <template v-for="row in report" :key="row.user.id">
                        <tr class="table-row">
                            <th scope="row"
                                class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap  border-x cursor-pointer hover:bg-gray-200"
                                @click="toggleDetails(row)">
                                {{ row.user.full_name }}
                            </th>
                            <td class="px-4 py-2 text-center border-r">
                                {{ formatPrice(row.accounts_receivable_sum) }}
                            </td>
                            <td class="px-4 py-2 text-center border-r">
                                {{ row.percent_ladder }} %
                            </td>
                            <td class="px-4 py-2 text-center border-r">
                                {{ formatPrice(row.upsells_money) }}
                            </td>
                            <td class="px-4 py-2 text-center border-r">
                                {{ row.close_contracts_count }}
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
                                {{ formatPrice(row.accounts_receivable_percent) }}
                            </td>
                            <td class="px-4 py-2 text-center border-r">
                                {{ formatPrice(row.upsells_bonus) }}
                            </td>
                            <td class="px-4 py-2 text-center border-r">
                                {{ formatPrice(row.bonuses) }}
                            </td>
                        </tr>
                        <tr v-if="detailedReportUser && detailedReportUser.user.id === row.user.id">
                            <td colspan="15" class="p-2 bg-gray-100">
                                <UserReport :user-report="detailedReportUser" />
                            </td>

                        </tr>
                    </template>
                </tbody>
            </table>

            <UserReport v-if="false" :user-report="userReport" />
        </div>
    </ProjectsLayout>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import ProjectsLayout from '../Layouts/ProjectsLayout.vue';
import SelectForm from './Components/SelectForm.vue';
import Error from '../../../Components/Error.vue';
import UserReport from './Components/UserReport.vue';

export default {
    components: {
        Head,
        ProjectsLayout,
        SelectForm,
        Error,
        UserReport,
    },
    props: {
        department: {
            type: Object,
        },
        date: {
            type: String,
        },
        report: {
            type: Object,
        },
        userReport: {
            type: Object,
            default: null,
        },
    },
    data() {
        return {
            detailedReportUser: this.userReport,
        }
    },
    methods: {
        toggleDetails(userRow) {
            if (this.detailedReportUser && this.detailedReportUser.user.id === userRow.user.id) {
                this.detailedReportUser = null;
            } else {
                this.detailedReportUser = userRow;
            }
        },
    }
}


</script>