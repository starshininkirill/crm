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
                                class="px-2 py-2 font-medium text-gray-900 whitespace-nowrap  border-x">
                                {{ row.user.full_name }}
                            </th>
                            <td class="px-2 py-2 text-center border-r font-semibold">
                                {{ formatPrice(row.upsells) }}
                            </td>
                            <td class="px-2 py-2 text-center border-r font-semibold">
                                {{ formatPrice(row.accounts_receivable) }}
                            </td>
                            <td class="px-2 py-2 text-center border-r font-semibold"
                                :class="{ 'text-green-500': row.b1.completed, 'text-red-500': !row.b1.completed }">
                                {{ formatPrice(row.b1.bonus) }}
                                <br>
                                ({{ row.b1.completed_count }} / {{ row.b1.employees_count }})
                            </td>
                            <td class="px-2 py-2 text-center border-r font-semibold"
                                :class="{ 'text-green-500': row.b2.completed, 'text-red-500': !row.b2.completed }">
                                {{ formatPrice(row.b2.bonus) }} 
                                <br>
                                ({{ row.b2.completed_count }} / {{ row.b2.employees_count }})
                            </td>
                            <td class="px-2 py-2 text-center border-r font-semibold">
                                {{ formatPrice(row.b3.bonus) }}
                                <br>
                                <span class="text-sm text-gray-500">
                                   ({{ row.b3.completed_count }} / {{ row.b3.employees_count }})
                                </span>
                            </td>
                            <td class="px-2 py-2 text-center border-r font-semibold"
                                :class="{ 'text-green-500': row.b4.completed, 'text-red-500': !row.b4.completed }">
                                {{ formatPrice(row.b4.bonus) }}
                                <br>
                                ({{ row.b4.completed_count }} / {{ row.b4.employees_count }})
                            </td>
                            <td class="px-2 py-2 text-center border-r font-semibold">
                                {{ formatPrice(row.upsales_bonus.bonus) }} 
                                <br>
                                ({{ formatPrice(row.upsales_bonus.upsales) }} / {{ formatPrice(row.upsales_bonus.goal) }})
                            </td>
                            <td class="px-2 py-2 text-center border-r font-semibold">
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