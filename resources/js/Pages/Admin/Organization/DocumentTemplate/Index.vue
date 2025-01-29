<template>

    <Head title="Шаблоны документов" />
    <div class="contract-page-wrapper flex flex-col">
        <h1 class="text-4xl font-semibold mb-6">Шаблоны документов</h1>

    </div>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import OrganizationLayout from '../../Layouts/OrganizationLayout.vue';

export default {
    components: {
        Head
    },
    props: {
        documetTemplates: {
            type: Array,
        },
    },
    layout: OrganizationLayout,

}

</script>