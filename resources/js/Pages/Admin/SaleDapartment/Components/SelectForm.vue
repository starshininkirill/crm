<template>
    <form @submit.prevent="submitForm" class="grid grid-cols-4 max-w-5xl gap-3 mb-6">
        <!-- Select Date -->
        <input type="month" name="date" class="input" v-model="form.date">
        <!-- Select Department -->
        <VueSelect class="full-vue-select" v-model="form.department" :reduce="department => department"
            label="name" :options="departments" @update:modelValue="filterUsers">
        </VueSelect>
        <!-- Select User -->
        <VueSelect class="full-vue-select" v-model="form.user" :reduce="user => user"
            label="first_name" :options="filtredUsers">
        </VueSelect>
        <button type="submit" class="btn">Выбрать</button>
    </form>
</template>

<script>
import { useForm } from '@inertiajs/vue3';
import VueSelect from 'vue-select';
import { route } from 'ziggy-js';

export default {
    components: {
        VueSelect,
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
                user: this.initialUser || null,
                department: this.initialDepartment || null,
            }),
            filtredUsers: [],
        };
    },
    watch: {
        'form.department': 'filterUsers',
        'form.user': 'validateUser'
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
            if (this.form.user && !this.filtredUsers.some(user => user.id === this.form.user.id)) {
                this.form.user = null;
            }
        },
        submitForm() {
            this.form.user = this.form.user?.id || null;
            this.form.department = this.form.department.id
            this.form.get(route('admin.sale-department.user-report'));
        }
    }
};
</script>