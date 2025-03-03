<template>
    <OrganizationLayout>

        <Head title="Редактировать организацию" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Редактировать организацию</h1>

            <form @submit.prevent="submitForm" method="POST" class="max-w-md shrink-0 ">

                <Error />

                <div class="flex flex-col gap-2">
                    <FormInput v-model="form.short_name" type="text" name="short_name" placeholder="ИП 1"
                        label="Краткое наименование организации" autocomplete="short_name" required />
                    <FormInput v-model="form.name" type="text" name="name"
                        placeholder="Индивидуальный предпиниматель Иванов Иван Иванович"
                        label="Полное наименование организации" required />
                    <FormInput v-model="form.inn" type="number" name="inn" placeholder="ИНН" label="ИНН"
                        autocomplete="inn" required />
                    <FormInput v-model="form.terminal" type="number" name="terminal" placeholder="Номер терминала"
                        label="Номер терминала" required />
                    <YesNoSelector v-model="form.nds" name="nds" label="НДС" />
                    <button type="submit"
                        class="middle w-full none center mr-4 rounded-lg bg-blue-500 py-3 px-6 font-sans text-xs font-bold uppercase text-white shadow-md shadow-blue-500/20 transition-all hover:shadow-lg hover:shadow-blue-500/40 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                        data-ripple-light="true">
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

export default {
    components: {
        Head,
        FormInput,
        YesNoSelector,
        OrganizationLayout
    },
    props: {
        organization: {
            type: Object,
        },
    },
    setup(props) {
        const form = useForm({
            'short_name': props.organization.short_name,
            'name': props.organization.name,
            'inn': props.organization.inn,
            'nds': props.organization.nds,
            'terminal': props.organization.terminal
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