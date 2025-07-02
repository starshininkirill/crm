<template>
    <form @submit.prevent="submitForm" class="flex gap-3 mb-6 max-w-4xl">

        <!-- Select User -->
        <div class="max-w-[300px] w-full flex flex-col">
            <label class="label">Сотрудник</label>
            <VueSelect class="full-vue-select h-full" v-model="selectedUser" :reduce="user => user" label="full_name"
                :options="users">
            </VueSelect>
        </div>

        <div class="w-fit max-w-[150px] flex flex-col">
            <label class="label">Дата</label>
            <VueDatePicker v-model="form.date" model-type="yyyy-MM" :auto-apply="true" month-picker locale="ru"
                class="h-full " />
        </div>
        <button type="submit" class="btn !w-fit !h-fit self-end">Выбрать</button>
    </form>
</template>

<script>
import { useForm, router } from '@inertiajs/vue3';
import VueSelect from 'vue-select';
import { route } from 'ziggy-js';
import VueDatePicker from '@vuepic/vue-datepicker'
import axios from 'axios';

export default {
    components: {
        VueSelect,
        VueDatePicker
    },
    props: {
        departments: Array,
        users: Array,
        initialDepartment: Object,
        initialUser: Object,
        initialDate: String,
    },
    data() {

        return {
            form: useForm({
                date: this.initialDate || new Date().toISOString().slice(0, 7),
                user: null,
            }),
            selectedUser: this.initialUser || null,
        };
    },
    methods: {
        submitForm() {
            this.form.user = this.selectedUser?.id || null;
            this.form.get(route('admin.projects-department.report'), {
                // preserveScroll: true,
            });
        },
    }
};
</script>