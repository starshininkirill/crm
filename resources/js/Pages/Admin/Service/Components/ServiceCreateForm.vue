<template>
    <div class="contract-page-wrapper flex flex-col">
        <form @submit.prevent="submitForm" method="POST" class=" shrink-0 ">
            <Error />

            <div class="flex flex-col gap-2">
                <FormInput v-model="form.name" type="text" name="name" placeholder="Название услуги"
                    label="Название услуги" required />

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
    </div>
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
    },
    setup(props) {
        let firstKey = null
        if (props.categories.length) {
            firstKey = props.categories[0].id
        }

        const form = useForm({
            'name': null,
            'description': null,
            'work_days_duration': null,
            'price': null,
            'service_category_id': firstKey
        });

        const submitForm = () => {
            form.post(route('admin.service.store'), {
                onFinish: () => {
                    form.name = null
                    form.description = null
                    form.work_days_duration = null
                    form.price = null
                    form.service_category_id = firstKey
                },
            });
        };

        return {
            form,
            submitForm
        }
    },
}


</script>