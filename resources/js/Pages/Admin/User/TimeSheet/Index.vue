<template>
    <UserLayout>

        <Head title="Кадровый табель" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Кадровый табель</h1>
        </div>

        <table
            class="shadow-md  overflow-hidden rounded-md sm:rounded-lg w-full text-sm text-left rtl:text-right text-gray-500 ">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50  ">
                <tr>
                    <th scope="col" class="px-3 py-3">
                        Ставка
                    </th>
                    <th scope="col" class="px-3 py-3">
                        Час
                    </th>
                    <th scope="col" class="px-3 py-3">
                        Должность
                    </th>
                    <th scope="col" class="px-3 py-3">
                        ФИО
                    </th>
                    <th v-for="(day, idx) in days" scope="col" class="px-1 py-3">
                        {{ idx }}
                    </th>
                </tr>
            </thead>
        </table>
    </UserLayout>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import UserLayout from '../../Layouts/UserLayout.vue';
import VueSelect from 'vue-select';

export default {
    components: {
        Head,
        UserLayout,
        VueSelect,
    },
    props: {
        days: {
            type: Array,
            required: true,
        }
    },
}


</script>