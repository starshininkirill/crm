<template>
    <form @submit.prevent="submitForm" class="flex gap-3 mb-6 max-w-4xl">

        <!-- Select Department -->
        <div class="w-2/4 flex flex-col">
            <label class="label">Отдел</label>
            <VueSelect class="full-vue-select h-full" v-model="selectedDepartment" :reduce="department => department"
                label="name" :options="departments" @update:modelValue="filterUsers">
            </VueSelect>
        </div>
        <!-- Select User -->
        <div class="w-2/4 flex flex-col">
            <label class="label">Сотрудник</label>
            <VueSelect class="full-vue-select h-full" v-model="selectedUser" :reduce="user => user" label="full_name"
                :options="filtredUsers">
            </VueSelect>
        </div>

        <div class="w-1/4 flex flex-col">
            <label class="label">Дата</label>
            <VueDatePicker v-model="form.date" model-type="yyyy-MM" :auto-apply="true" month-picker locale="ru"
                class="h-full" />
        </div>
        <button type="submit" class="btn !w-fit !h-fit self-end">Выбрать</button>
    </form>
</template>

<script>
import { useForm } from '@inertiajs/vue3';
import VueSelect from 'vue-select';
import { route } from 'ziggy-js';
import VueDatePicker from '@vuepic/vue-datepicker'

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
                department: null,
            }),
            selectedDepartment: this.initialDepartment || null,
            selectedUser: this.initialUser || null,
            filtredUsers: [],
        };
    },
    watch: {
        'form.department': 'filterUsers',
        'form.user': 'validateUser',
        'form.date': 'updateDate'
    },
    mounted() {
        this.filterUsers();
    },
    methods: {
        filterUsers() {
            if (!this.form.department || this.form.department.parent_id == null) {
                this.filtredUsers = this.users;
            } else {
                this.filtredUsers = this.users.filter(
                    user => user.department_id === this.form.department.id
                );
            }
            this.validateUser();
        },
        validateUser() {
            if (this.selectedUser && !this.filtredUsers.some(user => user.id === this.selectedUser.id)) {
                this.selectedUser = null;
            }
        },
        submitForm() {
            this.form.user = this.selectedUser?.id || null;
            this.form.department = this.selectedDepartment.id
            this.form.get(route('admin.sale-department.user-report'), {
                // preserveScroll: true,
            });
        },
        updateDate() {
            this.submitForm();
        },
    }
};
</script>