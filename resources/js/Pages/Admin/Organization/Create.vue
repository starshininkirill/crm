<template>

    <Head title="Создать организацию" />
    <div class="contract-page-wrapper flex flex-col">
        <h1 class="text-4xl font-semibold mb-6">Создать организацию</h1>

        <form @submit.prevent="submitForm" method="POST" class="max-w-md shrink-0 ">

            <ul v-if="form.errors" class="flex flex-col gap-1 mb-4">
                <li v-for="(error, index) in form.errors" :key="index" class="text-red-400">{{ error }}</li>
            </ul>

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
                <Checkbox v-model="form.nds" name="nds" label="НДС" />

                <button type="submit"
                    class="middle w-full none center mr-4 rounded-lg bg-blue-500 py-3 px-6 font-sans text-xs font-bold uppercase text-white shadow-md shadow-blue-500/20 transition-all hover:shadow-lg hover:shadow-blue-500/40 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                    data-ripple-light="true">
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
import Checkbox from '../../../Components/Checkbox.vue';
import { route } from 'ziggy-js';

export default {
    components: {
        Head,
        FormInput,
        Checkbox
    },
    layout: OrganizationLayout,
    data() {
        return {
            selectedValues: [], // Массив для выбранных значений
            checkboxOptions: [
                { value: 'option1', label: 'Опция 1' },
                { value: 'option2', label: 'Опция 2' },
                { value: 'option3', label: 'Опция 3' }
            ]
        };
    },
    setup() {
        const form = useForm({
            'short_name': null,
            'name': null,
            'inn': null,
            'nds': null,
            'terminal': null
        });

        const submitForm = () => {
            form.post(route('organization.store'), {
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