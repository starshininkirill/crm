<template>
    <SettingsLayout>

        <Head title="Настройка рабочих недель" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Настройка рабочих недель</h1>
            <form :action="route('admin.settings.finance-week')" method="GET" class="flex w-1/2 gap-3 mb-6">
                <input type="month" name="date" class="border px-3 py-1" :value="date">
                <button type="submit" class="btn !w-fit">
                    Выбрать
                </button>
            </form>

            <Error />


            <form @submit.prevent="submitForm" class="flex max-w-sm flex-col gap-4">
                <div v-for="i in 4" :key="i" class="flex justify-between items-end gap-4">
                    <VueDatePicker locale="ru" format="yyyy-MM-dd" model-type="yyyy-MM-dd" range
                        v-model="form.week[i - 1].dates"
                        :start-date="startOfMonth" />
                    <FormInput :name="`week[${i}][weeknum]`" :value="i" type="number" label="Номер недели" readonly />
                </div>
                <button type="submit" class="btn">Сохранить</button>
            </form>
        </div>
    </SettingsLayout>
</template>

<script>
import { Head, useForm } from '@inertiajs/vue3';
import SettingsLayout from '../Layouts/SettingsLayout.vue';
import FormInput from '../../../Components/FormInput.vue';
import VueDatePicker from '@vuepic/vue-datepicker';

export default {
    components: {
        Head,
        FormInput,
        SettingsLayout,
        VueDatePicker
    },
    props: {
        date: {
            type: String,
        },
        startOfMonth: {
            type: String
        },
        endOfMonth: {
            type: String
        },
        financeWeeks: {
            type: Array,
        },
    },
    setup(props) {
        const form = useForm({
            week: Array.from({ length: 4 }, (_, i) => {
                const start = props.financeWeeks[i]?.date_start;
                const end = props.financeWeeks[i]?.date_end;
                return {
                    dates: (start && end) ? [start, end] : [],
                    weeknum: props.financeWeeks[i]?.weeknum || i + 1,
                };
            }),
        });

        form.transform(data => ({
            ...data,
            week: data.week.map(w => ({
                date_start: w.dates ? w.dates[0] : null,
                date_end: w.dates ? w.dates[1] : null,
                weeknum: w.weeknum,
            })),
        }));

        return {
            form,
        };
    },
    methods: {
        submitForm() {

            if (!this.financeWeeks && !this.financeWeeks.length) {
                this.form.post(route('admin.settings.finance-week.set-weeks'));
            } else {
                this.form.post(route('admin.settings.finance-week.set-weeks'));
            }
        },
    }
}


</script>