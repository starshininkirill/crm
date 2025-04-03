<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-lg p-6 w-1/3 max-h-[80vh] relative flex flex-col gap-3">
            <button @click="closeModal"
                class="w-6 h-6 bg-red-500 text-white rounded hover:bg-red-600 absolute right-4 top-4">
                x
            </button>

            <h2 class="text-xl font-bold mb-4">
                Проверить правило
            </h2>

            <form @submit.prevent="submitForm" class="mb-2">
                <div class="flex flex-col gap-2">
                    <div v-for="(service, idx) in selectedServices">
                        <div class="label">
                            Услуга {{ idx + 1 }}
                        </div>
                        <div class="flex gap-3 items-center w-full">
                            <VueSelect v-model="selectedServices[idx]" label="name" :options="filtredServices"
                                class="full-vue-select" @update:modelValue="selectService">
                            </VueSelect>
                            <div @click="removeService(idx)"
                                class="flex items-center justify-center rounded-full w-8 h-8 bg-red-400 flex-shrink-0 cursor-pointer">
                                <span class="w-1/4 h-0.5 bg-white"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-sm text-green-500 font-semibold cursor-pointer mt-2 mb-4" @click="addService">
                    Добавить услугу
                </div>

                <button class="btn">
                    Проверить
                </button>
            </form>

        </div>
    </div>
</template>

<script>
import VueSelect from 'vue-select';
import { useForm } from '@inertiajs/vue3';

export default {
    components: {
        VueSelect,
    },
    props: {
        services: {
            type: Array,
            required: true
        },
    },
    data() {
        return {
            form: useForm({
                services: [],
            }),
            filtredServices: this.services,
            selectedServices: [null],
        }
    },
    methods: {
        submitForm() {
            this.form.services = this.selectedServices.map(service => service?.id);
            this.form.get(route('admin.document-selection-rule.check'), {
                onSuccess: () => {
                    this.form.services = [null];
                    this.filtredServices = this.services;
                },
            });
        },
        closeModal() {
            this.$emit('closeModal');
        },
        selectService(newId) {
            this.filtredServices = this.filtredServices.filter(function (el) {
                return el.id != newId.id;
            })
        },
        removeService(idx) {
            if (idx > 0 && idx < this.selectedServices.length) {
                this.selectedServices.splice(idx, 1);
            }
        },
        addService() {
            this.selectedServices.push(null);
        },

    }
}
</script>