<template>
    <OrganizationLayout>

        <Head title="Редактировать Шаблон документа" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Редактировать Шаблон документа</h1>
            <form @submit.prevent="submitForm" enctype="multipart/form-data" class="flex flex-col gap-3 max-w-md">
                <div class="text-3xl font-semibold">
                    Создать Шаблон документа
                </div>

                <Error />

                <FormInput v-model="form.name" type="text" name="name" placeholder="Название шаблона"
                    label="Название шаблона" autocomplete="name" required />

                <div>
                    Документ: <span class=" font-semibold">{{ documentTemplate.file_name }}</span>
                </div>

                <label class="text-sm font-medium leading-6 text-gray-900 flex flex-col gap-1 cursor-pointer"
                    for="file">
                    Прикрепить новый документ
                    <input type="file" id="file" name="file" class="form-input cursor-pointer"
                        @change="handleFileChange" />
                </label>

                <button type="submit" class="btn w-full" data-ripple-light="true">
                    Обновить
                </button>
            </form>
        </div>
    </OrganizationLayout>
</template>

<script>
import { Head, useForm } from '@inertiajs/vue3';
import OrganizationLayout from '../../Layouts/OrganizationLayout.vue';
import FormInput from '../../../../Components/FormInput.vue';
import { route } from 'ziggy-js';
import Error from '../../../../Components/Error.vue';

export default {
    components: {
        Head,
        FormInput,
        Error,
        OrganizationLayout
    },
    props: {
        documentTemplate: {
        },
    },
    setup(props) {

        const form = useForm({
            'name': props.documentTemplate.name,
            'file': null,
            '_method': 'PATCH'
        });

        const submitForm = () => {
            form.post(route('admin.document-template.update', { documentTemplate: props.documentTemplate }), {
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