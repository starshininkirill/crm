<template>
    <UserLayout>
    <Head title="Сотрудник" />
    <div class="contract-page-wrapper flex flex-col">
        <h1 class="text-4xl font-semibold mb-6">Сотрудник: {{ user.first_name }} {{ user.last_name }}</h1>
    </div>
    </UserLayout>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import UserLayout from '../Layouts/UserLayout.vue';

export default {
    components: {
        Head,
        UserLayout
    },
    props: {
        user: {
            type: Object,
        },
    },
}


</script>