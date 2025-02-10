<template>

    <Head title="Отчёт по разгороворам" />
    <div class="contract-page-wrapper flex flex-col">
        <h1 class="text-4xl font-semibold mb-6">Отчёт по разгороворам</h1>

        <ul v-if="$page.props.errors && $page.props.errors.length" class="flex flex-col gap-1 mb-4">
            <li v-for="(error, index) in $page.props.errors" :key="index" class="text-red-400">{{ error }}</li>
        </ul>

        <div>
            <table
                class="relative overflow-x-auto shadow-md sm:rounded-lg w-full text-sm text-left rtl:text-right text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Сотрудник
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Ср. мин. разг. день
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Ср. зв. день
                        </th>
                        <th scope="col" class="px-6 py-3 text-right w-12">
                            1
                        </th>
                        <th scope="col" class="px-6 py-3 text-right w-12">
                            2
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="stat in stats" :key="stat.id" class="bg-white border-b hover:bg-gray-50">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            {{ stat.first_name  }}  {{ stat.last_name }}
                        </th>
                        <td class="px-6 py-4">
                            
                        </td>
                        <td class="px-6 py-4">

                        </td>
                        <td class="px-6 py-4 text-right">

                        </td>
                        <td class="px-6 py-4 text-right">

                        </td>
                    </tr>
                </tbody>
            </table>
        </div>


    </div>
</template>

<script>
import { Head, useForm } from '@inertiajs/vue3';
import SaleDepartmentLayout from '../Layouts/SaleDepartmentLayout.vue';
import FormInput from '../../../Components/FormInput.vue';

export default {
    components: {
        Head,
        FormInput
    },
    props: {
        stats: {
            type: Array,
        }
    },
    layout: SaleDepartmentLayout,
}


</script>