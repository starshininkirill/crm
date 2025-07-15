<template>
    <OrganizationLayout>
        <Head title="Редактировать организацию" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Редактировать организацию</h1>
            <form @submit.prevent="submitForm" method="POST" class="shrink-0 max-w-md">
                <Error />
                <div class="flex flex-col gap-2">
                    <FormInput v-model="form.short_name" type="text" name="short_name" placeholder="ИП 1"
                        label="Краткое наименование организации*" autocomplete="short_name" required />
                    <FormInput v-model="form.name" type="text" name="name"
                        placeholder="Индивидуальный предпиниматель Иванов Иван Иванович"
                        label="Полное наименование организации*" required />
                    <FormInput v-model="form.inn" type="number" name="inn" placeholder="ИНН" label="ИНН"
                        autocomplete="inn" />
                    <FormInput v-model="form.terminal" type="number" name="terminal" placeholder="Номер терминала"
                        label="Номер терминала" />
                    <FormInput v-model="form.wiki_id" type="number" name="wiki_id" placeholder="ID в Wiki"
                        label="ID в Wiki*" required />
                    <ToggleSwitch v-model="form.has_doc_number" label="Имеет свой нумератор" />
                    <FormInput v-model="form.doc_number" type="number" name="doc_number" placeholder="Значение нумератора"
                        label="Значение нумератора" v-if="form.has_doc_number" />
                    <YesNoSelector v-model="form.nds" name="nds" label="С НДС" />
                    <button type="submit" class="btn" data-ripple-light="true">
                        Изменить
                    </button>
                </div>
            </form>
        </div>
    </OrganizationLayout>
</template>

<script>
import { Head, useForm } from '@inertiajs/vue3';
import OrganizationLayout from '../Layouts/OrganizationLayout.vue';
import FormInput from '../../../Components/FormInput.vue';
import YesNoSelector from '../../../Components/YesNoSelector.vue';
import { route } from 'ziggy-js';
import Error from '../../../Components/Error.vue';
import ToggleSwitch from '../../../Components/ToggleSwitch.vue';

export default {
    components: {
        Head,
        FormInput,
        YesNoSelector,
        OrganizationLayout,
        Error,
        ToggleSwitch
    },
    props: {
        organization: {
            type: Object,
        },
    },
    setup(props) {
        const form = useForm({
            'short_name': props.organization.short_name ?? null,
            'name': props.organization.name ?? null,
            'inn': props.organization.inn ?? null,
            'nds': props.organization.nds ?? 0,
            'terminal': props.organization.terminal ?? null,
            'has_doc_number': props.organization.has_doc_number ?? 0,
            'doc_number': props.organization.doc_number ?? null,
            'wiki_id': props.organization.wiki_id ?? null
        });

        const submitForm = () => {
            form.patch(route('admin.organization.update', { organization: props.organization.id }));
        };

        return {
            form,
            submitForm
        }
    },
}

</script>