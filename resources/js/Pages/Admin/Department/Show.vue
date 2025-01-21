<template>

    <Head title="Отдел" />
    <div class="contract-page-wrapper flex flex-col">
        <h1 class="text-4xl font-semibold mb-6">{{ department.name }}</h1>

    </div>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import DepartmentLayout from '../Layouts/DepartmentLayout.vue';

export default {
    components: {
        Head,
    },
    props: {
        department: {
            type: Array,
        },
    },
    layout: DepartmentLayout,
}


</script>