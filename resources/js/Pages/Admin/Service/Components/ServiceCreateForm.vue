<template>
    <h1 class="text-2xl font-semibold mb-6">
        Создать услугу
    </h1>
    <form @submit.prevent="submitForm" method="POST" class=" shrink-0 ">
        <Error />

        <div class="flex flex-col gap-2">
            <FormInput v-model="form.name" type="text" name="name" placeholder="Название услуги" label="Название услуги"
                required />

            <FormInput v-model="form.description" type="text" name="description" placeholder="Описание"
                label="Введите описание" required />

            <FormInput v-model="form.work_days_duration" type="text" name="work_days_duration"
                placeholder="5 ( пять ) рабочих дней" label="Срок исполнения" required />

            <FormInput v-model="form.price" type="number" name="price" placeholder="Рекомендованая цена"
                label="Рекомендованая цена" required />

            <IdSelectInput :options="categories" label="Выберите категорию" name="service_category_id"
                id="service_category_id" v-model="form.service_category_id" />

            <button type="submit" class="btn" data-ripple-light="true">
                Создать
            </button>
        </div>
    </form>
</template>

<script>
import { Head, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import FormInput from '../../../../Components/FormInput.vue';
import IdSelectInput from '../../../../Components/IdSelectInput.vue';
import Error from '../../../../Components/Error.vue';

export default {
    components: {
        Head,
        FormInput,
        IdSelectInput,
        Error
    },
    props: {
        categories: {
            type: Array,
        },
        openModal: {
            type: Boolean
        }
    },
    emits: ['success'],
    data() {
        let form = useForm({
            'name': null,
            'description': null,
            'work_days_duration': null,
            'price': null,
            'service_category_id': this.categories?.[0]?.id || null,
        });

        return {
            form,
        }
    },
    methods: {
        submitForm() {
            this.form.post(route('admin.service.store'), {
                onSuccess: () => {
                    this.form.name = null
                    this.form.description = null
                    this.form.work_days_duration = null
                    this.form.price = null
                    this.form.service_category_id = this.categories?.[0]?.id || null;
                    this.$emit('success');
                },
            });
        },

    },
}


</script>