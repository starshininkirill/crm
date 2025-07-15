<template>
    <div class="contract-page-wrapper flex flex-col">
        <form @submit.prevent="submitForm" method="POST" class="shrink-0 ">

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
                    Создать
                </button>
            </div>
        </form>
    </div>
</template>

<script>
import { useForm } from '@inertiajs/vue3';
import OrganizationLayout from '../../Layouts/OrganizationLayout.vue';
import FormInput from '../../../../Components/FormInput.vue';
import YesNoSelector from '../../../../Components/YesNoSelector.vue';
import { route } from 'ziggy-js';
import Error from '../../../../Components/Error.vue'
import ToggleSwitch from '../../../../Components/ToggleSwitch.vue';

export default {
    components: {
        FormInput,
        YesNoSelector,
        Error,
        ToggleSwitch
    },
    layout: OrganizationLayout,
    setup() {
        const form = useForm({
            'short_name': null,
            'name': null,
            'inn': null,
            'nds': 0,
            'terminal': null,
            'has_doc_number': 0,
            'doc_number': null,
            'wiki_id': null
        });

        const submitForm = () => {
            form.post(route('admin.organization.store'), {
                onFinish: () => {
                    form.name = null
                    form.short_name = null
                    form.inn = null
                    form.nds = null
                    form.terminal = null
                    form.has_doc_number = 0
                    form.doc_number = null
                    form.wiki_id = null
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