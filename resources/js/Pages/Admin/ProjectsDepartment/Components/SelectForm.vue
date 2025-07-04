<template>

    <div class="w-fit max-w-[150px] flex flex-col mb-6">
        <label class="label">Дата</label>
        <VueDatePicker v-model="form.date" model-type="yyyy-MM" :auto-apply="true" month-picker locale="ru"
            class="h-full" @update:model-value="updateDate" />
    </div>

</template>

<script>
import { useForm, router } from '@inertiajs/vue3';
import VueSelect from 'vue-select';
import { route } from 'ziggy-js';
import VueDatePicker from '@vuepic/vue-datepicker'


export default {
    components: {
        VueSelect,
        VueDatePicker
    },
    props: {
        initialDate: String,
    },
    data() {

        return {
            form: useForm({
                date: this.initialDate || new Date().toISOString().slice(0, 7),
            }),
        };
    },
    methods: {
        updateDate() {
            this.form.user = this.selectedUser?.id || null;
            this.form.get(route('admin.projects-department.report'), {
                // preserveScroll: true,
            });
        },
    }
};
</script>