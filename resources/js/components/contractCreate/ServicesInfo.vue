<template>
    <div class="flex flex-col w-full mb-6">
        <div class="flex flex-col rounded-md border border-gray-400 shadow-xl">
            <div @click="toggleForm" class="bg-gray-800 p-2 rounded-md text-white font-semibold text-xl cursor-pointer">
                Услуги
            </div>
            <div v-show="showForm" class="flex flex-col gap-4 p-2 mt-2">
                <div class="flex flex-col gap-5">

                    <div class="flex flex-col gap-2">
                        <label for="service" class="block text-2xl font-semibold leading-6 text-gray-900">
                            Услуга 1
                        </label>
                        <div class="grid grid-cols-2 items-end gap-3">
                            <select id="service" name="service" @change="updateService(0, $event)"
                                class="block h-fit w-full rounded-md border-0 py-2 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <option selected disabled>
                                    Выберите услугу
                                </option>
                                <optgroup :label="cat.category" v-for="cat in allCats" :key="cat.category">
                                    <option v-for="service in cat.services" :key="service.id" :value="service.id"
                                        :data-price="service.price"
                                        :data-duration="service.work_days_duration"
                                        >
                                        {{ service.name }}
                                    </option>
                                </optgroup>
                            </select>
                            <vue-form-input type="number" name="service_price" placeholder="Стоимость услуги"
                                v-model="servicePrices[0].price" label="Стоимость услуги" />
                        </div>
                    </div>

                    <div v-if="visibleServices >= 2" class="flex flex-col gap-2">
                        <label for="service" class="block text-2xl font-semibold leading-6 text-gray-900">
                            Услуга 2
                        </label>
                        <div class="grid grid-cols-2 items-end gap-3">
                            <select id="service" name="service" @change="updateService(1, $event)"
                                class="block h-fit w-full rounded-md border-0 py-2 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <option selected disabled>
                                    Выберите услугу 2
                                </option>
                                <option v-for="service in secondaryCats" :key="service.id" :value="service.id"
                                    :data-price="service.price"
                                    :data-duration="service.work_days_duration">
                                    {{ service.name }}
                                </option>
                            </select>
                            <vue-form-input type="number" name="service_price" placeholder="Стоимость услуги"
                                v-model="servicePrices[1].price" label="Стоимость услуги" />
                        </div>
                    </div>

                    <div v-if="visibleServices >= 3" class="flex flex-col gap-2">
                        <label for="service" class="block text-2xl font-semibold leading-6 text-gray-900">
                            Услуга 3
                        </label>
                        <div class="grid grid-cols-2 items-end gap-3">
                            <select id="service" name="service" @change="updateService(2, $event)"
                                class="block h-fit w-full rounded-md border-0 py-2 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <option selected disabled>
                                    Выберите услугу 3
                                </option>
                                <option v-for="service in secondaryCats" :key="service.id" :value="service.id"
                                    :data-price="service.price"
                                    :data-duration="service.work_days_duration"
                                    >
                                    {{ service.name }}
                                </option>
                            </select>
                            <vue-form-input type="number" name="service_price" placeholder="Стоимость услуги"
                                v-model="servicePrices[2].price" label="Стоимость услуги" />
                        </div>
                    </div>

                    <div v-if="visibleServices >= 4" class="flex flex-col gap-2">
                        <label for="service" class="block text-2xl font-semibold leading-6 text-gray-900">
                            Услуга 4
                        </label>
                        <div class="grid grid-cols-2 items-end gap-3">
                            <select id="service" name="service" @change="updateService(3, $event)"
                                class="block h-fit w-full rounded-md border-0 py-2 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <option selected disabled>
                                    Выберите услугу 4
                                </option>
                                <option v-for="service in secondaryCats" :key="service.id" :value="service.id"
                                    :data-price="service.price"
                                    :data-duration="service.work_days_duration">
                                    {{ service.name }}
                                </option>
                            </select>
                            <vue-form-input type="number" name="service_price" placeholder="Стоимость услуги"
                                v-model="servicePrices[3].price" label="Стоимость услуги" />
                        </div>
                    </div>

                    <div v-if="visibleServices >= 5" class="flex flex-col gap-2">
                        <label for="service" class="block text-2xl font-semibold leading-6 text-gray-900">
                            Услуга 5
                        </label>
                        <div class="grid grid-cols-2 items-end gap-3">
                            <select id="service" name="service" @change="updateService(4, $event)"
                                class="block h-fit w-full rounded-md border-0 py-2 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <option selected disabled>
                                    Выберите услугу 5
                                </option>
                                <option v-for="service in secondaryCats" :key="service.id" :value="service.id"
                                    :data-price="service.price"
                                    :data-duration="service.work_days_duration">
                                    {{ service.name }}
                                </option>
                            </select>
                            <vue-form-input type="number" name="service_price" placeholder="Стоимость услуги"
                                v-model="servicePrices[4].price" label="Стоимость услуги" />
                        </div>
                    </div>

                    <div v-if="visibleServices >= 6" class="flex flex-col gap-2">
                        <label for="service" class="block text-2xl font-semibold leading-6 text-gray-900">
                            Услуга 6
                        </label>
                        <div class="grid grid-cols-2 items-end gap-3">
                            <select id="service" name="service" @change="updateService(5, $event)"
                                class="block h-fit w-full rounded-md border-0 py-2 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <option selected disabled>
                                    Выберите услугу 6
                                </option>
                                <option v-for="service in secondaryCats" :key="service.id" :value="service.id"
                                    :data-price="service.price"
                                    :data-duration="service.work_days_duration">
                                    {{ service.name }}
                                </option>
                            </select>
                            <vue-form-input type="number" name="service_price" placeholder="Стоимость услуги"
                                v-model="servicePrices[5].price" label="Стоимость услуги" />
                        </div>
                    </div>

                </div>

                <div class="flex gap-3">
                    <div class="btn" @click="addService">
                        Добавить услугу
                    </div>
                    <div v-if="visibleServices >= 2" class="btn" @click="removeService">
                        Удалить услугу
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        cats: {
            type: Array,
            required: true
        },
        servicePrices:{
            type: Array,
            required: true
        },
    },
    data() {
        return {
            showForm: true,
            allCats: this.cats,
            secondaryCats: this.cats.flatMap(cat => cat.services),
            visibleServices: 1,
        };
    },
    methods: {
        updateService (index, event){
            const selectedOption = event.target.options[event.target.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            const duration = selectedOption.getAttribute('data-duration');            
            this.$emit('updateService', index, price, duration)            
        },  
        toggleForm() {
            this.showForm = !this.showForm;
        },
        addService() {
            if (this.visibleServices < 6) {
                this.visibleServices += 1;
            }
        },
        removeService(){
            if (this.visibleServices > 1) {
                this.servicePrices[this.visibleServices - 1].price = 0
                this.servicePrices[this.visibleServices - 1].duration = 0                
                this.visibleServices -= 1;
            }
        },
    }
}
</script>
