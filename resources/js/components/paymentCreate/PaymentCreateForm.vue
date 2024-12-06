<template>
    <div>
        <form :action="action" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="_token" :value="token">
            <div class="grid grid-cols-2 gap-4 max-w-xl mb-6">
                <vue-form-input required :value="old['leed']" type="number" name="leed" placeholder="Лид" label="Лид" />
                <vue-form-input required :value="old['number']" type="number" name="number" placeholder="Номер договора"
                    label="Номер договора" />
                <vue-form-input required :value="old['contact_fio']" type="text" name="contact_fio"
                    placeholder="ID Сделки" label="ID Сделки" />
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
                                <input required type="radio" value="0" v-model="clientType" name="client_type" /> Физическое лицо
                            </label>
                            <label class="cursor-pointer">
                                <input required type="radio" value="1" v-model="clientType" name="client_type" /> Юридическое
                                лицо
                            </label>
                        </div>
                    </div>

                    <!-- Выбор сценария оплаты -->
                    <div class="flex flex-col gap-2">
                        <div class="text-xl font-semibold">Сценарий оплаты</div>

                        <div class="flex w-fit gap-7">
                            <label class="cursor-pointer">
                                <input type="radio" value="0" v-model="PaymentDirection" name="payment_direction" />
                                Бюджет
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" value="1" v-model="PaymentDirection" name="payment_direction" />
                                Ведение
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" value="2" v-model="PaymentDirection" name="payment_direction" />
                                Допродажа
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" value="3" v-model="PaymentDirection" name="payment_direction" />
                                2-й платёж за настройку
                            </label>
                        </div>
                    </div>

                    <!-- Поля для физического лица -->
                    <fieldset v-show="clientType === '0'" :disabled="clientType != '0'" class="flex flex-col gap-2">
                        <div class="text-xl font-semibold">Данные для Физического лица</div>
                        <div class="grid grid-cols-2 gap-3">
                            <vue-form-input required v-model="fields.client_fio" type="number" name="client_fio"
                                placeholder="Общая сумма оплаты" label="Общая сумма оплаты" />
                            <vue-form-input required v-model="fields.amount_summ" type="text" name="amount_summ"
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
                            <label class="cursor-pointer">
                                <input checked type="radio" value="0" v-model="paymentType" name="payment_type" />
                                Без НДС (ИП1)
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" value="1" v-model="paymentType" name="payment_type" />
                                Без НДС (ИП2)
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" value="2" v-model="paymentType" name="payment_type" />
                                С НДС
                            </label>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <vue-form-input required v-model="fields.organization_short_name" type="number" name="organization_short_name"
                                placeholder="Краткое наименование организации"
                                label="Краткое наименование организации" />
                            <div></div>
                            <vue-form-input required v-model="fields.legal_address" type="text" name="legal_address"
                                placeholder="Юридический адрес" label="Юридический адрес" />
                            <vue-form-input required v-model="fields.inn" type="tel" name="inn"
                                placeholder="ИНН/КПП" label="ИНН/КПП" />
                        </div>

                        <div class="text-xl font-semibold">Данные заполнения счета и акта</div>
                        <div class="grid grid-cols-2 gap-3">
                            <vue-form-input required type="number" name="act_payment_summ" placeholder="Общая сумма оплаты"
                                v-model="fields.act_payment_summ" label="Общая сумма оплаты" />
                            <vue-form-input required type="text" name="act_payment_goal"
                                placeholder="Назначение платежа" v-model="fields.act_payment_goal"
                                label="Назначение платежа" />
                        </div>
                    </fieldset>

                <button class="btn" type="submit">
                    Создать платёж
                </button>
                </div>
            </div>
        </form>
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
    },
    data() {
        let old = this.rowOld ? JSON.parse(this.rowOld) : {};
        return {
            old,
            clientType: old.hasOwnProperty('client_type') ? old['client_type'] : '0',
            PaymentDirection: old.hasOwnProperty('payment_direction') ? old['payment_direction'] : '0',
            paymentType: old.hasOwnProperty('payment_type') ? old['payment_type'] : '0',

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
    }
};
</script>