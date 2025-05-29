<template>
    <DocumentTemplateLayout>

        <Head title="Нумератор" />

        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Нумератор</h1>

            <form @submit.prevent="submitForm()" class="max-w-80">
                <FormInput v-model="form.value" type="number" name="value" placeholder="Последнее значение нумератора"
                    label="Последнее значение нумератора" required />
                <button class="btn mt-3">Изменить</button>
            </form>
        </div>
    </DocumentTemplateLayout>
</template>

<script>
import { Head, useForm } from '@inertiajs/vue3';
import DocumentTemplateLayout from '../Layouts/DocumentTemplateLayout.vue';
import FormInput from '../../../Components/FormInput.vue';

export default {
    components: {
        DocumentTemplateLayout,
        Head,
        FormInput
    },
    props: {
        option: Object|null,
    },
    data() {
        let form = useForm({
            'name': 'document_generator_num',
            'value': this.option.value ?? null,
        });
        return {
            form
        }
    },
    methods: {
        submitForm() {
            if (this.option) {
                this.form.put(route('option.update', { option: this.option.id }));
            } else {
                this.form.post(route('option.store'));
            }
        },
    }
}


</script>