<template>
    <div class="relative z-50 w-fit h-fit flex items-center justify-center">
        <div class="border cursor-pointer border-gray-500 text-xs text-gray-500 p-1 rounded-full w-4 h-4 flex items-center justify-center"
            @mouseenter="showTooltip = true" @mouseleave="showTooltip = false">
            ?
        </div>

        <div v-if="showTooltip"
            class="absolute bottom-[1.5rem] left-24 transform -translate-x-1/2 bg-white border border-gray-300 rounded-md shadow-md w-48 px-2 py-1 text-xs text-gray-700 whitespace-pre-wrap">
            {{ text }}
        </div>
    </div>
</template>
<script>
export default {
    props: {
        text: {
            type: String,
            required: true
        },
    },
    data() {
        return {
            showTooltip: false, // Флаг для отображения тултипа
        };
    },
};
</script>