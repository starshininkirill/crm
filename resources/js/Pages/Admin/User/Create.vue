<template>

    <Head title="Создать сотруднки" />
    <div class="contract-page-wrapper flex flex-col">
        <h1 class="text-4xl font-semibold mb-6">Создать сотруднки</h1>

        <div class="text-2xl font-semibold">
            Форма создания сотрудника
        </div>
    </div>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import UserLayout from '../Layouts/UserLayout.vue';

export default {
    components: {
        Head,
    },
    layout: UserLayout,
}


</script>