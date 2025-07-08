<template>
    <ProjectsLayout>

        <Head title="Отчёт руководителя Сопровождения" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Отчёт руководителя отдела Сопровождения</h1>

            <SelectForm :initial-date="date" :target-url="route('admin.projects-department.head-report')" />

            <Error />

            <table v-if="report.length != 0" class="overflow-hidden table table-fixed mb-10">
                <thead class="thead border-b">
                    <tr>
                        <th scope="col" class="px-3 py-2 border-x w-60">
                            Сотрудник
                        </th>
                        <th scope="col" class="px-3 py-2 text-center border-x">
                            Допродажи
                        </th>
                        <th scope="col" class="px-3 py-2 text-center border-x">
                            ДЗ
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
                    <template v-for="row in report" :key="row.user.id">
                        <tr class="table-row">
                            <th scope="row"
                                class="px-4 py-2 font-medium text-gray-900 whitespace-nowrap  border-x">
                                {{ row.user.full_name }}
                            </th>
                            <td class="px-4 py-2 text-center border-r font-semibold">
                                {{ formatPrice(row.upsells) }}
                            </td>
                            <td class="px-4 py-2 text-center border-r font-semibold">
                                {{ formatPrice(row.accounts_receivable) }}
                            </td>
                            <td class="px-4 py-2 text-center border-r font-semibold"
                                :class="{ 'text-green-500': row.b1.completed, 'text-red-500': !row.b1.completed }">
                                {{ row.b1.completed_count }} / {{ row.b1.employees_count }}
                            </td>
                            <td class="px-4 py-2 text-center border-r font-semibold"
                                :class="{ 'text-green-500': row.b2.completed, 'text-red-500': !row.b2.completed }">
                                {{ row.b2.completed_count }} / {{ row.b2.employees_count }}
                            </td>
                            <td class="px-4 py-2 text-center border-r font-semibold">
                                -
                            </td>
                            <td class="px-4 py-2 text-center border-r font-semibold"
                                :class="{ 'text-green-500': row.b4.completed, 'text-red-500': !row.b4.completed }">
                                {{ row.b4.completed_count }} / {{ row.b4.employees_count }}
                            </td>
                            <td class="px-4 py-2 text-center border-r">
                                {{ formatPrice(row.bonuses) }}
                            </td>
                        </tr>
                    </template>
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
        Error,
    },
    props: {
        date: {
            type: String,
        },
        report: {
            type: Object,
        },
    },
}


</script>