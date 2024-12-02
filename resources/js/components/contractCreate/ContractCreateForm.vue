<template>
   <form :action="action" method="POST" enctype="multipart/form-data">
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


      <div class="navigation-buttons flex gap-3 mb-4">
         <div class="btn" :class="{ 'cursor-not-allowed opacity-50': currentStep === 1 }" @click="goBack">
            Назад
         </div>

         <div class="btn" :class="{ 'cursor-not-allowed opacity-50': !canGoNext() }" @click="goNext">
            Вперёд
         </div>
      </div>
      <div>
         <div v-show="currentStep === 1">
            <vue-agent-info :old="old" v-model:valid="stepsValid[0]" />
         </div>

         <div v-show="currentStep === 2">
            <vue-services-info v-model:valid="stepsValid[1]" :old="old" :mainCatsIds="mainCatsIds"
               :secondaryCatsIds="secondaryCatsIds" :cats="cats" @updateService="updateServicePrice"
               :servicePrices="servicePrices" v-model:isRk="isRk" v-model:isSeo="isSeo" v-model:isReady="isReady" 
               :rkText="rkText"/>
         </div>

         <div v-show="currentStep === 3">
            <vue-price-info :old="old" :servicePrices="servicePrices" v-model:amountPrice="amountPrice"
               v-model="sale" />
         </div>
      </div>

   </form>
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
      }
   },
   components: {
      'vue-agent-info': AgentInfo,
      'vue-services-info': ServicesInfo,
      'vue-price-info': PriceInfo
   },
   data() {      
      let cats = JSON.parse(this.stringCats)
      let allServices = cats.flatMap(cat => cat.services)      

      const old = this.rowOld ? JSON.parse(this.rowOld) : {};

      console.log(old);

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
         isRk: false,
         isSeo: false,
         isReady: false,
      };
   },
   watch: {
      sale: 'recalculateAmountPrice',
      servicePrices: {
         handler: 'recalculateAmountPrice',
         deep: true,
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
      }
   }
};
</script>