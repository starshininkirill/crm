<template>
   <div>
      <a v-if="file != ''" :href="file" download target="_blank" class="btn my-6 block">
         Скачать документ
      </a>
      <div v-if="link != ''" class="px-5 py-3 border border-black rounded flex gap-4 items-center my-6">
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
      <form ref="form" :action="action" method="POST" enctype="multipart/form-data">
         <input type="hidden" name="_token" :value="token">
         <div class="grid grid-cols-2 gap-4 max-w-xl mb-6">
            <vue-form-input required :value="old['leed']" type="number" name="leed" placeholder="Лид" label="Лид" />
            <vue-form-input required :value="old['number']" type="number" name="number" placeholder="Номер договора"
               label="Номер договора" />
            <vue-form-input required :value="old['contact_fio']" type="text" name="contact_fio"
               placeholder="ФИО представителя" label="ФИО представителя" />
            <vue-form-input required :value="old['contact_phone']" type="tel" name="contact_phone" placeholder="Телефон"
               label="Телефон" />
         </div>

         <div>
            <div v-show="currentStep === 1">
               <vue-agent-info :old="old" v-model:nds="nds" v-model:valid="stepsValid[0]" />
            </div>

            <div v-show="currentStep === 2">
               <vue-services-info v-model:valid="stepsValid[1]" v-model:nds="nds" :old="old"
                  v-model:mainCatsIds="mainCatsIds" :secondaryCatsIds="secondaryCatsIds" :cats="cats"
                  @updateService="updateServicePrice" :servicePrices="servicePrices" v-model:isRk="isRk"
                  v-model:isSeo="isSeo" v-model:isReady="isReady" :rkText="rkText" />
            </div>

            <div v-if="form" v-show="currentStep === 3">
               <vue-price-info
                  :form="form"
               :old="old" :servicePrices="servicePrices" v-model:amountPrice="amountPrice"
                  v-model="sale" />
            </div>
         </div>

         <div class="navigation-buttons flex gap-3 mb-4">
            <div class="btn" :class="{ 'cursor-not-allowed opacity-50': currentStep === 1 }" @click="goBack">
               Назад
            </div>

            <div class="btn" :class="{ 'cursor-not-allowed opacity-50': !canGoNext() }" @click="goNext">
               Вперёд
            </div>
         </div>

      </form>

   </div>

</template>

<script>
import AgentInfo from './AgentInfo.vue';
import ServicesInfo from './ServicesInfo.vue';
import PriceInfo from './PriceInfo.vue';

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
      stringCats: {
         type: String,
         required: true
      },
      stringMainCats: {
         type: String,
      },
      stringSecondaryCats: {
         type: String,
      },
      rowOld: {
         type: String,
      },
      rkText: {
         type: String,
      },
      link: {
         type: String,
      },
      file: {
         type: String,
      }
   },
   components: {
      'vue-agent-info': AgentInfo,
      'vue-services-info': ServicesInfo,
      'vue-price-info': PriceInfo
   },
   data() {;
      
      let cats = JSON.parse(this.stringCats)
      let allServices = cats.flatMap(cat => cat.services)
     

      const old = this.rowOld ? JSON.parse(this.rowOld) : {};

      const oldServices = Array.isArray(old.service) ? old.service : [];
      const oldPrices = Array.isArray(old.service_price) ? old.service_price : [];
      const oldDuration = Array.isArray(old.service_duration) ? old.service_duration : [];

      const servicePrices = Array.from({ length: 6 }, (_, index) => {
         const foundService = allServices.find(el => el.id == oldServices[index]);

         return {
            service: oldServices[index] || 0,
            price: oldPrices[index] || 0,
            duration: oldDuration[index] || 0,
            isRk: foundService ? foundService.isRk : false,
            isReady: foundService ? foundService.isReady : false,
            isSeo: foundService ? foundService.isSeo : false,
         };
      });

      return {
         old,
         cats,
         mainCatsIds: JSON.parse(this.stringMainCats),
         secondaryCatsIds: JSON.parse(this.stringSecondaryCats),
         sale: parseInt(old.sale) || 0,
         amountPrice: 0,
         servicePrices,
         currentStep: 1,
         stepsValid: [false, false],
         nds: old['tax'] || '0',
         form: null,

         isRk: servicePrices.filter(el => el.isRk == 'true' || el.isRk == true).length != 0 ? true : false,
         isSeo: servicePrices.filter(el => el.isSeo == 'true' || el.isSeo == true).length != 0 ? true : false,
         isReady: servicePrices.filter(el => el.isReady == 'true' || el.isReady == true).length != 0 ? true : false,
      };
   },
   watch: {
      sale: 'recalculateAmountPrice',
      servicePrices: {
         handler: 'recalculateAmountPrice',
         deep: true,
      },
      nds(newValue) {
         if (newValue == true) {
            this.mainCatsIds = this.cats.filter((cat) => cat.isRk == true).map((el) => el.id);
         } else {
            this.mainCatsIds = JSON.parse(this.stringMainCats);
         }

      }
   },
   mounted() {
      this.recalculateAmountPrice();
   },
   methods: {
      recalculateAmountPrice() {
         const total = this.servicePrices.reduce((acc, item) => acc + parseInt(item.price), 0);
         this.amountPrice = total - this.sale;

         this.isRk = this.servicePrices.filter(el => el.isRk == 'true' || el.isRk == true).length != 0 ? true : false;
         this.isSeo = this.servicePrices.filter(el => el.isSeo == 'true' || el.isSeo == true).length != 0 ? true : false;
         this.isReady = this.servicePrices.filter(el => el.isReady == 'true' || el.isReady == true).length != 0 ? true : false;

      },
      updateServicePrice(index, price, duration, isRk, isSeo, isReady) {
         this.servicePrices[index].price = price || 0;
         this.servicePrices[index].duration = duration || 0;
         this.servicePrices[index].isRk = isRk || false;
         this.servicePrices[index].isSeo = isSeo || false;
         this.servicePrices[index].isReady = isReady || false;
      },
      canGoNext() {
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
   },
   mounted() {
        this.form = this.$refs.form;
    },
};
</script>