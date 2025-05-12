<template>


    <form @submit.prevent="submitForm" class="flex flex-col gap-2">
        <Error />

        <div class="grid grid-cols-2 gap-4 gap-y-2">
            <FormInput type="text" required v-model="form.first_name" name="first_name" label="Имя" placeholder="Имя" />
            <FormInput type="text" required v-model="form.last_name" name="last_name" label="Фамилия"
                placeholder="Фамилия" />
            <FormInput type="text" required v-model="form.surname" name="surname" label="Отчество"
                placeholder="Отчество" />
            <div></div>

            <FormInput type="email" required v-model="form.email" name="email" label="Почта" placeholder="Почта" />
            <FormInput type="password" required v-model="form.password" name="password" label="Пароль"
                placeholder="Пароль" />
            <FormInput type="phone" required v-model="form.work_phone" name="work_phone" label="Рабочий телефон"
                placeholder="Рабочий телефон" />
            <FormInput type="number" required v-model="form.bitrix_id" name="bitrix_id" label="Битрикс ID"
                placeholder="Битрикс ID" />
        </div>

        <div class="flex flex-col">
            <span class="label">
                Тип устройства
            </span>
            <VueSelect v-model="form.employment_type_id" :reduce="type => type.id" label="name"
                :options="employmentTypes">
            </VueSelect>
        </div>
        <div v-if="form.employment_type_id" class="grid grid-cols-2 gap-y-2 gap-x-4">
            <FormInput type="number" required maxLen="20" v-model="form.payment_account" name="payment_account"
                label="Расчётный счёт" placeholder="Расчётный счёт" info="Должен содержать 20 символов" />
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
            <VueSelect v-model="form.position_id" :reduce="position => position.id" label="name" :options="positions">
            </VueSelect>
        </div>


        <div v-if="form.position_id" class="grid grid-cols-2 gap-4">
            <FormInput type="number" v-model="form.salary" name="salary" readonly label="Ставка" placeholder="Ставка" />
            <FormInput type="number" v-model="form.personal_salary" name="personal_salary"
                info="Заполнить если ставка сотрудника отличается от стандартной ставки для должности"
                label="Персональная ставка" placeholder="Персональная ставка" />
            <div v-if="selectedPositionHasProbation">
                <div class="label">
                    Испытательный срок
                    <Info text="Заполнить если для сотрудника предусмотрен испытательный срок" />
                </div>

                <VueDatePicker v-model="form.probation_dates" locale="ru" format="yyyy-MM-dd" model-type="yyyy-MM-dd"
                    range />
            </div>
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
import Info from '../../../../Components/Info.vue';
import VueDatePicker from '@vuepic/vue-datepicker';

export default {
    components: {
        FormInput,
        VueSelect,
        Error,
        Info,
        VueDatePicker
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
            'surname': null,
            'payment_account': null,
            'email': null,
            'password': null,
            'salary': null,
            'personal_salary': null,
            'probation_dates': null,
            'department_id': null,
            'position_id': null,
            'employment_type_id': null,
            'bitrix_id': null,
            'work_phone': null,
            'details': [],
        });

        return {
            form,
            fields: [],
        };
    },
    watch: {
        'form.position_id': {
            handler: 'updateSalary',
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

            this.form.post(route('admin.user.store'), {
                onSuccess() {
                    th.form.first_name = null;
                    th.form.last_name = null;
                    th.form.surname = null,
                        th.form.payment_account = null,
                        th.form.email = null;
                    th.form.work_phone = null,
                        th.form.bitrix_id = null,
                        th.form.password = null;
                    th.form.salary = null;
                    th.form.personal_salary = null,
                        th.form.probation_dates = null;
                    th.form.department_id = null;
                    th.form.position_id = null;
                    th.form.employment_type_id = null;
                    th.form.details = [];
                },
            });
        },
        updateSalary() {
            let position = this.positions.find(department => department.id === this.form.position_id)
            if (position) {
                this.form.salary = position.salary
            }

        },
        setEmploymentFields() {
            let activeEmploymentType = this.employmentTypes.find(type => type.id === this.form.employment_type_id);
            if (activeEmploymentType && activeEmploymentType.fields) {
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
    computed: {
        selectedPositionHasProbation() {
            if (!this.form.position_id) return false;
            const position = this.positions.find(d => d.id === this.form.position_id);
            return position ? position.has_probation : false;
        }
    },
};
</script>