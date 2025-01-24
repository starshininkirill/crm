<template>

    <Head title="Личный кабинет" />
    ЛК
</template>
<script>
import { Head } from '@inertiajs/vue3';
import LkLayout from '../../Layouts/LkLayout.vue';

export default {
    components: {
        Head,
    },
    props: {
        departments: {
            type: Array,
        },
    },
    layout: LkLayout,
}


</script>