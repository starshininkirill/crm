<template>
    <div>
        <form ref="form" :action="action" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="_token" :value="token">
            <div class="grid grid-cols-2 gap-4 max-w-xl mb-6">
                <vue-form-input required :value="old['leed']" type="number" name="leed" placeholder="Лид" label="Лид" />
                <vue-form-input required :value="old['number']" type="number" name="number" placeholder="Номер договора"
                    label="Номер договора" />
                <vue-form-input required :value="old['deal_id']" type="number" name="deal_id" placeholder="ID Сделки"
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
                                <input required type="radio" value="0" v-model="clientType" name="client_type" />
                                Физическое лицо
                            </label>
                            <label class="cursor-pointer">
                                <input required type="radio" value="1" v-model="clientType" name="client_type" />
                                Юридическое
                                лицо
                            </label>
                        </div>
                    </div>

                    <!-- Выбор сценария оплаты -->
                    <div class="flex flex-col gap-2">
                        <div class="text-xl font-semibold">Сценарий оплаты</div>

                        <div class="flex w-fit gap-7">
                            <label class="cursor-pointer">
                                <input type="checkbox" value="0" v-model="paymentDirection" name="payment_direction"
                                    :disabled="paymentDirection.indexOf('1') != -1 || paymentDirection.indexOf('2') != -1 || paymentDirection.indexOf('3') != -1" />
                                Бюджет
                            </label>
                            <label class="cursor-pointer">
                                <input type="checkbox" value="1" v-model="paymentDirection" name="payment_direction"
                                    :disabled="paymentDirection.indexOf('0') != -1" />
                                Ведение
                            </label>
                            <label class="cursor-pointer">
                                <input type="checkbox" value="2" v-model="paymentDirection" name="payment_direction"
                                    :disabled="paymentDirection.indexOf('0') != -1" />
                                Допродажа
                            </label>
                            <label class="cursor-pointer">
                                <input type="checkbox" value="3" v-model="paymentDirection" name="payment_direction"
                                    :disabled="paymentDirection.indexOf('0') != -1" />
                                2-й платёж за настройку
                            </label>
                        </div>
                    </div>

                    <!-- Поля для физического лица -->
                    <fieldset v-show="clientType === '0'" :disabled="clientType != '0'" class="flex flex-col gap-2">
                        <div class="text-xl font-semibold">Данные для Физического лица</div>
                        <div class="grid grid-cols-2 gap-3">
                            <vue-form-input required v-model="fields.amount_summ" type="number" name="amount_summ"
                                placeholder="Общая сумма оплаты" label="Общая сумма оплаты" />
                            <vue-form-input required v-model="fields.client_fio" type="text" name="client_fio"
                                placeholder="ФИО" label="ФИО" />
                            <vue-form-input required v-model="fields.phone" type="tel" name="phone"
                                placeholder="Телефон" label="Телефон" />
                        </div>
                    </fieldset>

                    <!-- Поля для юридического лица -->
                    <fieldset v-show="clientType === '1'" :disabled="clientType != '1'" class="flex flex-col gap-2">
                        <div class="text-xl font-semibold">Данные для Юридического лица</div>

                        <div class="text-xl font-semibold">Тип оплаты</div>
                        <div class="flex w-fit gap-7 mb-4">
                            <label v-for="organisation in organisations" class="cursor-pointer">
                                <input checked type="radio" :value="organisation.id" v-model="paymentType"
                                    name="payment_type" />
                                {{ organisation.name }} {{ organisation.nds == 0 ? '(Без НДС)' : '(С НДС)' }}
                            </label>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <vue-form-input required v-model="fields.organization_short_name" type="text"
                                name="organization_short_name" placeholder="Краткое наименование организации"
                                label="Краткое наименование организации" />
                            <div></div>
                            <vue-form-input required v-model="fields.legal_address" type="text" name="legal_address"
                                placeholder="Юридический адрес" label="Юридический адрес" />
                            <vue-form-input required v-model="fields.inn" type="number" name="inn" placeholder="ИНН/КПП"
                                label="ИНН/КПП" />
                        </div>

                        <div class="text-xl font-semibold">Данные заполнения счета и акта</div>
                        <div class="grid grid-cols-2 gap-3">
                            <vue-form-input required type="number" name="act_payment_summ"
                                placeholder="Общая сумма оплаты" v-model="fields.act_payment_summ"
                                label="Общая сумма оплаты" />
                            <vue-form-input required type="text" name="act_payment_goal"
                                placeholder="Назначение платежа" v-model="fields.act_payment_goal"
                                label="Назначение платежа" />
                        </div>
                    </fieldset>

                    <button @click="handleSubmit" :disabled="isSubmitting" class="btn">
                        {{ isSubmitting ? 'Генерация платежа...' : 'Создать платёж' }}
                    </button>
                </div>
            </div>
        </form>
        <div v-if="link != ''" class="px-5 py-3 border border-black rounded flex gap-4 items-center mt-6">
            <span>
                Ссылка для оплаты:
            </span>
            <input type="text" readonly :value="link" class="input max-w-xs w-full">
            <div @click="copyToClipboard" class=" w-14 h-14 bg-gray-800 rounded-md p-2 cursor-pointer">
                <svg class="w-full h-full" width="64" height="64" viewBox="0 0 64 64" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M40 40H47.4667C50.4536 40 51.9466 39.9999 53.0874 39.4186C54.091 38.9073 54.9079 38.0916 55.4193 37.0881C56.0006 35.9472 56.0006 34.4537 56.0006 31.4668V16.5334C56.0006 13.5465 56.0006 12.053 55.4193 10.9121C54.9079 9.90858 54.091 9.09262 53.0874 8.5813C51.9466 8 50.4541 8 47.4672 8H32.5339C29.5469 8 28.0523 8 26.9115 8.5813C25.9079 9.09262 25.0926 9.90858 24.5813 10.9121C24 12.053 24 13.5466 24 16.5335V24.0002M8 47.4669V32.5335C8 29.5466 8 28.053 8.5813 26.9121C9.09262 25.9086 9.90793 25.0926 10.9115 24.5813C12.0523 24 13.5469 24 16.5339 24H31.4672C34.4541 24 35.9466 24 37.0874 24.5813C38.091 25.0926 38.9079 25.9086 39.4193 26.9121C40.0006 28.053 40.0006 29.5464 40.0006 32.5334V47.4668C40.0006 50.4537 40.0006 51.9472 39.4193 53.0881C38.9079 54.0916 38.091 54.9073 37.0874 55.4186C35.9466 55.9999 34.4541 56 31.4672 56H16.5339C13.5469 56 12.0523 55.9999 10.9115 55.4186C9.90793 54.9073 9.09262 54.0916 8.5813 53.0881C8 51.9472 8 50.4538 8 47.4669Z"
                        stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </div>
        </div>
        <div class="flex items-center gap-3 mt-6">
            <a v-if="downloadLink != ''" target="_blank" :href="downloadLink" class="btn">
                Скачать документ
            </a>
            <a v-if="pdfDownloadLink != ''" target="_blank" :href="pdfDownloadLink" class="btn">
                Скачать PDF документ
            </a>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        token: {
            type: String,
            required: true
        },
        action: {
            type: String,
            required: true
        },
        rowOld: {
            type: String,
        },
        organisations: {
            required: true,
            type: Object,
        },
        link: {
            type: String,
        },
        downloadLink: {
            type: String,
        },
        pdfDownloadLink: {
            type: String,
        },
    },
    data() {
        let old = this.rowOld ? JSON.parse(this.rowOld) : {};
        return {
            old,
            clientType: old.hasOwnProperty('client_type') ? old['client_type'] : '0',
            paymentDirection: old.hasOwnProperty('payment_direction') ? old['payment_direction'] : [],
            paymentType: old.hasOwnProperty('payment_type') ? old['payment_type'] : '0',
            isSubmitting: false,

            fields: {
                amount_summ: old.hasOwnProperty('amount_summ') ? old['amount_summ'] : '',

                client_fio: old.hasOwnProperty('client_fio') ? old['client_fio'] : '',
                phone: old.hasOwnProperty('phone') ? old['phone'] : '',

                organization_short_name: old.hasOwnProperty('organization_short_name') ? old['organization_short_name'] : '',
                legal_address: old.hasOwnProperty('legal_address') ? old['legal_address'] : '',
                inn: old.hasOwnProperty('inn') ? old['inn'] : '',
                act_payment_summ: old.hasOwnProperty('act_payment_summ') ? old['act_payment_summ'] : '',
                act_payment_goal: old.hasOwnProperty('act_payment_goal') ? old['act_payment_goal'] : '',
            },
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
        handleSubmit() {
            this.isSubmitting = true;
            setTimeout(() => {
                this.$refs.form.submit();
            }, 0);
        },
    },
};
</script>