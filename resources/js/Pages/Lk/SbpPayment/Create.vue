<template>

    <Head title="Создание Платёж СБП" />

    <h1 class=" text-4xl font-bold mb-5">
        Создание Платёж СБП
    </h1>

    <Error />

    <form @submit.prevent="submitForm" enctype="multipart/form-data" class=" max-w-xl flex flex-col gap-3">

        <FormInput required v-model="form.number" type="number" name="number" placeholder="Номер договора"
            label="Номер договора" />

        <FormInput required v-model="form.value" type="number" name="summ" placeholder="10 000" label="Сумма платежа" />

        <FormInput required v-model="form.description" type="text" name="description" placeholder="Оплата за Ведение РК"
            label="Описание" />

        <FormInput required v-model="form.date" type="datetime-local" name="date" placeholder="Оплата за Ведение РК"
            label="Дата и время прихода" />

        <div class="text-sm font-medium leading-6 text-gray-900 flex flex-col gap-1 cursor-pointer">
            Организация
            <VueSelect v-model="form.organization_id" :reduce="organizations => organizations.id" label="short_name"
                :options="organizations">
            </VueSelect>
        </div>

        <label class="text-sm font-medium leading-6 text-gray-900 flex flex-col gap-1 cursor-pointer" for="file">
            Прикрепить чек платежа
            <input accept="image/*" type="file" id="file" name="file" class="form-input cursor-pointer"
                @change="handleFileChange" />
        </label>

        <button type="submit" class="btn">
            Создать
        </button>

    </form>



</template>
<script>
import { Head, useForm } from '@inertiajs/vue3';
import FormInput from '../../../Components/FormInput.vue';
import LkLayout from '../../../Layouts/LkLayout.vue';
import VueSelect from 'vue-select';
import { route } from 'ziggy-js';

export default {
    components: {
        Head,
        FormInput,
        VueSelect

    },
    layout: LkLayout,

    props: {
        organizations: {
            type: Array
        }
    },

    setup() {
        const form = useForm({
            'number': null,
            'value': null,
            'description': null,
            'date': null,
            'organization_id': null,
            'file': null
        });

        const submitForm = () => {
            form.post(route('lk.sbp.store'), {
                onSuccess: () => {
                    const fileInput = document.querySelector('input[type="file"]');
                    if (fileInput) {
                        fileInput.value = '';
                    }
                },
            });
        };

        return {
            form,
            submitForm
        }
    },
    methods: {
        handleFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.form.file = file;
            } else {
                this.form.file = null;
            }
        },
    }
}


</script>