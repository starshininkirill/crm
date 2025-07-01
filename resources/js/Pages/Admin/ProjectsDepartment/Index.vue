<template>
    <ProjectsLayout>

        <Head title="Отдел Сопровождения" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Отдел Сопровождения</h1>


            <div class="text-2xl font-semibold">
                Тут будет инфомация об отделе
            </div>
        </div>
    </ProjectsLayout>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import ProjectsLayout from '../Layouts/ProjectsLayout.vue';

export default {
    components: {
        Head,
        ProjectsLayout
    },
    props: {
        department: {
            type: Object,
        },
    },
}


</script>