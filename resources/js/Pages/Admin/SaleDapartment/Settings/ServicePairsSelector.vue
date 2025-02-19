<template>
    <div class="flex flex-col gap-2">
        <div v-if="title" class="text-lg font-semibold">{{ title }}</div>
        <div v-for="(pair, idx) in selectedPairs" :key="idx" class="flex items-center gap-4">
            <VueSelect v-if="isEditable" class="full-vue-select" :reduce="service => service" label="name"
                :options="allOptions" v-model="selectedPairs[idx][0]" @update:modelValue="updateSelectedPairs" />
            <VueSelect v-if="isEditable" class="full-vue-select" :reduce="service => service" label="name"
                :options="allOptions" v-model="selectedPairs[idx][1]" @update:modelValue="updateSelectedPairs" />
            <div v-if="isEditable" class="btn-green !w-fit" :class="!canAddPair ? 'opacity-60 !cursor-default' : ''"
                @click="addPair">
                и
            </div>
            <div v-if="isEditable" type="button"
                class="w-10 h-10 text-white bg-red-500 flex items-center justify-center shrink-0 rounded-full cursor-pointer"
                @click="removePair(idx)">
                <span class=" w-1/3 h-1 bg-white"></span>
            </div>
            <div v-if="!isEditable" class="text-lg">
                {{ selectedPairs[idx][0]?.name }} И {{ selectedPairs[idx][1]?.name }}
            </div>
        </div>
    </div>
</template>

<script>
import VueSelect from 'vue-select';

export default {
    components: {
        VueSelect,
    },
    props: {
        title: {
            type: String,
            required: false,
            default: '',
        },
        initialPairs: {
            type: Array,
            required: true,
        },
        allOptions: {
            type: Array,
            required: true,
        },
        isEditable: {
            type: Boolean,
            default: true,
        },
    },
    data() {
        const initialData = this.initialPairs.length > 0
            ? this.initialPairs.map(pair => [pair.firstService, pair.secondService])
            : [[null, null]];
        return {
            selectedPairs: initialData,
        };
    },
    computed: {
        canAddPair() {
            const lastPair = this.selectedPairs[this.selectedPairs.length - 1];
            if (!lastPair[0] || !lastPair[1]) {
                return false;
            }
            return true;
        },
    },
    methods: {
        addPair() {
            if (!this.canAddPair) return;
            this.selectedPairs.push([null, null]);
        },
        removePair(index) {
            if (this.selectedPairs.length === 1) return;
            this.selectedPairs.splice(index, 1);
            this.updateSelectedPairs();
        },
        updateSelectedPairs() {
            // Создаем копию массива, чтобы не изменять исходные данные напрямую
            const pairs = this.selectedPairs.map(pair => [
                pair[0] ? pair[0].id : null,
                pair[1] ? pair[1].id : null,
            ]);
            // Эмитируем событие со всеми парами, включая незаполненные
            this.$emit('update:selected-pairs', pairs);
        }
    },
    watch: {
        initialPairs(newVal) {
            this.selectedPairs = newVal.map(pair => [pair.firstService, pair.secondService]);
            if (this.selectedPairs.length === 0) {
                this.selectedPairs.push([null, null]);
            }
        },
    },
};
</script>