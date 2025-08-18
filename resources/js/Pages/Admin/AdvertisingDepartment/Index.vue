<template>
    <AdvertisingDepartmentLayout>

        <Head title="Отдел Рекламы" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Отдел Рекламы</h1>


            <div class="text-2xl font-semibold">
                Тут будет инфомация об отделе
            </div>
        </div>
    </AdvertisingDepartmentLayout>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import AdvertisingDepartmentLayout from '../Layouts/AdvertisingDepartmentLayout.vue';

export default {
    components: {
        Head,
        AdvertisingDepartmentLayout
    },
    props: {
        department: {
            type: Object,
        },
    },
}


</script>