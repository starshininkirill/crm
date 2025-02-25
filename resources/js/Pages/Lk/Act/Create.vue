<template>

    <Head title="Генератор Счёт/Акт" />

    <h1 class=" text-4xl font-bold mb-5">
        Создание Счёт/Акт
    </h1>

    <Error />

    <form enctype="multipart/form-data">
        <div class="grid grid-cols-2 gap-4 max-w-xl mb-6">
            <FormInput required v-model="form.leed" type="number" name="leed" placeholder="Лид" label="Лид" />
            <FormInput required v-model="form.number" type="number" name="number" placeholder="Номер договора"
                label="Номер договора" />
            <FormInput required v-model="form.deal_id" type="number" name="deal_id" placeholder="ID Сделки"
                label="ID Сделки" />
        </div>
        <div class="flex flex-col rounded-md border border-gray-400 shadow-xl">
            <div class="bg-gray-800 p-2 rounded-md text-white font-semibold text-xl">
                Контрагент
            </div>
            <div class="flex flex-col gap-4 p-2 mt-2">
                <div class="flex flex-col gap-2">
                    <div class="text-xl font-semibold">Контрагент</div>

                    <!-- Выбор физ/юр лица -->
                    <div class="grid grid-cols-2 w-fit gap-5">
                        <label class="cursor-pointer">
                            <input required type="radio" value="0" v-model="form.client_type" name="client_type" />
                            Физическое лицо
                        </label>
                        <label class="cursor-pointer">
                            <input required type="radio" value="1" v-model="form.client_type" name="client_type" />
                            Юридическое
                            лицо
                        </label>
                    </div>

                    <!-- Выбор сценария оплаты -->
                    <div class="flex flex-col gap-2">
                        <div class="text-xl font-semibold">Сценарий оплаты</div>
                        <div class="flex w-fit gap-7">
                            <label v-for="service in services" :key="service.id" class="cursor-pointer">
                                <input type="radio" :value="service.id" v-model="form.service_id" name="service_id" />
                                {{ service.name }}
                            </label>
                        </div>
                    </div>

                    <!-- Выбор организации -->
                    <div class="text-xl font-semibold">Организация</div>
                    <div class="flex w-fit gap-7 mb-4">
                        <label v-for="organisation in organisations" class="cursor-pointer">
                            <input type="radio" :value="organisation.id" v-model="form.organization_id"
                                name="organization_id" />
                            {{ organisation.short_name }} {{ organisation.nds == 0 ? '(Без НДС)' : '(С НДС)' }}
                        </label>
                    </div>

                    <!-- Поля для физического лица -->
                    <fieldset v-show="form.client_type == '0'" :disabled="form.client_type != '0'"
                        class="flex flex-col gap-2">
                        <div class="text-xl font-semibold">Данные для Физического лица</div>
                        <div class="grid grid-cols-2 gap-3">
                            <FormInput required v-model="form.amount_summ" type="number" name="amount_summ"
                                placeholder="Общая сумма оплаты" label="Общая сумма оплаты" />
                            <FormInput required v-model="form.client_fio" type="text" name="client_fio"
                                placeholder="ФИО" label="ФИО" />
                            <FormInput required v-model="form.phone" type="tel" name="phone" placeholder="Телефон"
                                label="Телефон" />
                        </div>
                    </fieldset>

                    <!-- Поля для юридического лица -->
                    <fieldset v-show="form.client_type == '1'" :disabled="form.client_type != '1'"
                        class="flex flex-col gap-2">
                        <div class="text-xl font-semibold">Данные для Юридического лица</div>

                        <div class="grid grid-cols-2 gap-3">
                            <FormInput required v-model="form.organization_short_name" type="text"
                                name="organization_short_name" placeholder="Краткое наименование организации"
                                label="Краткое наименование организации" />
                            <div></div>
                            <FormInput required v-model="form.legal_address" type="text" name="legal_address"
                                placeholder="Юридический адрес" label="Юридический адрес" />
                            <FormInput required v-model="form.inn" type="number" name="inn" placeholder="ИНН/КПП"
                                label="ИНН/КПП" />
                        </div>

                        <div class="text-xl font-semibold">Данные заполнения счета и акта</div>
                        <div class="grid grid-cols-2 gap-3">
                            <FormInput required type="number" name="act_payment_summ" placeholder="Общая сумма оплаты"
                                v-model="form.act_payment_summ" label="Общая сумма оплаты" />
                            <FormInput required type="text" name="act_payment_goal" placeholder="Назначение платежа"
                                v-model="form.act_payment_goal" label="Назначение платежа" />
                        </div>
                    </fieldset>

                    <button type="submit" @click="handleSubmit" :disabled="isSubmitting" class="btn !w-fit">
                        {{ isSubmitting ? 'Генерация документа...' : 'Отправить' }}
                    </button>

                </div>
            </div>
        </div>

    </form>


    <div class=" mt-5 ">
        <a v-if="$page.props.session.linkData && $page.props.session.linkData.type == 'document'"
            class="p-4 border-2 border-black rounded cursor-pointer text-xl font-semibold flex justify-center items-center"
            :href="$page.props.session.linkData.link">
            Скачать
        </a>
        <div v-if="$page.props.session.linkData && $page.props.session.linkData.type == 'sbp'"
            class="px-5 py-3 border border-black rounded flex gap-4 items-center ">
            <span>
                Ссылка для оплаты:
            </span>
            <input type="text" readonly ref="linkInput" :value="$page.props.session.linkData.link"
                class="input max-w-xs w-full">
            <div @click="copyToClipboard" class=" w-14 h-14 bg-gray-800 rounded-md p-2 cursor-pointer">
                <svg class="w-full h-full" width="64" height="64" viewBox="0 0 64 64" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M40 40H47.4667C50.4536 40 51.9466 39.9999 53.0874 39.4186C54.091 38.9073 54.9079 38.0916 55.4193 37.0881C56.0006 35.9472 56.0006 34.4537 56.0006 31.4668V16.5334C56.0006 13.5465 56.0006 12.053 55.4193 10.9121C54.9079 9.90858 54.091 9.09262 53.0874 8.5813C51.9466 8 50.4541 8 47.4672 8H32.5339C29.5469 8 28.0523 8 26.9115 8.5813C25.9079 9.09262 25.0926 9.90858 24.5813 10.9121C24 12.053 24 13.5466 24 16.5335V24.0002M8 47.4669V32.5335C8 29.5466 8 28.053 8.5813 26.9121C9.09262 25.9086 9.90793 25.0926 10.9115 24.5813C12.0523 24 13.5469 24 16.5339 24H31.4672C34.4541 24 35.9466 24 37.0874 24.5813C38.091 25.0926 38.9079 25.9086 39.4193 26.9121C40.0006 28.053 40.0006 29.5464 40.0006 32.5334V47.4668C40.0006 50.4537 40.0006 51.9472 39.4193 53.0881C38.9079 54.0916 38.091 54.9073 37.0874 55.4186C35.9466 55.9999 34.4541 56 31.4672 56H16.5339C13.5469 56 12.0523 55.9999 10.9115 55.4186C9.90793 54.9073 9.09262 54.0916 8.5813 53.0881C8 51.9472 8 50.4538 8 47.4669Z"
                        stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </div>
        </div>
    </div>


