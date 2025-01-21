<template>
    <div>
        <label :for="id" class="mb-1 block text-sm font-medium leading-6 text-gray-900">
            {{ label }}
        </label>
        <select :id="id" :name="name" v-model="selectedValue"
            class="block w-full rounded-md border-0 py-2 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
            <option v-for="option in options" :key="option.id" :value="option.id">
                {{ option.name }}
            </option>
        </select>
    </div>
</template>

<script>
export default {
    name: "IdSelectInput",
    props: {
        options: {
            type: Array,
            required: true,
        },
        label: {
            type: String,
            required: true,
        },
        name: {
            type: String,
            required: true,
        },
        id: {
            type: String,
            required: true,
        },
        modelValue: {
            type: [String, Number, null],
            default: null,
        },
    },
    computed: {
        selectedValue: {
            get() {
                return this.modelValue;
            },
            set(value) {
                this.$emit("update:modelValue", value);
            },
        },
    },
};
</script>