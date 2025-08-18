<template>

    <div class="flex flex-col gap-3">
        <h2 class="text-xl font-bold mb-4">{{ user.full_name }} - Неполный рабочий день</h2>
        <div class="grid grid-cols-2 gap-3">
            <div class="flex flex-col">
                <label class="label">Начало рабочего дня</label>
                <VueDatePicker v-model="modelTimeStart" time-picker />
            </div>
            <div class="flex flex-col">
                <label class="label">Конец рабочего дня</label>
                <VueDatePicker v-model="modelTimeEnd" time-picker />
            </div>
        </div>
        <div class="btn" @click="$emit('save')">Сохранить</div>
        <button @click="$emit('close')"
            class="w-6 h-6 bg-red-500 text-white rounded hover:bg-red-600 absolute right-4 top-4">x</button>
    </div>
</template>

<script>
import VueDatePicker from '@vuepic/vue-datepicker';

export default {
    components: { VueDatePicker },
    props: {
        user: Object,
        timeStart: null,
        timeEnd: null,
    },
    emits: ['update:timeStart', 'update:timeEnd', 'close', 'save'],
    computed: {
        modelTimeStart: {
            get() { return this.timeStart },
            set(val) { this.$emit('update:timeStart', val) },
        },
        modelTimeEnd: {
            get() { return this.timeEnd },
            set(val) { this.$emit('update:timeEnd', val) },
        },
    }
}
</script>