</template>
<script>
import { Head, useForm } from '@inertiajs/vue3';
import FormInput from '../../../Components/FormInput.vue';
import LkLayout from '../../../Layouts/LkLayout.vue';

export default {
    components: {
        Head,
        FormInput,

    },
    layout: LkLayout,

    props: {
        organisations: {
            required: true,
            type: Array,
        },
        services: {
            required: true,
            type: Array,
        },
    },
    data() {
        return {
            isSubmitting: false,
        };
    },
    setup() {
        const form = useForm({
            'leed': null,
            'number': null,
            'deal_id': null,
            'client_type': 0,
            'service_id': [],
            'amount_summ': null,
            'client_fio': '',
            'phone': '',
            'organization_id': null,
            'organization_short_name': null,
            'legal_address': null,
            'inn': null,
            'act_payment_summ': null,
            'act_payment_goal': null,
        });

        return {
            form,
        }
    },
    methods: {
        copyToClipboard() {
            const linkInput = this.$refs.linkInput;
            if (linkInput) {
                navigator.clipboard.writeText(linkInput.value)
                    .then(() => {
                        alert('Скопировано!');
                    })
                    .catch((err) => {
                        console.error("Ошибка при копировании текста: ", err);
                    });
            }
        },
        handleSubmit(event) {
            event.preventDefault();
            this.isSubmitting = true;
            let th = this;
            this.form.post(route('lk.act.store'), {
                onFinish() {
                    th.isSubmitting = false;
                },
                onSuccess() {
                    th.form.leed = null;
                    th.form.number = null;
                    th.form.deal_id = null;
                    th.form.client_type = 0;
                    th.form.service_id = [];
                    th.form.amount_summ = null;
                    th.form.client_fio = '';
                    th.form.phone = '';
                    th.form.organization_id = null;
                    th.form.organization_short_name = null;
                    th.form.legal_address = null;
                    th.form.inn = null;
                    th.form.act_payment_summ = null;
                    th.form.act_payment_goal = null;
                }
            });
        },
    },

}


</script>