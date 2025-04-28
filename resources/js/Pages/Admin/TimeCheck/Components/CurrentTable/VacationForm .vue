<template>

    <div class="flex flex-col gap-3">
        <h2 class="text-xl font-bold mb-4">{{ user.full_name }} - Проставить отпуск</h2>
        <Error />
        <div class="grid grid-cols-2 gap-3">
            <div class="flex flex-col">
                <label class="label">Даты</label>
                <VueDatePicker v-model="modelDates" locale="ru" format="yyyy-MM-dd" model-type="yyyy-MM-dd" range />
            </div>
        </div>
        <div class="btn" @click="$emit('save')">Проставить</div>
    </div>
</template>

<script>
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import Error from '../../../../../Components/Error.vue'
import Modal from '../../../../../Components/Modal.vue';

export default {
    components: {
        VueDatePicker,
        Error,
        Modal
    },
    props: {
        user: Object,
        dates: Array,
        workStatusId: [String, Number, null],
    },
    emits: ['update:dates', 'close', 'save'],
    computed: {
        modelDates: {
            get() { return this.dates },
            set(val) { this.$emit('update:dates', val) },
        },
    }
}
</script>