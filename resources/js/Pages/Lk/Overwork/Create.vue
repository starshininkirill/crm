<template>

    <Head title="Переработки" />

    <h1 class=" text-4xl font-bold mb-5">
        Переработки
    </h1>

    <Error />

    <form @submit.prevent="submitForm" enctype="multipart/form-data" class=" max-w-xl flex flex-col gap-3">

        <div class="grid grid-cols-2 gap-3">
            <FormInput required v-model="form.hours" type="number" name="hours" placeholder="Количество часов"
                label="Количество часов" />

            <div>
                <div class="label">
                    Дата*
                </div>
                <VueDatePicker v-model="form.date" date :auto-apply="true" locale="ru" format="yyyy-MM-dd"
                    model-type="yyyy-MM-dd" />
            </div>
        </div>

        <FormInput required v-model="form.links" type="text" name="links" placeholder="Ссылка на задачу"
            label="Ссылка на задачу" info="Перечислите ссылки на задачи через запятую"/>

        <div>
            <div class="label">
                Комментарий
            </div>
            <textarea v-model="form.report" name="report" class="input resize-none h-52"
                placeholder="Комментарий"></textarea>
        </div>

        <button type="submit" class="btn">
            Отправить
        </button>

    </form>


</template>
<script>
import { Head, useForm } from '@inertiajs/vue3';
import FormInput from '../../../Components/FormInput.vue';
import LkLayout from '../../../Layouts/LkLayout.vue';
import { route } from 'ziggy-js';
import Error from '../../../Components/Error.vue'
import VueDatePicker from '@vuepic/vue-datepicker';

export default {
    components: {
        Head,
        FormInput,
        Error,
        VueDatePicker

    },
    layout: LkLayout,

    data() {
        let form = useForm({
            'hours': null,
            'date': null,
            'report': null,
            'links': null
        });

        return {
            form,
        }
    },
    methods: {
        submitForm() {
            this.form.post(route('lk.overwork.store'), {
                onSuccess: () => {
                    console.log(this.form.date);

                    this.form.hours = null;
                    this.form.date = null;
                    this.form.report = null;
                    this.form.links = null;
                },
            });
        },
    }
}


</script>