<template>
    <div class="flex flex-col w-full mb-6">
        <div class="flex flex-col rounded-md border border-gray-400 shadow-xl">
            <div class="bg-gray-800 p-2 rounded-md text-white font-semibform text-xl">
                Контрагент
            </div>
            <div class="flex flex-col gap-4 p-2 mt-2">
                <div class="flex flex-col gap-2">
                    <div class="text-xl font-semibform">Контрагент</div>

                    <!-- Выбор физ/юр лица -->
                    <div class="grid grid-cols-2 w-fit gap-5">
                        <label class="cursor-pointer">
                            <input type="radio" value="0" v-model="form.client_type" name="client_type" /> Физическое
                            лицо
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" value="1" v-model="form.client_type" name="client_type" /> Юридическое
                            лицо
                        </label>
                    </div>

                    <!-- Выбор типа оплаты -->
                    <div class="flex flex-col gap-2">
                        <div class="text-xl font-semibold">Тип оплаты</div>

                        <div class="grid grid-cols-2 w-fit gap-5">
                            <label class="cursor-pointer">
                                <input type="radio" value="0" v-model="form.tax" name="tax" /> Без НДС
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" value="1" v-model="form.tax" name="tax" /> С НДС
                            </label>
                        </div>
                    </div>

                    <!-- Поля для физического лица -->
                    <fieldset v-show="form.client_type == '0'" :disabled="form.client_type != '0'"
                        class="flex flex-col gap-2">
                        <div class="text-xl font-semibold">Данные для Физического лица</div>
                        <div class="grid grid-cols-2 gap-3">
                            <FormInput required v-model="form.client_fio" type="text" name="client_fio"
                                placeholder="ФИО" label="ФИО" />
                            <FormInput required v-model="form.passport_series" type="number" name="passport_series"
                                placeholder="Серия паспорта" label="Серия паспорта" />
                            <FormInput required v-model="form.passport_number" type="number" name="passport_number"
                                placeholder="Номер паспорта" label="Номер паспорта" />
                            <FormInput required v-model="form.passport_issued" type="text" name="passport_issued"
                                placeholder="Паспорт кем выдан" label="Паспорт кем выдан" />
                            <FormInput required v-model="form.physical_address" type="text" name="physical_address"
                                placeholder="Адрес регистрации" label="Адрес регистрации" />
                        </div>
                    </fieldset>


                    <!-- Поля для юридического лица -->
                    <fieldset v-show="form.client_type == '1'" :disabled="form.client_type != '1'" class="flex flex-col gap-2">
                        <div class="text-xl font-semibold">Данные для Юридического лица</div>
                        <div class="grid grid-cols-2 gap-3 mb-2">
                            <FormInput required type="text" name="organization_name"
                                v-model="form.organization_name" placeholder="Полное название организации"
                                label="Полное название организации" />
                            <FormInput required type="text" name="organization_short_name"
                                v-model="form.organization_short_name" placeholder="Кратное наименование организации"
                                label="Кратное наименование организации" />
                        </div>

                        <div class="text-xl font-semibold">ОГРН или ОГРНИП</div>
                        <div class="grid grid-cols-2 w-fit gap-5 mb-4">
                            <label class="cursor-pointer">
                                <input checked type="radio" value="0" v-model="form.register_number_type" name="register_number_type" />
                                ОГРН
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" value="1" v-model="form.register_number_type" name="register_number_type" /> ОГРНИП
                            </label>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-2">
                            <FormInput required type="text" name="register_number" placeholder="Номер ОГРН/ОГРНИП"
                                v-model="form.register_number" label="Номер ОГРН/ОГРНИП" />
                            <FormInput required type="text" v-show="form.register_number_type == '0'" name="director_name"
                                v-model="form.director_name" placeholder="(Иванова Ивана Ивановича)"
                                label="ФИО Ген.дира в РОД ПАДЕЖЕ" />
                            <div v-if="form.register_number_type == '1'">
                            </div>
                            <FormInput required type="text" name="legal_address" placeholder="Юридический адрес"
                                v-model="form.legal_address" label="Юридический адрес" />
                            <FormInput required type="number" name="inn" placeholder="ИНН/КПП" label="ИНН/КПП"
                                v-model="form.inn" />
                            <FormInput required type="number" name="current_account" placeholder="Расчётный счёт"
                                v-model="form.current_account" label="Расчётный счёт" />
                            <FormInput required type="number" name="correspondent_account"
                                v-model="form.correspondent_account" placeholder="Корреспондентский счёт"
                                label="Корреспондентский счёт" />
                            <FormInput required type="text" name="bank_name" placeholder="Наименование банка"
                                v-model="form.bank_name" label="Наименование банка" />
                            <FormInput required type="number" name="bank_bik" placeholder="БИК Банка"
                                v-model="form.bank_bik" label="БИК Банка" />
                        </div>

                        <div class="text-xl font-semibold">Данные заполнения счета и акта</div>
                        <div class="grid grid-cols-2 gap-3">
                            <FormInput required type="number" name="act_payment_summ" placeholder="Сумма"
                                v-model="form.act_payment_summ" label="Сумма" />
                            <FormInput required type="text" name="act_payment_goal"
                                placeholder="Назначение платежа" v-model="form.act_payment_goal"
                                label="Назначение платежа" />
                        </div>
                    </fieldset>

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
    },
    watch: {
        form: {
            handler: 'validate',
            deep: true,
        },
    },
    methods: {
        validate() {            
            let isValid = true;
            if (this.form.client_type == '0') {
                isValid = !!(
                    this.form.client_fio &&
                    this.form.passport_series &&
                    this.form.passport_number &&
                    this.form.passport_issued &&
                    this.form.physical_address
                );
            } else if (this.form.client_type == '1') {
                isValid = !!(
                    this.form.organization_name &&
                    this.form.organization_short_name &&
                    this.form.register_number &&
                    (this.form.register_number_type == '1' || this.form.director_name) &&
                    this.form.legal_address &&
                    this.form.inn &&
                    this.form.current_account &&
                    this.form.correspondent_account &&
                    this.form.bank_name &&
                    this.form.bank_bik &&
                    this.form.act_payment_summ &&
                    this.form.act_payment_goal 
                );
            }                        
            this.$emit('update:modelValue', isValid);
        },
    },
    mounted() {
        this.validate();
    },
};
</script>