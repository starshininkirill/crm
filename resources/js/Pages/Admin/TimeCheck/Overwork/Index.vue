<template>
    <TimeCheckLayout>

        <Head title="Переработки" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Переработки</h1>
        </div>

        <h2 v-if="!overworks.length" class="text-2xl font-semibold">
            Переработок не найдено
        </h2>
        <table v-else
            class="shadow-md overflow-hidden rounded-md sm:rounded-lg w-full text-sm text-left rtl:text-right text-gray-500 ">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50  ">
                <tr>
                    <th scope="col" class="px-6 py-3 w-32">
                        Дата
                    </th>
                    <th scope="col" class="px-6 py-3 w-16">
                        Часы
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Сотрудник
                    </th>

                    <th scope="col" class="px-6 py-3 flex-grow">
                        Описание
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Действия
                    </th>
                </tr>
            </thead>
            <tbody>

                <tr v-for="overwork in overworks" :key="overwork.id" class="bg-white border-b   hover:bg-gray-50 ">
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ overwork.date }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        {{ overwork.hours }}
                    </td>
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                        {{ overwork.user?.full_name }}
                    </th>
                    <td class="px-6 py-4 w-full">
                        {{ overwork.description }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap flex gap-3 items-center">
                        <button @click="acceptOverwork(overwork.id)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="#4CAF50" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-check">
                                <path d="M20 6L9 17l-5-5" />
                            </svg>
                        </button>
                        <button @click="rejectOverwork(overwork.id)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="#F44336" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-x">
                                <line x1="18" y1="6" x2="6" y2="18" />
                                <line x1="6" y1="6" x2="18" y2="18" />
                            </svg>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

    </TimeCheckLayout>
</template>

<script>
import { Head, router } from '@inertiajs/vue3';
import TimeCheckLayout from '../../Layouts/TimeCheckLayout.vue';

export default {
    components: {
        Head,
        TimeCheckLayout,
    },
    props: {
        overworks: {
            type: Object,
            required: true,
        },
    },
    methods:{
        acceptOverwork(overworkId){
            router.post(route('admin.time-check.overwork.accept', {overwork: overworkId}));
        },
        rejectOverwork(overworkId){
            router.post(route('admin.time-check.overwork.reject'));
        },
    }
}


</script>