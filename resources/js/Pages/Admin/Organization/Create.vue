<template>

    <Head title="Создать организацию" />
    <div class="contract-page-wrapper flex flex-col">
        <h1 class="text-4xl font-semibold mb-6">Создать организацию</h1>

        <form @submit.prevent="submitForm" method="POST" class="max-w-md shrink-0 ">

            <Error />

            <div class="flex flex-col gap-2">
                <FormInput v-model="form.short_name" type="text" name="short_name" placeholder="ИП 1"
                    label="Краткое наименование организации" autocomplete="short_name" required />
                <FormInput v-model="form.name" type="text" name="name"
                    placeholder="Индивидуальный предпиниматель Иванов Иван Иванович"
                    label="Полное наименование организации" required />
                <FormInput v-model="form.inn" type="number" name="inn" placeholder="ИНН" label="ИНН" autocomplete="inn"
                    required />
                <FormInput v-model="form.terminal" type="number" name="terminal" placeholder="Номер терминала"
                    label="Номер терминала" required />
                <YesNoSelector v-model="form.nds" name="nds" label="НДС" />

                <button type="submit" class="btn" data-ripple-light="true">
                    Создать
                </button>
            </div>
        </form>
    </div>
</template>

<script>
import { Head, useForm } from '@inertiajs/vue3';
import OrganizationLayout from '../Layouts/OrganizationLayout.vue';
import FormInput from '../../../Components/FormInput.vue';
import YesNoSelector from '../../../Components/YesNoSelector.vue';
import { route } from 'ziggy-js';

export default {
    components: {
        Head,
        FormInput,
        YesNoSelector
    },
    layout: OrganizationLayout,
    setup() {
        const form = useForm({
            'short_name': null,
            'name': null,
            'inn': null,
            'nds': null,
            'terminal': null
        });

        const submitForm = () => {
            form.post(route('admin.organization.store'), {
                onFinish: () => {
                    form.name = null
                    form.short_name = null
                    form.inn = null
                    form.nds = null
                    form.terminal = null
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