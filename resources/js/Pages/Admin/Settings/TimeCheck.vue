<template>
    <SettingsLayout>

        <Head title="Настройки TimeCheck" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Настройки TimeCheck</h1>
            <Error />

            <div class="grid grid-cols-2 gap-6 max-w-xl">
                <form @submit.prevent="saveStartTime" class="flex flex-col gap-3">
                    <FormInput v-model="startTime" name="startTime" type="time" label="Начало рабочего дня" />
                    <button class="btn">
                        Сохранить
                    </button>
                </form>
                <form @submit.prevent="saveBreakTime" class="flex flex-col gap-3">
                    <FormInput v-model="breakTime" name="breakTime" type="time" label="Время на перерывы" />
                    <button class="btn">
                        Сохранить
                    </button>
                </form>
            </div>

        </div>
    </SettingsLayout>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import SettingsLayout from '../Layouts/SettingsLayout.vue';
import FormInput from '../../../Components/FormInput.vue';
import Error from '../../../Components/Error.vue';
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

export default {
    components: {
        Head,
        FormInput,
        SettingsLayout,
        Error
    },
    props: {
        startTimeProp: {
            type: String,
            required: true,
        },
        breakTimeProp: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            startTime: this.startTimeProp,
            breakTime: this.breakTimeProp,
        };
    },
    methods: {
        saveStartTime() {
            if (!this.startTime) {
                return;
            }
            const formattedTime = `${this.startTime}:00`;

            router.post(route('option.store'), {
                value: formattedTime,
                name: 'time_check_start_work_day_time',
            });
        },
        saveBreakTime() {
            if (!this.breakTime) {
                return;
            }
            const formattedTime = `${this.breakTime}:00`;

            router.post(route('option.store'), {
                value: formattedTime,
                name: 'time_check_max_breaktime',
            });
        }
    }
}


</script>