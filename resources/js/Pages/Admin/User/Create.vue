<template>

    <Head title="Создать сотрудника" />
    <div class="contract-page-wrapper flex flex-col">
        <h1 class="text-4xl font-semibold mb-6">Создать сотрудника</h1>

        <form @submit.prevent="submitForm" class="max-w-lg flex flex-col gap-2">
            <div class="grid grid-cols-2 gap-4">
                <FormInput type="text" required v-model="form.first_name" name="first_name" label="Имя"
                    placeholder="Имя" />
                <FormInput type="text" required v-model="form.last_name" name="last_name" label="Фамилия"
                    placeholder="Фамилия" />
            </div>
            <FormInput type="number" required v-model="form.salary" name="salary" label="Ставка" placeholder="Ставка" />

            <div class="flex flex-col">
                <span class="label">
                    Тип устройства
                </span>
                <VueSelect v-model="form.employment_type_id" :reduce="type => type.id" label="name"
                    :options="employmentTypes">
                </VueSelect>
            </div>
            <div v-if="fields.length > 0" class="grid grid-cols-2 gap-y-2 gap-x-4">
                <FormInput v-for="(field, index) in fields" :key="index" :type="field.type" :name="field.name"
                    :label="field.readName" :placeholder="field.readName" v-model="form.details[index].value" />
            </div>

            <div class="flex flex-col">
                <span class="label">
                    Отдел
                </span>
                <VueSelect v-model="form.department_id" :reduce="department => department.id" label="name"
                    :options="departments">
                </VueSelect>
            </div>

            <div v-if="form.department_id" class="flex flex-col">
                <span class="label">
                    Должность
                </span>
                <VueSelect v-model="form.position_id" :reduce="position => position.id" label="name"
                    :options="activePositions">
                </VueSelect>
            </div>

            <button class="btn">
                Создать
            </button>
        </form>
    </div>
</template>

<script>
import { Head, useForm } from '@inertiajs/vue3';
import UserLayout from '../Layouts/UserLayout.vue';
import FormInput from '../../../Components/FormInput.vue';
import VueSelect from 'vue-select';

export default {
    components: {
        Head,
        FormInput,
        VueSelect,
    },
    layout: UserLayout,
    props: {
        positions: {
            type: Array,
            required: true,
        },
        departments: {
            type: Array,
            required: true,
        },
        employmentTypes: {
            type: Array,
            required: true,
        },
    },
    data() {
        let form = useForm({
            'first_name': null,
            'last_name': null,
            'salary': null,
            'department_id': null,
            'position_id': null,
            'employment_type_id': null,
            'details': [],
        });

        return {
            form,
            activePositions: [],
            fields: [],
        };
    },
    watch: {
        'form.department_id': {
            handler: 'updatePositions',
            deep: true,
        },
        'form.employment_type_id': {
            handler: 'setEmploymentFields',
            deep: true,
        },
    },
    methods: {
        submitForm() {
            this.form.post(route('admin.user.store'));
        },
        updatePositions() {
            let activeDepartment = this.departments.find(department => department.id === this.form.department_id);
            if (!activeDepartment || !activeDepartment.positions) {
                this.activePositions = [];
                this.form.position_id = null;
                return;
            }
            this.activePositions = activeDepartment.positions;
            if (!this.activePositions.includes(this.form.position_id)) {
                this.form.position_id = null;
            }
        },
        setEmploymentFields() {
            let activeEmploymentType = this.employmentTypes.find(type => type.id === this.form.employment_type_id);
            if (activeEmploymentType) {
                this.fields = activeEmploymentType.fields;

                this.form.details = this.fields.map(field => ({
                    name: field.name,
                    value: null,
                }));
            } else {
                this.fields = [];
                this.form.details = [];
            }
        },
    },
};
</script>