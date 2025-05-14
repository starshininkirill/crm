<template>
    <TimeCheckLayout>

        <Head title="Time Check" />
        <div class="flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Time Check</h1>
            <div class="flex flex-col gap-6">
                <div>
                    <div class="label">
                        Дата отображения
                    </div>
                    <VueDatePicker v-model="reactiveDate" class="w-fit" :auto-apply="true" format="yyyy-MM-dd"
                        model-type="yyyy-MM-dd" date locale="ru" @update:modelValue="updateDate" />
                </div>
                <div class="text-xl font-semibold">
                    Текущий статус сотрудников
                </div>
                <div class="flex w-full gap-4">
                    <div class="w-8/12">
                        <CurrentUserStatuses :date="date" :todayReport="todayReport" :workStatuses="workStatuses" />
                    </div>
                    <div class=" w-4/12">
                        <AggregateTable :report="aggregatedReport" />
                    </div>
                </div>

                <LogReport :logReport="logReport" />
            </div>
        </div>
    </TimeCheckLayout>
</template>

<script>
import { Head, router } from '@inertiajs/vue3';
import TimeCheckLayout from '../Layouts/TimeCheckLayout.vue';
import CurrentUserStatuses from './Components/CurrentTable/CurrentUserStatuses.vue';
import LogReport from './Components/LogReportTable/LogReport.vue';
import { route } from 'ziggy-js';
import VueDatePicker from '@vuepic/vue-datepicker';
import AggregateTable from './Components/AggregateTable/AggregateTable.vue';

export default {
    components: {
        Head,
        TimeCheckLayout,
        CurrentUserStatuses,
        LogReport,
        VueDatePicker,
        AggregateTable
    },
    props: {
        todayReport: Array,
        aggregatedReport: Object,
        logReport: Array,
        workStatuses: Array,
        date: String
    },
    data() {
        return {
            reactiveDate: this.date,
            pollInterval: null
        }
    },
    methods: {
        updateDate() {
            router.get(route('admin.time-check.index'), {
                date: this.reactiveDate,
            });
        },
        startPolling() {
            this.pollInterval = setInterval(() => {
                router.get(route('admin.time-check.index'), {
                    date: this.reactiveDate,
                }, { 
                    preserveState: true,
                    preserveScroll: true,
                    only: ['todayReport', 'aggregatedReport'],
                });
            }, 30000);
        }
    },
    mounted() {
        this.startPolling();
    },
    beforeUnmount() {
        if (this.pollInterval) clearInterval(this.pollInterval);
    }
}
</script>