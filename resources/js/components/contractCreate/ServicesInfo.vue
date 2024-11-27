<template>
    <div class="flex flex-col w-full mb-6">
        <div class="flex flex-col rounded-md border border-gray-400 shadow-xl">
            <div class="bg-gray-800 p-2 rounded-md text-white font-semibold text-xl">
                Услуги
            </div>
            <div v-show="showForm" class="flex flex-col gap-4 p-2 mt-2">
                <div class="flex flex-col gap-5">

                    <div class="flex flex-col gap-2">
                        <label for="service" class="block text-2xl font-semibold leading-6 text-gray-900">
                            Услуга 1
                        </label>
                        <div class="grid grid-cols-2 items-end gap-3">
                            <select id="service" name="service[]" v-model="servicePrices[0].service"
                                @change="updateService(0, $event)"
                                class="block h-fit w-full rounded-md border-0 py-2 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <option selected disabled>
                                    Выберите услугу
                                </option>
                                <optgroup :label="cat.category" v-for="cat in mainCats" :key="cat.category">
                                    <option v-for="service in cat.services" :key="service.id" :value="service.id"
                                        :data-price="service.price" :data-duration="service.work_days_duration"
                                        :data-isRk="service.isRk" :data-isReady="service.isReady"
                                        :data-isSeo="service.isSeo">
                                        {{ service.name }}
                                    </option>
                                </optgroup>
                            </select>
                            <vue-form-input type="number" name="service_price[]" placeholder="Стоимость услуги"
                                v-model="servicePrices[0].price" label="Стоимость услуги" />
                            <vue-form-input type="hidden" name="service_duration[]"
                                v-model="servicePrices[0].duration" />
                        </div>
                    </div>

                    <div v-for="(servicePrice, index) in servicePrices.slice(1, visibleServices)" :key="index"
                        class="flex flex-col gap-2">
                        <label :for="'service-' + (index + 1)"
                            class="block text-2xl font-semibold leading-6 text-gray-900">
                            Услуга {{ index + 2 }}
                        </label>
                        <div class="grid grid-cols-2 items-end gap-3">
                            <select :id="'service-' + (index + 1)" name="service[]"
                                @change="updateService(index + 1, $event)" v-model="servicePrice.service"
                                class="block h-fit w-full rounded-md border-0 py-2 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <option selected disabled>Выберите услугу {{ index + 2 }}</option>
                                <option v-for="service in secondaryCats" :key="service.id" :value="service.id"
                                    :data-price="service.price" :data-duration="service.work_days_duration"
                                    :data-isRk="service.isRk" :data-isReady="service.isReady"
                                    :data-isSeo="service.isSeo">
                                    {{ service.name }}
                                </option>
                            </select>
                            <vue-form-input type="number" name="service_price[]" placeholder="Стоимость услуги"
                                v-model="servicePrice.price" label="Стоимость услуги" />
                            <vue-form-input type="hidden" name="service_duration[]" v-model="servicePrice.duration" />
                        </div>
                    </div>

                    <div v-if="isReady" class="grid grid-cols-2 gap-3">
                        <vue-form-input type="text" required name="ready_site_link"
                            placeholder="Ссылка на готовый дизайн" label="Ссылка на готовый дизайн" />
                        <vue-form-input type="file" required name="ready_site_image"
                            placeholder="Скриншот главной страницы готового дизайна (для приложения №2)"
                            label="Скриншот главной страницы готового дизайна (для приложения №2)" />
                    </div>
                    <vue-form-input v-if="isSeo" type="number" v-model="seoPages" required name="seo_pages"
                        placeholder="Кол-во страниц SEO" label="Кол-во страниц SEO" />
                    <vue-form-input v-if="isRk" type="hidden" :value="rkText" required name="rk_text" />
                </div>

                <div class="flex gap-3">
                    <div v-if="visibleServices < 6" class="btn" @click="addService">
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
        mainCatsIds: {
            type: Array,
            required: true
        },
        secondaryCatsIds: {
            type: Array,
            required: true
        },
        servicePrices: {
            type: Array,
            required: true
        },
        valid: {
            type: Boolean,
            required: true,
        },
        isRk: {
            type: Boolean,
            required: true,
        },
        isSeo: {
            type: Boolean,
            required: true,
        },
        isReady: {
            type: Boolean,
            required: true,
        },
        old: {
            Array
        },
        rkText: {
            type: String,
        }
    },
    data() {
        let visibleServices = this.servicePrices.filter(item => item.service !== 0).length
        if (visibleServices == 0) {
            visibleServices = 1
        }

        return {
            seoPages: this.old?.seo_pages !== undefined ? this.old.seo_pages : 0,
            showForm: true,
            allCats: this.cats,
            mainCats: this.cats.filter(cat => this.mainCatsIds.map(Number).includes(cat.id)),
            secondaryCats: this.cats.filter(cat => this.secondaryCatsIds.map(Number).includes(cat.id)).flatMap(cat => cat.services),
            visibleServices: visibleServices,
        };
    },
    methods: {
        checkValidFieldsFilled() {
            let valid = this.servicePrices.slice(0, this.visibleServices).every(service => {
                return service.service !== 0 && service.price > 0;
            });

            if (this.isSeo) {
                valid = valid && this.seoPages != '' && this.seoPages != 0;
            }

            return valid


        },
        updateService(index, event) {
            const selectedOption = event.target.options[event.target.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            const duration = selectedOption.getAttribute('data-duration');
            const isRk = selectedOption.getAttribute('data-isRk');
            const isSeo = selectedOption.getAttribute('data-isSeo');
            const isReady = selectedOption.getAttribute('data-isReady');
            this.$emit('updateService', index, price, duration, isRk, isSeo, isReady)
        },
        addService() {
            if (this.visibleServices < 6) {
                this.visibleServices += 1;
            }
        },
        removeService() {
            if (this.visibleServices > 1) {
                this.servicePrices[this.visibleServices - 1].service = 0
                this.servicePrices[this.visibleServices - 1].price = 0
                this.servicePrices[this.visibleServices - 1].duration = 0
                this.servicePrices[this.visibleServices - 1].isRk = false
                this.servicePrices[this.visibleServices - 1].isSeo = false
                this.servicePrices[this.visibleServices - 1].isReady = false
                this.visibleServices -= 1;
            }
        },
    },
    watch: {
        visibleServices: {
            handler() {
                this.$emit('update:valid', this.checkValidFieldsFilled());
            }
        },
        servicePrices: {
            handler() {
                this.$emit('update:valid', this.checkValidFieldsFilled());
            },
            deep: true
        },
        seoPages: {
            handler() {
                this.$emit('update:valid', this.checkValidFieldsFilled());
            },
        }
    },
    mounted() {
        this.$emit('update:valid', this.checkValidFieldsFilled());
    },
}
</script>