<template>

    <Head title="Генератор документов" />

    <h1 class=" text-4xl font-bold mb-5">
        Создание Договора
    </h1>

    <Error />

    <form method="POST" enctype="multipart/form-data">
        <div class="grid grid-cols-2 gap-4 max-w-xl mb-6">
            <FormInput v-model="form.leed" type="number" name="leed" placeholder="Лид" label="Лид" required />
            <FormInput v-model="form.number" type="number" name="number" placeholder="Номер договора"
                label="Номер договора" required />
            <FormInput v-model="form.contact_fio" type="text" name="contact_fio" placeholder="ФИО представителя"
                label="ФИО представителя" required />
            <FormInput v-model="form.contact_phone" type="tel" name="contact_phone" placeholder="Телефон"
                label="Телефон" required />
        </div>
        <div>
            <div v-show="currentStep == 1">
                <AgentInfo :form="form" v-model="stepsValid[0]" />
            </div>
            <div v-show="currentStep == 2">
                <ServicesInfo :form="form" :services="form.services" :cats="cats" v-model="stepsValid[1]"
                    :mainCatsIds="mainCatsIds" :secondaryCatsIds="secondaryCatsIds" />
            </div>
            <div v-show="currentStep == 3">
                <PriceInfo :form="form" />
            </div>
        </div>

        <div class="navigation-buttons flex gap-3 mb-4">
            <div class="btn !w-fit" :class="{ 'cursor-not-allowed opacity-50': currentStep == 1 }"
                :disabled="currentStep == 1" @click="goBack">
                Назад
            </div>

            <div class="btn !w-fit" :class="{ 'cursor-not-allowed opacity-50': !canGoNext() }" :disabled="!canGoNext()"
                @click="goNext">
                Вперёд
            </div>
        </div>

    </form>


</template>
<script>
import { Head, useForm } from '@inertiajs/vue3';
import LkLayout from '../../../Layouts/LkLayout.vue';
import FormInput from '../../../Components/FormInput.vue';
import AgentInfo from './AgentInfo.vue';
import ServicesInfo from './ServicesInfo.vue';
import PriceInfo from './PriceInfo.vue';

export default {
    components: {
        Head,
        FormInput,
        AgentInfo,
        ServicesInfo,
        PriceInfo
    },

    props: {
        cats: {
            type: Array,
        },
        mainCatsIds: {
            type: Array,
        },
        secondaryCatsIds: {
            type: Array,
        },
        rkText: {
            type: String,
        },
    },

    layout: LkLayout,

    data() {
        return {
            stepsValid: [false, false],
            currentStep: 1,
        };
    },

    setup() {
        const form = useForm({
            'leed': '',
            'number': '',
            'contact_fio': '',
            'contact_phone': '',
            'client_type': 0,
            'tax': 0,
            // Поля для физ лица
            'client_fio': '',
            'passport_series': '',
            'passport_number': '',
            'passport_issued': '',
            'physical_address': '',
            // Поля для Юр. лица
            'organization_name': '',
            'organization_short_name': '',
            'register_number_type': 0,
            'register_number': '',
            'director_name': '',
            'legal_address': '',
            'inn': '',
            'current_account': '',
            'correspondent_account': '',
            'bank_name': '',
            'bank_bik': '',
            'act_payment_summ': '',
            'act_payment_goal': '',
            'services': Array.from({ length: 6 }, (_, index) => {
                return {
                    service_id: null,
                    price: 0,
                    duration: 0,
                    isRk: false,
                    isReady: false,
                    isSeo: false,
                };
            }),

            // Услуги
            'ready_site_link': '',
            'ready_site_image': '',
            'seo_pages': '',
            'rk_text': '',

            // Цены
            'amount_price': 0,
            'sale': 0,
            'development_time': '',
            'payments': [],
        })

        return {
            form
        }
    },
    watch: {
        'form.services': {
            handler: 'recalculateAmountData',
            deep: true,
        },
        'form.sale': {
            handler: 'recalculateAmountData',
            deep: true,
        },
    },
    methods: {
        recalculateAmountData() {
            const amount_price = this.form.services.reduce((acc, item) => acc + parseInt(item.price || 0), 0);
            this.form.amount_price = amount_price - this.form.sale;
            
            this.form.development_time = this.form.services.reduce((accumulator, currentValue) => accumulator + parseInt(currentValue.duration), 0);
        },
        canGoNext() {
            // return true;

            return this.stepsValid[this.currentStep - 1];
        },
        goNext() {
            if (this.canGoNext() && this.currentStep < 3) {
                this.currentStep++;
            }
        },
        goBack() {
            if (this.currentStep > 1) {
                this.currentStep--;
            }
        },
    },
}


</script>