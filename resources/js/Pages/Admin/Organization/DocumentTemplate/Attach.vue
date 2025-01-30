<template>

    <Head title="Привязать шаблон" />
    <div class="contract-page-wrapper flex flex-col">
        <h1 class="text-4xl font-semibold mb-6">Привязать шаблон</h1>

        <form @submit.prevent="submitForm" method="POST" enctype="multipart/form-data" class="flex flex-col gap-3">
            <div class="text-3xl font-semibold">
                Связать
            </div>

            <ul v-if="form.errors" class="flex flex-col gap-1">
                <li v-for="(error, index) in form.errors" :key="index" class="text-red-400">{{ error }}</li>
            </ul>

            <div class=" flex flex-col gap-2">
                <VueSelect v-model="form.document_template_id"
                    :reduce="documetTemplates => documetTemplates.id" label="name" :options="documetTemplates">
                </VueSelect>
                <VueSelect v-model="form.service_id"
                    :reduce="services => services.id" label="name" :options="services">
                </VueSelect>
                <VueSelect v-model="form.organization_id"
                    :reduce="organizations => organizations.id" label="short_name" :options="organizations">
                </VueSelect>

                <VueSelect v-model="form.type"
                    :reduce="organizations => organizations.value" label="name" :options="[{'name': 'Тип 1', 'value': 1}, {'name': 'Тип 2', 'value': 2}]">
                </VueSelect>

                <button type="submit" class="btn !h-auto w-full" data-ripple-light="true">
                    Создать
                </button>
            </div>
        </form>
    </div>
</template>

<script>
import { Head, useForm } from '@inertiajs/vue3';
import OrganizationLayout from '../../Layouts/OrganizationLayout.vue';
import FormInput from '../../../../Components/FormInput.vue';
import VueSelect from 'vue-select';
import 'vue-select/dist/vue-select.css';
import { route } from 'ziggy-js';

export default {
    components: {
        Head,
        FormInput,
        VueSelect
    },
    props: {
        documetTemplates: {
            type: Array,
        },
        services: {
            type: Array,
        },
        organizations: {
            type: Array,
        }
    },
    layout: OrganizationLayout,
    setup(props) {

        const form = useForm({
            'document_template_id': null,
            'service_id': null,
            'organization_id': null,
            'type': null
        });


        const submitForm = () => {
            form.post(route('osdt.store'), {
                onSuccess: () => {
                    form.document_template_id = null,
                    form.service_id = null,
                    form.organization_id = null,
                    form.type = null
                },
            });
        };

        return {
            form,
            submitForm
        }
    },
    methods: {
        test() {
            console.log(this.form.document_template_id);

        }
    }
}

</script>