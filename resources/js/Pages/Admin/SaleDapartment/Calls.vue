<template>
    <SaleDepartmentLayout>

        <Head title="Отчёт по разгороворам" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-medium mb-6">Отчёт по разгороворам</h1>

            <Error />

            <div class=" flex gap-3 mb-4">
                <FormInput @change="changeDate" v-model="date" name="date" type="month" />
            </div>
            <div v-if="error && error != ''" class=" text-2xl font-semibold">
                {{ error }}
            </div>
            <div v-else>
                <table
                    class="relative overflow-hidden border-collapse shadow-md sm:rounded-lg w-full text-sm text-left rtl:text-right text-gray-500">
                    <thead class="text-gray-700 bg-gray-50">
                        <tr>
                            <th scope="col" class="px-1 border-r pl-2 py-2 text-gray-900 font-medium text-xs w-24">
                                Сотрудник
                            </th>
                            <th scope="col"
                                class="px-1 border-r py-2 text-gray-900 font-medium text-xs text-center w-24">
                                Ср. мин. разг. день
                            </th>
                            <th scope="col"
                                class="px-1 border-r py-2 text-gray-900 font-medium text-xs w-24 text-center">
                                Ср. зв. день
                            </th>
                            <th v-for="(value, date) in callsDataByDate" scope="col"
                                class="px-1 border-r text-center py-2 text-gray-900 font-medium text-xs w-14">
                                {{ date }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(numberData, number) in totalNumberValues"
                            class="table-row">
                            <th scope="row"
                                class="px-1 pl-2 py-2 text-gray-900 font-medium border-r text-xs whitespace-nowrap">
                                <template v-if="numberData.user">
                                    {{ numberData.user.last_name }} {{ numberData.user.first_name }}
                                </template>
                            </th>
                            <td class="px-1 py-2 text-gray-900 font-medium border-r text-xs text-center"
                                :class="numberData.middle_value >= callDurationPlan ? 'bg-green-500' : ' bg-red-300'">
                                {{ numberData.middle_value }}
                            </td>
                            <td class="px-1 py-2 text-gray-900 font-medium border-r text-xs text-center"
                                :class="numberData.middle_calls >= callCountPlan ? '' : ' bg-red-300'">
                                {{ numberData.middle_calls }}
                            </td>
                            <td v-for="(dayData, day) in callsDataByDate"
                                class="px-1 py-2 text-gray-900 font-medium border-r text-xs text-center whitespace-nowrap">
                                <template
                                    v-if="dayData[number] && dayData[number].duration && dayData[number].duration > 0">
                                    {{ Math.round(dayData[number].duration) }} ({{ dayData[number].calls }})
                                </template>
                                <template v-else>
                                    0 (0)
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </SaleDepartmentLayout>
</template>

<script>
import { Head, useForm } from '@inertiajs/vue3';
import SaleDepartmentLayout from '../Layouts/SaleDepartmentLayout.vue';
import FormInput from '../../../Components/FormInput.vue';
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';
import Error from '../../../Components/Error.vue';

export default {
    components: {
        Head,
        FormInput,
        Error,
        SaleDepartmentLayout,
    },
    props: {
        callsDataByDate: {
            type: Object
        },
        totalNumberValues: {
            type: Object,
        },
        callDurationPlan: {
            type: Number
        },
        callCountPlan: {
            type: Number
        },
        dateProp: {
            type: String
        },
        error: {
            type: String,
            default: '',
        }
    },
    data() {
        return {
            date: this.dateProp
        }
    },
    methods: {
        changeDate() {
            router.get(route('admin.sale-department.calls'), {
                date: this.date,
            })
        }
    }
}


</script>