<template>
    <ProjectsLayout>

        <Head title="Отчёт руководителя Сопровождения" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Отчёт руководителя отдела Сопровождения</h1>

            <SelectForm :initial-date="date" />

            <Error />
        </div>
    </ProjectsLayout>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import ProjectsLayout from '../Layouts/ProjectsLayout.vue';
import SelectForm from './Components/SelectForm.vue';
import Error from '../../../Components/Error.vue';

export default {
    components: {
        Head,
        ProjectsLayout,
        SelectForm,
        Error,
    },
    props: {
        date: {
            type: String,
        },
    },
}


</script>