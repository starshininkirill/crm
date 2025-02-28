<template>


    <form @submit.prevent="submitForm" class="flex flex-col gap-2">
        <Error />

        <div class="grid grid-cols-2 gap-4">
            <FormInput type="text" required v-model="form.first_name" name="first_name" label="Имя" placeholder="Имя" />
            <FormInput type="text" required v-model="form.last_name" name="last_name" label="Фамилия"
                placeholder="Фамилия" />
        </div>

        <div class="grid grid-cols-2 gap-4">
            <FormInput type="email" required v-model="form.email" name="email" label="Почта" placeholder="Почта" />
            <FormInput type="password" required v-model="form.password" name="password" label="Пароль"
                placeholder="Пароль" />
        </div>


        <div class="grid grid-cols-2 gap-4">
            <FormInput type="number" v-model="form.salary" name="salary" label="Персональная ставка"
                placeholder="Персональная ставка"
                info="Заполните если персональная ставка сотрудника отличатся от ставки по должности" />
            <div>
                <div class="label">
                    Испытательный срок?
                </div>
                <div class="grid grid-cols-2">
                    <label class="flex gap-1 items-center cursor-pointer w-fit">
                        <input v-model="form.probation" checked type="radio" name="probation" value="1">
                        Да
                    </label>
                    <label class="flex gap-1 items-center cursor-pointer w-fit">
                        <input v-model="form.probation" type="radio" name="probation" value="0">
                        Нет
                    </label>
                </div>
            </div>
        </div>

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
                :label="field.readName" :placeholder="field.readName" v-model="form.details[index].value" required />
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
</template>

<script>
import { useForm } from '@inertiajs/vue3';
import FormInput from '../../../../Components/FormInput.vue';
import VueSelect from 'vue-select';
import Error from '../../../../Components/Error.vue'

export default {
    components: {
        FormInput,
        VueSelect,
        Error,
    },
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
            'email': null,
            'password': null,
            'salary': null,
            'probation': 1,
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
            let th = this;
            console.log(th);

            this.form.post(route('admin.user.store'), {
                onSuccess() {
                    th.form.first_name = null;
                    th.form.last_name = null;
                    th.form.email = null;
                    th.form.password = null;
                    th.form.salary = null;
                    th.form.probation = 1;
                    th.form.department_id = null;
                    th.form.position_id = null;
                    th.form.employment_type_id = null;
                    th.form.details = [];
                },
            });
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
                    readName: field.readName,
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