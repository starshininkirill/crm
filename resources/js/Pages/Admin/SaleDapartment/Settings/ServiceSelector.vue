<template>
    <div class="flex flex-col gap-2">
        <div v-if="title" class="text-lg font-semibold">{{ title }}</div>
        <div v-for="(select, idx) in selectedServices" :key="idx">
            <label class="label">Услуга {{ idx + 1 }}</label>
            <div class="flex items-center gap-2 w-full">
                <VueSelect v-if="isEditable" class="full-vue-select" :reduce="service => service" label="name"
                    :options="availableOptions" v-model="selectedServices[idx].selectedService"
                    @update:modelValue="updateSelectedServices" />
                <div v-if="isEditable" type="button"
                    class="text-red-400 cursor-pointer hover:text-red-500 w-fit whitespace-nowrap"
                    @click="removeService(idx)">
                    Удалить услугу
                </div>
                <div v-if="!isEditable" class="text-lg">
                    {{ selectedServices[idx].selectedService?.name }}
                </div>
            </div>
        </div>
        <div v-if="canAddService && isEditable" class="btn-green" @click="addService">
            Добавить услугу
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
        initialServices: {
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
        return {
            selectedServices: this.initialServices.map(service => ({ selectedService: service })),
        };
    },
    computed: {
        availableOptions() {
            const selectedIds = this.selectedIds;
            return this.allOptions.filter(option => !selectedIds.includes(option.id));
        },
        selectedIds() {            
            return this.selectedServices
                .filter(item => item.selectedService !== null)
                .map(item => item.selectedService?.id);
        },
        canAddService() {
            return this.selectedServices.every(item => item.selectedService !== null) &&
                this.availableOptions.length > 0;
        },
    },
    methods: {
        addService() {
            if (!this.canAddService) return;
            this.selectedServices.push({ selectedService: null });
        },
        removeService(index) {
            this.selectedServices.splice(index, 1);
            this.updateSelectedServices();
        },
        updateSelectedServices() {
            this.$emit('update:selected-services', this.selectedIds);
        },
    },
    watch: {
        initialServices(newVal) {
            this.selectedServices = newVal.map(service => ({ selectedService: service }));
        },
    },
};
</script>