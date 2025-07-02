<template>
    <div class="mb-6">
        <h2 class="text-xl font-semibold mb-3">Статус договора</h2>
        <div class="relative flex items-start justify-between py-2.5 w-fit">
            <div class="absolute left-0 top-5 h-[3px] w-full bg-gray-200 z-10"></div>
            
            <div 
                v-for="state in all_states" 
                :key="state.value"
                class="relative z-20 flex w-20 flex-col items-center text-center"
            >
                <div 
                    class="h-6 w-6 rounded-full border-[3px] transition-all duration-300 ease-in-out mb-2"
                    :class="{
                        'border-green-600 bg-green-600': state.order < current_state.order,
                        'scale-125 border-blue-600 bg-white': state.order === current_state.order,
                        'border-gray-200 bg-gray-100': state.order > current_state.order,
                    }"
                ></div>
                <div 
                    class="text-sm font-medium"
                    :class="{
                        'text-gray-800': state.order < current_state.order,
                        'font-semibold text-blue-600': state.order === current_state.order,
                        'text-gray-400': state.order > current_state.order
                    }"
                >{{ state.name }}</div>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    all_states: {
        type: Array,
        required: true,
    },
    current_state: {
        type: Object,
        required: true,
    },
});
</script> 