<template>
    <label class="flex items-center space-x-3 cursor-pointer w-fit">
        <span class="text-sm font-medium text-gray-700">
            <slot>{{ label }}</slot>
        </span>
        <div class="relative">
            <input type="checkbox" :checked="modelValue" @change="$emit('update:modelValue', $event.target.checked)"
                class="sr-only">
            <div :class="{ 'bg-indigo-500': modelValue, 'bg-gray-300': !modelValue }"
                class="block w-14 h-8 rounded-full transition-colors"></div>
            <div :class="{ 'translate-x-6': modelValue, 'translate-x-1': !modelValue }"
                class="absolute left-0 top-1 bg-white w-6 h-6 rounded-full transition-transform"></div>
        </div>
        <span class="text-sm font-medium text-gray-700">
            {{ modelValue ? 'Да' : 'Нет' }}
        </span>
    </label>
</template>

<script>
export default {
    name: 'ToggleSwitch',
    props: {
        modelValue: {
            type: Boolean,
            required: true
        },
        label: {
            type: String,
            default: 'Выбрано:'
        }
    },
    emits: ['update:modelValue']
}
</script>