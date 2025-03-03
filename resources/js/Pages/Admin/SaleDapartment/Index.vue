<template>
    <SaleDepartmentLayout>

        <Head title="Отдел продаж" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Отдел продаж</h1>


            <div class="text-2xl font-semibold">
                Тут будет инфомация об отделе
            </div>
        </div>
    </SaleDepartmentLayout>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import SaleDepartmentLayout from '../Layouts/SaleDepartmentLayout.vue';

export default {
    components: {
        Head,
        SaleDepartmentLayout
    },
    props: {
        department: {
            type: Object,
        },
    },
}


</script>