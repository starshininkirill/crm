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


            <form @submit.prevent="submitForm" class="flex max-w-md flex-col gap-4">
                <div v-for="i in 5" :key="i" class="flex justify-between gap-4">
                    <FormInput :name="`week[${i}][date_start]`" v-model="form.week[i - 1].date_start" type="date"
                        :min="startOfMonth" :max="endOfMonth" label="Начало недели" />
                    <FormInput :name="`week[${i}][date_end]`" v-model="form.week[i - 1].date_end" type="date"
                        :min="startOfMonth" :max="endOfMonth" label="Конец недели" />
                    <FormInput :name="`week[${i}][weeknum]`" :value="i" type="number" label="Номер недели" readonly />
                </div>
                <button type="submit" class="btn">Отправить</button>
            </form>
        </div>
    </SettingsLayout>
</template>

<script>
import { Head, useForm } from '@inertiajs/vue3';
import SettingsLayout from '../Layouts/SettingsLayout.vue';
import FormInput from '../../../Components/FormInput.vue';

export default {
    components: {
        Head,
        FormInput,
        SettingsLayout
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
            week: Array.from({ length: 5 }, (_, i) => ({
                date_start: props.financeWeeks[i]?.date_start || '',
                date_end: props.financeWeeks[i]?.date_end || '',
                weeknum: props.financeWeeks[i]?.weeknum || i + 1,
            })),
        });

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