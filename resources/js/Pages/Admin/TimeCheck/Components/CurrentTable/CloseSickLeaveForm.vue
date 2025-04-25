<template>
    <div class="flex flex-col gap-3">
        <h2 class="text-xl font-bold mb-4">{{ user.full_name }} - Закрыть больничный</h2>
        <Error />
        <div class="grid grid-cols-2 gap-3">
            <div class="flex flex-col">
                <label class="label">Даты</label>
                <VueDatePicker v-model="modelDates" locale="ru" format="yyyy-MM-dd" model-type="yyyy-MM-dd" range />
            </div>
            <div>
                <div class="label">
                    Справка
                </div>
                <input @change="handleFileChange" type="file" class="input cursor-pointer" accept="image/*">
            </div>
        </div>
        <div class="btn" @click="$emit('save')">Закрыть больничный</div>
        <button @click="$emit('close')"
            class="w-6 h-6 bg-red-500 text-white rounded hover:bg-red-600 absolute right-4 top-4">x</button>
    </div>
</template>

<script>
import VueDatePicker from '@vuepic/vue-datepicker';
import Error from '../../../../../Components/Error.vue'

export default {
    components: { VueDatePicker, Error },
    props: {
        user: Object,
        dates: Array,
        image: {
            type: File,
            default: null,
        },
        workStatusId: [String, Number, null],
    },
    emits: ['update:dates', 'update:image', 'close', 'save'],
    methods: {
        handleFileChange(event) {
            const file = event.target.files[0];
            if (file) {
                this.$emit('update:image', file)
            }
        }
    },
    computed: {
        modelDates: {
            get() { return this.dates },
            set(val) { this.$emit('update:dates', val) },
        },
    }
}
</script>