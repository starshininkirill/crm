<template>
    <TimeCheckLayout>

        <Head title="Time Check" />
        <div class="flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Time Check</h1>
            <div class="flex flex-col gap-6 w-2/3">
                <CurrentUserStatuses :todayReport="todayReport" :workStatuses="workStatuses" />

                <GeneralUserStatuses :dateReport="dateReport" :date="date" />

                <LogReport :logReport="logReport" />
            </div>
        </div>
    </TimeCheckLayout>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import TimeCheckLayout from '../Layouts/TimeCheckLayout.vue';
import CurrentUserStatuses from './Components/CurrentUserStatuses.vue';
import GeneralUserStatuses from './Components/GeneralUserStatuses.vue';
import LogReport from './Components/LogReport.vue'

export default {
    components: {
        Head,
        TimeCheckLayout,
        CurrentUserStatuses,
        GeneralUserStatuses,
        LogReport,
    },
    props: {
        todayReport: {
            type: Array,
            required: true,
        },
        dateReport: {
            type: Array,
            required: true,
        },
        logReport: {
            type: Array,
            required: true,
        },
        workStatuses: {
            type: Array,
            required: true,
        },
        date: {
            type: String,
            required: true,
        }
    },
}


</script>