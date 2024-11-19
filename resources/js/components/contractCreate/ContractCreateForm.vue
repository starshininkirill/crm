<template>
   <form :action="action" method="POST">
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

      <vue-agent-info :old="old" />
      <vue-services-info :old="old" :mainCatsIds="mainCatsIds" :secondaryCatsIds="secondaryCatsIds" :cats="cats"
         @updateService="updateServicePrice" :servicePrices="servicePrices" />
      <vue-price-info :old="old" :servicePrices="servicePrices" v-model:amountPrice="amountPrice" v-model="sale" />

      <button type="submit" class="btn">Отправить</button>
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
   },
   components: {
      'vue-agent-info': AgentInfo,
      'vue-services-info': ServicesInfo,
      'vue-price-info': PriceInfo
   },
   data() {
      const old = this.rowOld ? JSON.parse(this.rowOld) : {};

      const oldServices = Array.isArray(old.service) ? old.service : [];
      const oldPrices = Array.isArray(old.service_price) ? old.service_price : [];
      const oldDuration = Array.isArray(old.service_duration) ? old.service_duration : [];

      const servicePrices = Array.from({ length: 6 }, (_, index) => ({
         service: oldServices[index] || 0,
         price: oldPrices[index] || 0,
         duration: oldDuration[index] || 0,
      }));      

      return {
         old,
         cats: JSON.parse(this.stringCats),
         mainCatsIds: JSON.parse(this.stringMainCats),
         secondaryCatsIds: JSON.parse(this.stringSecondaryCats),
         sale: parseInt(old.sale) || 0,
         amountPrice: 0,
         servicePrices,
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
      },
      updateServicePrice(index, price, duration) {
         this.servicePrices[index].price = price || 0;
         this.servicePrices[index].duration = duration || 0;
      }
   }
};
</script>