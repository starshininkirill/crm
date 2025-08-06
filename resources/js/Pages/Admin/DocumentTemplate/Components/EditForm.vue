<template>
    <form @submit.prevent="submitForm" method="POST" enctype="multipart/form-data" class="flex flex-col gap-3">
        <div class="text-xl font-semibold">
            Редактировать Шаблон документа
        </div>

        <Error />

        <FormInput v-model="form.template_name" type="text" name="name"
        placeholder="ИП Николаев ИНД ЛЕНД ЮР" label="Название шаблона" autocomplete="name" />

        <FormInput v-model="form.result_name" type="text" name="name" placeholder="Разработка сайта"
            label="Название файла после генерации" autocomplete="name" required />

        <FormInput v-model="form.template_id" type="number" name="name" placeholder="id шаблона" label="id шаблона"
            autocomplete="name" required readonly />

        <ToggleSwitch v-model="form.use_custom_doc_number" label="Использовать нумератор организации" />

        <label class="text-sm font-medium leading-6 text-gray-900 flex flex-col gap-1 cursor-pointer" for="file">
            Прикрепить новый документ
            <input type="file" id="file" name="file" class="form-input cursor-pointer" @change="handleFileChange" />
        </label>

        <button type="submit" class="btn w-full" data-ripple-light="true">
            Изменить
        </button>
    </form>
</template>
<script>
import { useForm } from '@inertiajs/vue3';
import FormInput from '../../../../Components/FormInput.vue';
import { route } from 'ziggy-js';
import Error from '../../../../Components/Error.vue';
import ToggleSwitch from '../../../../Components/ToggleSwitch.vue';

export default {
    components: {
        FormInput,
        Error,
        ToggleSwitch
    },
    props: {
        docuementTemplate: {
            required: true,
            type: Object,
        }
    },
    setup(props, context) {
        const form = useForm({
            'template_name': props.docuementTemplate.template_name, 
            'result_name': props.docuementTemplate.result_name,
            'template_id': props.docuementTemplate.template_id,
            'file': props.docuementTemplate.file,
            'use_custom_doc_number': props.docuementTemplate.use_custom_doc_number,
            '_method': 'PATCH'
        });

        const submitForm = () => {
            form.post(route('admin.document-generator.update', { documentTemplate: props.docuementTemplate.id }), {
                onSuccess: () => {
                    form.template_id = ''
                    form.template_name = '';
                    form.file = '';
                    form.result_name = '';
                    form.use_custom_doc_number = 0;

                    const fileInput = document.querySelector('input[type="file"]');
                    if (fileInput) {
                        fileInput.value = '';
                    }
                    context.emit('closeModal');
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