<template>

    <Head title="Создать должность" />
    <div class="contract-page-wrapper flex flex-col">
        <h1 class="text-4xl font-semibold mb-6">Создать должность</h1>

        <h2 class="text-2xl font-semibold">Форма создания должностьи</h2>
    </div>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import DepartmentLayout from '../../Layouts/DepartmentLayout.vue';

export default {
    components: {
        Head,
    },

    layout: DepartmentLayout,
}


</script>