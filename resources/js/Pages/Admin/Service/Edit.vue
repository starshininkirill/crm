<template>
    <ServiceLayout>
    <Head title="Редактировать услугу" />
    <div class="contract-page-wrapper flex flex-col">
        <h1 class="text-4xl font-semibold mb-6">Редактировать услугу</h1>
        <form @submit.prevent="submitForm" method="POST" class="max-w-md shrink-0 ">

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
                    Изменить
                </button>
            </div>
        </form>
    </div>
    </ServiceLayout>
</template>

<script>
import { Head, useForm } from '@inertiajs/vue3';
import ServiceLayout from '../Layouts/ServiceLayout.vue';
import { route } from 'ziggy-js';
import FormInput from '../../../Components/FormInput.vue';
import IdSelectInput from '../../../Components/IdSelectInput.vue';

export default {
    components: {
        Head,
        FormInput,
        IdSelectInput,
        ServiceLayout
    },
    props: {
        service: {
            type: Object,
        },
        categories: {
            type: Array
        }
    },
    setup(props) {
        const form = useForm({
            'name': props.service.name,
            'description': props.service.description,
            'work_days_duration': props.service.work_days_duration,
            'price': props.service.price,
            'service_category_id': props.service.service_category_id
        });

        const submitForm = () => {
            form.patch(route('admin.service.update', { service: props.service.id }))
        };

        return {
            form,
            submitForm
        }
    },
}


</script>