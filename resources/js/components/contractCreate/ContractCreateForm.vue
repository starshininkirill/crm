<template>
   <form :action="action" method="POST">
      <input type="hidden" name="_token" :value="token">
      <div class="grid grid-cols-2 gap-4 max-w-xl mb-6">
         <vue-form-input type="number" name="leed" placeholder="Лид" label="Лид" />
         <vue-form-input type="number" name="contract_number" placeholder="Номер договора" label="Номер договора" />
         <vue-form-input type="text" name="contact_fio" placeholder="ФИО" label="ФИО" />
         <vue-form-input type="tel" name="phone" placeholder="Телефон" label="Телефон" />
      </div>

      <div class="flex flex-col w-full mb-6">
         <div class="flex flex-col rounded-md border border-gray-400 shadow-xl">
            <div class="bg-gray-800 p-2 rounded-md text-white font-semibold text-xl cursor-pointer">
               Контрагент
            </div>
            <div class="flex flex-col gap-4 p-2 mt-2">
               <div class="flex flex-col gap-2">
                  <div class="text-xl font-semibold">Контрагент</div>

                  <!-- Выбор физ/юр лица -->
                  <div class="grid grid-cols-2 w-fit gap-5">
                     <label class="cursor-pointer">
                        <input type="radio" value="fizic" v-model="clientType" name="client_type" /> Физическое лицо
                     </label>
                     <label class="cursor-pointer">
                        <input type="radio" value="uric" v-model="clientType" name="client_type" /> Юридическое лицо
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
               <fieldset v-if="clientType === 'fizic'" :disabled="clientType !== 'fizic'" class="flex flex-col gap-2">
                  <div class="text-xl font-semibold">Данные для Физического лица</div>
                  <div class="grid grid-cols-2 gap-3">
                     <vue-form-input type="text" name="fio" placeholder="ФИО" label="ФИО" />
                     <vue-form-input type="number" name="pasport_seria" placeholder="Серия паспорта"
                        label="Серия паспорта" />
                     <vue-form-input type="number" name="pasport_number" placeholder="Номер паспорта"
                        label="Номер паспорта" />
                     <vue-form-input type="text" name="pasport_who" placeholder="Паспорт кем выдан"
                        label="Паспорт кем выдан" />
                     <vue-form-input type="text" name="address" placeholder="Адрес регистрации"
                        label="Адрес регистрации" />
                  </div>
               </fieldset>

               <!-- Поля для юридического лица -->
               <fieldset v-if="clientType === 'uric'" :disabled="clientType !== 'uric'" class="flex flex-col gap-2">
                  <div class="text-xl font-semibold">Данные для Юридического лица</div>
                  <div class="grid grid-cols-2 gap-3 mb-2">
                     <vue-form-input type="text" name="full_corp_name" placeholder="Полное название организации"
                        label="Полное название организации" />
                     <vue-form-input type="text" name="short_corp_name" placeholder="Кратное наименование организации"
                        label="Кратное наименование организации" />
                  </div>

                  <div class="text-xl font-semibold">ОГРН или ОГРНИП</div>
                  <div class="grid grid-cols-2 gap-3 mb-2">
                     <vue-form-input type="text" name="ogrn" placeholder="Номер ОГРН/ОГРНИП"
                        label="Номер ОГРН/ОГРНИП" />
                     <vue-form-input type="text" name="director_name" placeholder="(Иванова Ивана Ивановича)"
                        label="ФИО Ген.дира в РОД ПАДЕЖЕ" />
                     <vue-form-input type="text" name="ur_address" placeholder="Юридический адрес"
                        label="Юридический адрес" />
                     <vue-form-input type="number" name="inn" placeholder="ИНН/КПП" label="ИНН/КПП" />
                     <vue-form-input type="number" name="payment_account" placeholder="Расчётный счёт"
                        label="Расчётный счёт" />
                     <vue-form-input type="number" name="сorrespondent_account" placeholder="Корреспондентский счёт"
                        label="Корреспондентский счёт" />
                     <vue-form-input type="text" name="bank_name" placeholder="Наименование банка"
                        label="Наименование банка" />
                     <vue-form-input type="number" name="bank_bik" placeholder="БИК Банка" label="БИК Банка" />
                  </div>
               </fieldset>
            </div>
         </div>
      </div>

      <div class="flex flex-col w-full mb-6">
         <div class="flex flex-col rounded-md border border-gray-400 shadow-xl">
            <div class="bg-gray-800 p-2 rounded-md text-white font-semibold text-xl cursor-pointer">
               Услуги
            </div>
         </div>
      </div>

      <button type="submit" class="btn">Отправить</button>
   </form>
</template>

<script>
import FormInput from '../UI/FormInput.vue';

export default {
   props: {
      token: String,
      action: String
   },
   components: {
      'vue-form-input': FormInput
   },
   data() {
      return {
         clientType: 'fizic',
         taxType: '0',
      };
   },
   methods: {

   }
};
</script>