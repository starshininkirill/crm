<template>
    <div class=" w-full">
        <h2 class="text-2xl font-semibold mb-4">{{ title }}</h2>
        <p class="mb-6 text-gray-600">
            Сотрудник: <span class="font-medium text-gray-900">{{ user.full_name }}</span>
        </p>
        <Error/>
        <form @submit.prevent="submit">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="new_value" class="label">Новое значение</label>
                    <input type="number" id="new_value" v-model="form.new_value" class="input" placeholder="Введите новую сумму">
                    <div v-if="form.errors.new_value" class="text-red-500 text-sm mt-1">{{ form.errors.new_value }}</div>
                </div>

                <div>
                    <label for="effective_date" class="label">Дата вступления в силу</label>
                    <VueDatePicker 
                        v-model="form.effective_date" 
                        model-type="yyyy-MM" 
                        format="yyyy-MM"
                        :auto-apply="true" 
                        month-picker
                        locale="ru"
                        :enable-time-picker="false"
                    />
                    <div v-if="form.errors.effective_date" class="text-red-500 text-sm mt-1">{{ form.errors.effective_date }}</div>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-4">
                <button type="submit" class="btn" :disabled="form.processing">
                    {{ form.processing ? 'Сохранение...' : 'Запланировать' }}
                </button>
            </div>
        </form>
    </div>
</template>

<script>
import { useForm } from '@inertiajs/vue3';
import VueDatePicker from '@vuepic/vue-datepicker';
import { route } from 'ziggy-js';
import Error from '../../../../Components/Error.vue';
import axios from 'axios';

export default {
    components: {
        VueDatePicker,
        Error
    },
    props: {
        user: {
            type: Object,
            required: true,
        },
        field: {
            type: String,
            required: true,
        },
        title: {
            type: String,
            required: true,
        },
        date: {
            type: String,
            required: true,
        },
    },
    emits: ['close', 'update-scheduled'],
    data() {
        return {
            form: useForm({
                user_id: this.user.id,
                updatable_type: 'user',
                field: this.field,
                new_value: null,
                effective_date: null,
                date: this.date,
            }),
        }
    },
    methods: {
        submit() {
            this.form.processing = true;
            axios.post(route('admin.time-sheet.schedule-salary-update.store'), this.form.data())
                .then(response => {
                    this.form.reset();
                    this.form.clearErrors();
                    this.$emit('update-scheduled', response.data.scheduled_update);
                    this.$emit('close');
                })
                .catch(error => {
                    if (error.response && error.response.status === 422) {
                        this.form.setError(error.response.data.errors);
                    }
                })
                .finally(() => {
                    this.form.processing = false;
                });
        }
    }
}
</script>