<template>
    <ServiceLayout>
    <Head title="Редактировать Категорию Услуг" />
    <div class="contract-page-wrapper flex flex-col">
        <h1 class="text-4xl font-semibold mb-6">Редактировать Категорию Услуг</h1>
        <form @submit.prevent="submitForm" method="POST" class="flex flex-col gap-3 max-w-md">
            <div class="text-3xl font-semibold">
                Создать категорию
            </div>
            <Error />
            <FormInput v-model="form.name" type="text" name="name" placeholder="Название категории"
                label="Название категории" autocomplete="name" required />
            <KeyValueSelectInput v-if="Object.keys(types).length" :options="types" label="Выберите тип категории услуг"
                name="type" id="type" v-model="form.type" />
            <button type="submit" class="btn w-full" data-ripple-light="true">
                Создать
            </button>
        </form>
    </div>
    </ServiceLayout>
</template>

<script>
import { Head, useForm } from '@inertiajs/vue3';
import ServiceLayout from '../../Layouts/ServiceLayout.vue';
import FormInput from '../../../../Components/FormInput.vue';
import KeyValueSelectInput from '../../../../Components/KeyValueSelectInput.vue';

export default {
    components: {
        Head,
        FormInput,
        KeyValueSelectInput,
        ServiceLayout
    },
    props: {
        category: {
            type: Object,
        },
        types: {
            type: Object
        }
    },
    methods: {
        deleteServiceCategory(id) {
            if (confirm('Вы уверены, что хотите удалить эту Категорию?')) {
                router.delete(route('admin.service-category.destroy', id));
            }
        },
    },
    setup(props) {
        const firstKey = Object.keys(props.types)[0];

        const form = useForm({
            'name': props.category.name,
            'type': props.category.type,
        });

        const submitForm = () => {
            form.patch(route('admin.service-category.update', { serviceCategory: props.category.id }));
        };

        return {
            form,
            submitForm
        }
    },
}


</script>