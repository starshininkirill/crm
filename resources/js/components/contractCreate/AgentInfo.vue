<template>
    <div class="flex flex-col w-full mb-6">
        <div class="flex flex-col rounded-md border border-gray-400 shadow-xl">
            <div @click="toggleForm" class="bg-gray-800 p-2 rounded-md text-white font-semibold text-xl cursor-pointer">
                Контрагент
            </div>
            <div v-show="showForm" class="flex flex-col gap-4 p-2 mt-2">
                <div class="flex flex-col gap-2">
                    <div class="text-xl font-semibold">Контрагент</div>

                    <!-- Выбор физ/юр лица -->
                    <div class="grid grid-cols-2 w-fit gap-5">
                        <label class="cursor-pointer">
                            <input type="radio" value="0" v-model="clientType" name="client_type" /> Физическое лицо
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" value="1" v-model="clientType" name="client_type" /> Юридическое лицо
                        </label>
                    </div>
                </div>

                <!-- Выбор типа оплаты -->
                <div class="flex flex-col gap-2">
                    <div class="text-xl font-semibold">Тип оплаты</div>

                    <div class="grid grid-cols-2 w-fit gap-5">
                        <label class="cursor-pointer">
                            <input type="radio" value="0" v-model="taxType" name="tax" /> Без НДС
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" value="1" v-model="taxType" name="tax" /> С НДС
                        </label>
                    </div>
                </div>

                <!-- Поля для физического лица -->
                <fieldset v-show="clientType === '0'" :disabled="clientType !== '0'" class="flex flex-col gap-2">
                    <div class="text-xl font-semibold">Данные для Физического лица</div>
                    <div class="grid grid-cols-2 gap-3">
                        <vue-form-input required type="text" name="client_fio" placeholder="ФИО" label="ФИО" />
                        <vue-form-input required type="number" name="passport_series" placeholder="Серия паспорта"
                            label="Серия паспорта" />
                        <vue-form-input required type="number" name="passport_number" placeholder="Номер паспорта"
                            label="Номер паспорта" />
                        <vue-form-input required type="text" name="passport_issued" placeholder="Паспорт кем выдан"
                            label="Паспорт кем выдан" />
                        <vue-form-input required type="text" name="physical_address" placeholder="Адрес регистрации"
                            label="Адрес регистрации" />
                    </div>
                </fieldset>

                <!-- Поля для юридического лица -->
                <fieldset v-show="clientType === '1'" :disabled="clientType !== '1'" class="flex flex-col gap-2">
                    <div class="text-xl font-semibold">Данные для Юридического лица</div>
                    <div class="grid grid-cols-2 gap-3 mb-2">
                        <vue-form-input required type="text" name="full_corp_name" placeholder="Полное название организации"
                            label="Полное название организации" />
                        <vue-form-input required type="text" name="short_corp_name"
                            placeholder="Кратное наименование организации" label="Кратное наименование организации" />
                    </div>

                    <div class="text-xl font-semibold">ОГРН или ОГРНИП</div>
                    <div class="grid grid-cols-2 w-fit gap-5 mb-4">
                        <label class="cursor-pointer">
                            <input checked type="radio" value="0" v-model="ogrnType" name="ogrn" /> ОГРН
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" value="1" v-model="ogrnType" name="ogrn" /> ОГРНИП
                        </label>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-2">
                        <vue-form-input required type="text" name="ogrn" placeholder="Номер ОГРН/ОГРНИП"
                            label="Номер ОГРН/ОГРНИП" />
                        <vue-form-input required type="text" v-if="ogrnType === '0'" name="director_name" placeholder="(Иванова Ивана Ивановича)"
                            label="ФИО Ген.дира в РОД ПАДЕЖЕ" />
                        <div v-if="ogrnType === '1'">

                        </div>
                        <vue-form-input required type="text" name="ur_address" placeholder="Юридический адрес"
                            label="Юридический адрес" />
                        <vue-form-input required type="number" name="inn" placeholder="ИНН/КПП" label="ИНН/КПП" />
                        <vue-form-input required type="number" name="payment_account" placeholder="Расчётный счёт"
                            label="Расчётный счёт" />
                        <vue-form-input required type="number" name="сorrespondent_account" placeholder="Корреспондентский счёт"
                            label="Корреспондентский счёт" />
                        <vue-form-input required type="text" name="bank_name" placeholder="Наименование банка"
                            label="Наименование банка" />
                        <vue-form-input required type="number" name="bank_bik" placeholder="БИК Банка" label="БИК Банка" />
                    </div>

                    <div class="text-xl font-semibold">Данные заполнения счета и акта</div>
                    <div class="grid grid-cols-2 gap-3">
                        <vue-form-input required type="number" name="act_payment_summ" placeholder="Сумма"
                            label="Сумма" />
                        <vue-form-input required type="text" name="act_payment_goal" placeholder="Назначение платежа"
                            label="Назначение платежа" />
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            showForm: true,
            clientType: '0',
            taxType: '0',
            ogrnType: '0',
        };
    },
    methods: {
        toggleForm() {
            this.showForm = !this.showForm;
        }
    }
};
</script>