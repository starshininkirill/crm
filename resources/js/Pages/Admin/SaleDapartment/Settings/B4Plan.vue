<template>
    <div class="flex flex-col gap-3">
        <div class="text-2xl font-semibold mb-2">
            Б4 План
        </div>
        <form class="flex flex-col gap-2" @submit.prevent="submitForm(plan)">
            <div class="grid grid-cols-2 gap-3">
                <FormInput :disabled="!isCurrentMonth" v-model="plan.data.goal" type="number" name="goal" placeholder="8" label="Цель"
                    autocomplete="goal" required />
                <FormInput :disabled="!isCurrentMonth" v-model="plan.data.bonus" type="number" name="bonus" placeholder="10000" label="Бонус"
                    autocomplete="bonus" required />
            </div>
            <div class=" text-lg font-semibold" v-if="isCurrentMonth">
                Выберите услуги, которые будут засчитываться в план
            </div>
            <div v-for="(select, idx) in selectServices">
                <label class="label">
                    Услуга {{ idx + 1 }}
                </label>
                <div class="flex items-center gap-2 w-full">
                    <VueSelect v-if="isCurrentMonth" class="full-vue-select"
                        :reduce="filtredServices => filtredServices" label="name" :options="filtredServices"
                        v-model="selectServices[idx].selectedService" @update:modelValue="updateServices" />
                    <div v-if="isCurrentMonth" type="button"
                        class="text-red-400 cursor-pointer hover:text-red-500 w-fit whitespace-nowrap"
                        @click="removeSelect(idx)">
                        Удалить услугу
                    </div>

                    <div v-if="!isCurrentMonth" class=" text-xl font-semibold">
                        {{ selectServices[idx].selectedService?.name }}
                    </div>
                </div>
            </div>
            <div v-if="filtredServices.length > 0 && this.selectServices.length < this.services.length && isCurrentMonth"
                class="btn-green" :class="isAddButtonDisabled ? 'opacity-60 !cursor-default' : ''" @click="addSelect">
                Добавить услугу
            </div>

            <button v-if="isCurrentMonth" class="btn" :class="isAddButtonDisabled ? 'opacity-60 !cursor-default' : ''">
                Сохранить
            </button>
        </form>
    </div>
</template>
<script>
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';
import VueSelect from 'vue-select';
import FormInput from '../../../../Components/FormInput.vue';

export default {
    components: {
        VueSelect,
        FormInput
    },
    props: {
        propServices: {
            type: Array,
            required: true,
        },
        departmentId: {
            type: Number,
            required: true,
        },
        isCurrentMonth: {
            type: Boolean,
            default: false
        },
        propPlan: {
            type: Object
        },
    },
    data() {
        let selectServices = [
            { selectedService: null },
        ];
        let plan = {
            data: {
                'bonus': null,
                'goal': null,
            },
            'type': 'b4Plan',
        }
        let create = true;

        if (this.propPlan && this.propPlan.length >= 1) {
            plan = this.propPlan[0];
            const selectedServiceIds = plan.data.includeIds;
            create = false;

            if (selectedServiceIds && selectedServiceIds.length > 0) {
                selectServices = this.propServices
                    .filter(service => selectedServiceIds.includes(service.id))
                    .map(service => ({
                        selectedService: service
                    }));
            }
        }
        return {
            services: this.propServices,
            filtredServices: this.propServices,
            selectServices,
            create,
            plan
        }
    },
    methods: {
        submitForm() {
            if (this.isAddButtonDisabled) {
                return;
            }
            if (this.create) {
                this.createPlan()
            } else {
                this.updatePlan()
            }
        },
        createPlan() {
            router.post(route('admin.sale-department.work-plan.store'), {
                'data': {
                    'includeIds': this.includeIds,
                    'goal': this.plan.data.goal,
                    'bonus': this.plan.data.bonus
                },
                'type': 'b4Plan',
                'department_id': this.departmentId,
            }, {
                onSuccess() {
                    this.create = false
                },
            })
        },
        updatePlan() {
            router.put(route('admin.sale-department.work-plan.update', { workPlan: this.plan }), {
                'data': {
                    'includeIds': this.includeIds,
                    'goal': this.plan.data.goal,
                    'bonus': this.plan.data.bonus
                },
                'type': 'b4Plan',
            })
        },
        addSelect() {
            if (this.isAddButtonDisabled) {
                return;
            }

            this.selectServices.push({ selectedService: null });
        },
        removeSelect(index) {
            this.selectServices.splice(index, 1);
            if (this.selectServices.length == 0) {
                this.filtredServices = this.services;
            }
            this.updateServices()
        },
        updateServices() {
            this.filtredServices = this.propServices.filter(service => {
                return !this.includeIds.includes(service.id);
            });
        }
    },
    mounted() {
        this.updateServices();
    },
    computed: {
        isAddButtonDisabled() {
            return this.selectServices.some(item => item.selectedService === null);
        },
        includeIds() {
            return this.selectServices
                .filter(item => item.selectedService !== null)
                .map(item => item.selectedService.id);
        }
    },
}


</script>