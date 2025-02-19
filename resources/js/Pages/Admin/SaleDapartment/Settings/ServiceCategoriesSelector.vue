<template>
    <div class="flex flex-col gap-2">
        <div v-if="title" class="text-lg font-semibold">{{ title }}</div>
        <div v-for="(category, idx) in selectedCategories" :key="idx">
            <label class="label">Категория {{ idx + 1 }}</label>
            <div class="flex items-center gap-2 w-full">
                <VueSelect v-if="isEditable" class="full-vue-select" :reduce="cat => cat" label="name"
                    :options="availableCategories" v-model="selectedCategories[idx].selectedCategory"
                    @update:modelValue="updateSelectedCategories" />
                <div v-if="isEditable" type="button"
                    class="text-red-400 cursor-pointer hover:text-red-500 w-fit whitespace-nowrap"
                    @click="removeCategory(idx)">
                    Удалить категорию
                </div>
                <div v-if="!isEditable" class="text-lg">
                    {{ selectedCategories[idx].selectedCategory?.name }}
                </div>
            </div>
        </div>
        <div v-if="canAddCategory && isEditable" class="btn-green" @click="addCategory">
            Добавить категорию
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
            default: ''
        },
        initialCategories: {
            type: Array,
            required: true,
        },
        allCategories: {
            type: Array,
            required: true,
        },
        isEditable: {
            type: Boolean,
            default: true,
        },
    },
    data() {
        return {
            selectedCategories: this.initialCategories.map(cat => ({ selectedCategory: cat })),
        };
    },
    computed: {
        availableCategories() {
            const selectedCategoryIds = this.selectedCategoryIds;
            return this.allCategories.filter(cat => !selectedCategoryIds.includes(cat.id));
        },
        selectedCategoryIds() {
            return this.selectedCategories
                .filter(item => item.selectedCategory !== null)
                .map(item => item.selectedCategory.id);
        },
        canAddCategory() {
            return this.selectedCategories.every(item => item.selectedCategory !== null) &&
                this.availableCategories.length > 0;
        },
    },
    methods: {
        addCategory() {
            if (!this.canAddCategory) return;
            this.selectedCategories.push({ selectedCategory: null });
        },
        removeCategory(index) {
            this.selectedCategories.splice(index, 1);
            this.updateSelectedCategories();
        },
        updateSelectedCategories() {
            this.$emit('update:selected-categories', this.selectedCategoryIds);
        },
    },
    watch: {
        initialCategories(newVal) {
            this.selectedCategories = newVal.map(cat => ({ selectedCategory: cat }));
        },
    },
};
</script>