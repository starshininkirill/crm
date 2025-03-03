<template>
    <SettingsLayout>

        <Head title="Календарь рабочих дней" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Календарь рабочих дней</h1>
            <form :action="route('admin.settings.calendar')" method="GET" class="flex w-1/2 gap-3 mb-6">
                <input type="month" name="date" class="border px-3 py-1" :value="date">
                <button type="submit" class="btn !w-fit">
                    Выбрать
                </button>
            </form>
            <div class="grid grid-cols-4 auto-rows-max gap-6">
                <CalendarMonth v-for="month in months" :month-name="month['name']" :weeks="month['weeks']">
                </CalendarMonth>
            </div>
        </div>
    </SettingsLayout>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import SettingsLayout from '../Layouts/SettingsLayout.vue';
import CalendarMonth from '../../../Components/CalendarMonth.vue';

export default {
    components: {
        Head,
        CalendarMonth,
        SettingsLayout,
    },
    props: {
        date: {
            type: String
        },
        months: {
            type: Array,
        },
    },
}


</script>