<template>
    <TimeCheckLayout>

        <Head title="Time Check" />
        <div class="flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Time Check</h1>
            <div class="flex flex-col gap-6 w-3/4">
                <div>
                    <div class="label">
                        Дата отображения
                    </div>
                    <VueDatePicker v-model="reactiveDate" class="w-fit" :auto-apply="true" format="yyyy-MM-dd"
                        model-type="yyyy-MM-dd" date locale="ru" @update:modelValue="updateDate" />
                </div>

                <CurrentUserStatuses :date="date" :todayReport="todayReport" :workStatuses="workStatuses" />

                <LogReport :logReport="logReport" />
            </div>
        </div>
    </TimeCheckLayout>
</template>

<script>
import { Head, router } from '@inertiajs/vue3';
import TimeCheckLayout from '../Layouts/TimeCheckLayout.vue';
import CurrentUserStatuses from './Components/CurrentTable/CurrentUserStatuses.vue';
import LogReport from './Components/LogReport.vue'
import { route } from 'ziggy-js';
import VueDatePicker from '@vuepic/vue-datepicker';

export default {
    components: {
        Head,
        TimeCheckLayout,
        CurrentUserStatuses,
        LogReport,
        VueDatePicker
    },
    props: {
        todayReport: {
            type: Array,
            required: true,
        },
        // dateReport: {
        //     type: Array,
        //     required: true,
        // },
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
    data() {
        return {
            reactiveDate: this.date
        }
    },
    methods: {
        updateDate() {
            router.get(route('admin.time-check.index'), {
                date: this.reactiveDate,
            })
        },
    }
}


</script>