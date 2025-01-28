<template>

    <Head title="Генератор Платежей" />

    <h1 class=" text-4xl font-bold mb-5">
        Создание Платежа
    </h1>

    <ul v-if="form.errors" class="flex flex-col gap-1 mb-4">
        <li v-for="(error, index) in form.errors" :key="index" class="text-red-400">{{ error }}</li>
    </ul>

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
                            <label class="cursor-pointer">
                                <input type="radio" value="0" v-model="form.payment_direction" name="payment_direction"
                                    :disabled="form.payment_direction.indexOf('1') != -1 || form.payment_direction.indexOf('2') != -1 || form.payment_direction.indexOf('3') != -1" />
                                Бюджет
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" value="1" v-model="form.payment_direction" name="payment_direction"
                                    :disabled="form.payment_direction.indexOf('0') != -1" />
                                Ведение
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" value="2" v-model="form.payment_direction" name="payment_direction"
                                    :disabled="form.payment_direction.indexOf('0') != -1" />
                                Допродажа
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" value="3" v-model="form.payment_direction" name="payment_direction"
                                    :disabled="form.payment_direction.indexOf('0') != -1" />
                                2-й платёж за настройку
                            </label>
                        </div>
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

                        <div class="text-xl font-semibold">Тип оплаты</div>
                        <div class="flex w-fit gap-7 mb-4">
                            <label v-for="organisation in organisations" class="cursor-pointer">
                                <input checked type="radio" :value="organisation.id" v-model="form.organization_id"
                                    name="organization_id" />
                                {{ organisation.short_name }} {{ organisation.nds == 0 ? '(Без НДС)' : '(С НДС)' }}
                            </label>
                        </div>

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
            'payment_direction': [],
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
            const text = this.link;
            navigator.clipboard.writeText(text)
                .then(() => {
                    alert('Скопировано!')
                })
                .catch((err) => {
                    console.error("Ошибка при копировании текста: ", err);
                });
        },
        handleSubmit(event) {
            event.preventDefault();
            this.isSubmitting = true;
            let th = this;
            this.form.post(route('lk.payment.store'), {
                onFinish() {
                    th.isSubmitting = false;
                },
                onSuccess() {
                    th.form.leed = null;
                    th.form.number = null;
                    th.form.deal_id = null;
                    th.form.client_type = 0;
                    th.form.payment_direction = [];
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