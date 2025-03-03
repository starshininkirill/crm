<template>
    <DepartmentLayout>
    <Head title="Создать отдел" />
    <div class="contract-page-wrapper flex flex-col">
        <h1 class="text-4xl font-semibold mb-6">Создать отдел</h1>

        <h2 class="text-2xl font-semibold">Форма создания отдела</h2>
    </div>
    </DepartmentLayout>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import DepartmentLayout from '../Layouts/DepartmentLayout.vue';

export default {
    components: {
        Head,
        DepartmentLayout
    },

}


</script>