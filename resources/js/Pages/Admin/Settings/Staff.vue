<template>
    <SettingsLayout>

        <Head title="Настройки кадров" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Настройки кадров</h1>

            <form @submit.prevent="handleSubmit" class="flex flex-col gap-4 max-w-xl">
                <p class="text-gray-600">Выберите типы трудоустройства для включения в отчет.</p>

                <div class="grid grid-cols-2 gap-2">
                    <div v-for="employmentType in employmentTypes" :key="employmentType.id"
                        class="flex items-cente cursor-pointerr">
                        <input type="checkbox" :id="'employment_type_' + employmentType.id" :value="employmentType.id"
                            v-model="selectedEmploymentTypes"
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded flex-shrink-0 cursor-pointer">
                        <label :for="'employment_type_' + employmentType.id"
                            class="ml-2 block text-sm text-gray-900 cursor-pointer">
                            {{ employmentType.name }}
                        </label>
                    </div>
                </div>

                <button class="btn">
                    Сохранить
                </button>
            </form>
        </div>
    </SettingsLayout>
</template>

<script>
import { Head, useForm } from '@inertiajs/vue3';
import SettingsLayout from '../Layouts/SettingsLayout.vue';

export default {
    components: {
        Head,
        SettingsLayout,
    },
    props: {
        option: {
            type: Object,
            required: true,
        },
        employmentTypes: {
            type: Array,
            required: true,
        },
    },
    data() {
        let selectedEmploymentTypes = this.option?.value ? JSON.parse(this.option.value) : [];

        let form = useForm({
            'name': 'ids_of_employment_types_for_generating_salary_table',
            'value': selectedEmploymentTypes,
        });
        return {
            form,
            selectedEmploymentTypes
        }
    },
    watch: {
        selectedEmploymentTypes(newValue) {
            this.form.value = newValue;
        }
    },
    methods: {
        handleSubmit() {
            this.form.transform((data) => ({
                ...data,
                value: JSON.stringify(data.value)
            }));
            
            if (!this.option || !this.option?.value) {
                this.form.post(route('option.store'));
            } else {
                this.form.put(route('option.update', { option: this.option.id }));
            }
        }
    }
}

</script>