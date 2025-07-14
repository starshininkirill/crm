<template>
    <h1 class="text-2xl font-semibold mb-6">
        Сформировать выплату
    </h1>
    <div class="flex flex-col gap-4">
        <p class="text-gray-600">Выберите типы трудоустройства для включения в отчет.</p>

        <div class="grid grid-cols-2 gap-2">
            <div v-for="employmentType in employmentTypes" :key="employmentType.id" class="flex items-center">
                <input type="checkbox" :id="'employment_type_' + employmentType.id" :value="employmentType.id"
                    v-model="selectedEmploymentTypes"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label :for="'employment_type_' + employmentType.id" class="ml-2 block text-sm text-gray-900">
                    {{ employmentType.name }}
                </label>
            </div>
        </div>

        <div v-if="error" class="text-red-500 text-sm mt-2">
            {{ error }}
        </div>

        <div class="btn col-span-3 mt-4" @click="exportSalary()" :class="{ 'opacity-50': isLoading }"
            :disabled="isLoading">
            {{ isLoading ? 'Формирование...' : 'Сформировать и скачать' }}
        </div>
    </div>
</template>

<script>
import { route } from 'ziggy-js';
import axios from 'axios';

export default {
    props: {
        employmentTypes: {
            type: Array,
            required: true,
        },
        half: {
            type: Number,
            required: true,
        },
        date: {
            type: String,
            required: true,
        },
        department_id: {
            type: Number,
            default: null,
        }
    },
    data() {
        return {
            selectedEmploymentTypes: [],
            isLoading: false,
            error: null,
        }
    },
    emits: ['close'],
    methods: {
        exportSalary() {
            if (this.selectedEmploymentTypes.length === 0) {
                this.error = 'Выберите хотя бы один тип трудоустройства.';
                return;
            }

            this.isLoading = true;
            this.error = null;

            axios.get(route('admin.time-sheet.export-payments'), {
                params: {
                    date: this.date,
                    half: this.half,
                    department_id: this.department_id,
                    employment_type_ids: this.selectedEmploymentTypes,
                },
                responseType: 'blob',
            })
                .then(response => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    const contentDisposition = response.headers['content-disposition'];

                    let filename = 'export.xlsx';
                    const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                    const matches = filenameRegex.exec(contentDisposition);

                    if (matches != null && matches[1]) {
                        filename = matches[1].replace(/['"]/g, '');
                    }

                    link.href = url;
                    link.download = filename;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    window.URL.revokeObjectURL(url);
                    this.$emit('close');
                })
                .catch(error => {
                    console.error(error);
                    if (error.response && error.response.status === 422) {
                        this.error = 'Ошибка валидации. Проверьте переданные данные.';
                    } else {
                        this.error = 'Не удалось сформировать отчет. Попробуйте еще раз.';
                    }
                })
                .finally(() => {
                    this.isLoading = false;
                });
        }
    }
}
</script>