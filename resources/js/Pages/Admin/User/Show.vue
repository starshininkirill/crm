<template>

    <Head title="Сотрудник" />
    <div class="contract-page-wrapper flex flex-col">
        <h1 class="text-4xl font-semibold mb-6">Сотрудник: {{ user.first_name }} {{ user.last_name }}</h1>
    </div>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import UserLayout from '../Layouts/UserLayout.vue';

export default {
    components: {
        Head,
    },
    props: {
        user: {
            type: Object,
        },
    },
    layout: UserLayout,
}


</script>