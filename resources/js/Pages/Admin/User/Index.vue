<template>

    <Head title="Сотрудники" />
    <div class="contract-page-wrapper flex flex-col">
        <h1 class="text-4xl font-semibold mb-6">Сотрудники</h1>
        <h2 v-if="!users.length" class="text-2xl mb-4">Сотрудники не найдены</h2>
        <div v-if="users.length" class="flex flex-col gap-2">
            <Link :href="route('admin.user.show', {user: user.id})" v-for="user in users" :key="user.id" href="#" class=" border-b pb-2 flex flex-col gap-1">
            <div class=" font-semibold text-xl">
                {{ user.first_name }}
                {{ user.last_name }}
            </div>
            <div>
                {{ user.email }}
            </div>
            <div v-if="user.position" class="div">
                Должность: {{ user.position }}
            </div>
            </Link>

        </div>
    </div>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import UserLayout from '../Layouts/UserLayout.vue';
import { route } from 'ziggy-js';

export default {
    components: {
        Head,
    },
    props: {
        users: {
            type: Array,
        },
    },
    layout: UserLayout,
}


</script>