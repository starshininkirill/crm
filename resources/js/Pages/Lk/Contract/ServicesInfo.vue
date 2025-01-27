<template>
    <div class="flex flex-col w-full mb-6">
        <div class="flex flex-col rounded-md border border-gray-400 shadow-xl">
            <div class="bg-gray-800 p-2 rounded-md text-white font-semibold text-xl">
                Услуги
            </div>
        </div>
        <div class="flex flex-col gap-4 p-2 mt-2">
            <div class="flex flex-col gap-5">

                <div class="flex flex-col gap-2">
                    <label for="service" class="block text-2xl font-semibold leading-6 text-gray-900">
                        Услуга 1
                    </label>
                    <div class="grid grid-cols-2 items-end gap-3">
                        <select id="service" name="services[]" v-model="form.services[0].service_id"
                            @change="updateService(0, $event)"
                            class="block h-fit w-full rounded-md border-0 py-2 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option selected disabled>
                                Выберите услугу
                            </option>
                            <optgroup :label="cat.category" v-for="cat in mainCats" :key="cat.category">
                                <option v-for="service in cat.services" :key="service.id" :value="service.id"
                                    :data-price="service.price" :data-duration="service.work_days_duration"
                                    :data-isRk="service.isRk" :data-isReady="service.isReady"
                                    :data-isSeo="service.needSeoPages">
                                    {{ service.name }}
                                </option>
                            </optgroup>
                        </select>
                        <FormInput type="number" name="service[0][price]" placeholder="Стоимость услуги"
                            v-model="form.services[0].price" label="Стоимость услуги" />
                        <FormInput type="hidden" name="service[0][duration]" v-model="form.services[0].duration" />
                    </div>
                </div>

                <div v-for="(service, index) in form.services.slice(1, visibleServices)" :key="index"
                    class="flex flex-col gap-2">
                    <label :for="'service-' + (index + 1)" class="block text-2xl font-semibold leading-6 text-gray-900">
                        Услуга {{ index + 2 }}
                    </label>
                    <div class="grid grid-cols-2 items-end gap-3">
                        <select :id="'service-' + (index + 1)" name="service[]"
                            @change="updateService(index + 1, $event)" v-model="service.service_id"
                            class="block h-fit w-full rounded-md border-0 py-2 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                            <option selected disabled>Выберите услугу {{ index + 2 }}</option>
                            <option v-for="service in secondaryCats" :key="service.id" :value="service.id"
                                :data-price="service.price" :data-duration="service.work_days_duration"
                                :data-isRk="service.isRk" :data-isReady="service.isReady"
                                :data-isSeo="service.needSeoPages">
                                {{ service.name }}
                            </option>
                        </select>
                        <FormInput type="number" :name="`service[${index + 1}][price]`" placeholder="Стоимость услуги"
                            v-model="service.price" label="Стоимость услуги" />
                        <FormInput type="hidden" :name="`service[${index + 1}][duration]`" v-model="service.duration" />
                    </div>
                </div>

                <div v-if="isReady" class="grid grid-cols-2 gap-3">
                    <FormInput v-model="form.ready_site_link" type="text" required name="ready_site_link"
                        placeholder="Ссылка на готовый дизайн" label="Ссылка на готовый дизайн" />
                    <input type="file" id="ready_site_image" name="ready_site_image" accept="image/*"
                        @change="handleFileChange" class="form-input" />
                </div>
                <FormInput v-if="isSeo" type="number" v-model="form.seo_pages" required name="seo_pages"
                    placeholder="Кол-во страниц SEO" label="Кол-во страниц SEO" />
                <FormInput v-if="isRk" type="hidden" :value="form.rk_text" required name="rk_text" />

            </div>

            <div v-if="form.tax == 0" class="flex gap-3">
                <div v-if="visibleServices < 6" class="btn !w-fit" @click="addService">
                    Добавить услугу
                </div>
                <div v-if="visibleServices >= 2" class="btn !w-fit" @click="removeService">
                    Удалить услугу
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import FormInput from '../../../Components/FormInput.vue';

export default {
    components: {
        FormInput,
    },
    props: {
        form: {
            type: Object,
            required: true,
        },
        modelValue: {
            type: Boolean,
            required: true,
        },
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
    },

    data() {
        return {
            mainCats: this.cats.filter(cat => this.mainCatsIds.map(Number).includes(cat.id)),
            secondaryCats: this.cats.filter(cat => this.secondaryCatsIds.map(Number).includes(cat.id)).flatMap(cat => cat.services),
            visibleServices: 1,
            isRk: false,
            isSeo: false,
            isReady: false,
        };
    },

    watch: {
        form: {
            handler: 'checkValidFieldsFilled',
            deep: true,
        },
    },

    methods: {
        checkValidFieldsFilled() {
            let isValid = this.form.services.slice(0, this.visibleServices).every(service => {
                return service.service_id !== 0 && service.price > 0;
            });

            if (this.isSeo) {
                isValid = isValid && this.form.seo_pages != '' && this.form.seo_pages != 0 && this.form.seo_pages != null;
            }

            if (this.isReady) {
                isValid = isValid && this.form.ready_site_link != '' && this.form.ready_site_link != 0 && this.form.ready_site_link != null;
                isValid = isValid && this.form.ready_site_image != '' && this.form.ready_site_image != 0 && this.form.ready_site_image != null;
            }

            this.$emit('update:modelValue', isValid);
        },
        updateService(index, event) {
            const selectedOption = event.target.options[event.target.selectedIndex];

            this.form.services[index].price = selectedOption.getAttribute('data-price');
            this.form.services[index].duration = selectedOption.getAttribute('data-duration');
            this.form.services[index].isRk = selectedOption.getAttribute('data-isRk');
            this.form.services[index].isSeo = selectedOption.getAttribute('data-isSeo');
            this.form.services[index].isReady = selectedOption.getAttribute('data-isReady');

            this.updateAdditionalFields();
        },
        addService() {
            if (this.visibleServices < 6) {
                this.visibleServices += 1;
            }
        },
        removeService() {
            if (this.visibleServices > 1) {
                this.form.services[this.visibleServices - 1].service = 0
                this.form.services[this.visibleServices - 1].price = 0
                this.form.services[this.visibleServices - 1].duration = 0
                this.form.services[this.visibleServices - 1].isRk = false
                this.form.services[this.visibleServices - 1].isSeo = false
                this.form.services[this.visibleServices - 1].isReady = false
                this.visibleServices -= 1;
            }
            this.updateAdditionalFields();
        },
        updateAdditionalFields() {
            this.isRk = this.form.services.filter(el => el.isRk == 'true' || el.isRk == true).length != 0 ? true : false;
            this.isSeo = this.form.services.filter(el => el.isSeo == 'true' || el.isSeo == true).length != 0 ? true : false;
            this.isReady = this.form.services.filter(el => el.isReady == 'true' || el.isReady == true).length != 0 ? true : false;
        },
        handleFileChange(event) {
            const file = event.target.files[0]; 
            if (file) {
                this.form.ready_site_image = file;
            } else {
                this.form.ready_site_image = null;
            }
        }
    },
};
</script>