<template>
   <form :action="action" method="POST">
      <input type="hidden" name="_token" :value="token">
      <div class="grid grid-cols-2 gap-4 max-w-xl mb-6">
         <vue-form-input required type="number" name="leed" placeholder="Лид" label="Лид" />
         <vue-form-input required type="number" name="number" placeholder="Номер договора" label="Номер договора" />
         <vue-form-input required type="text" name="contact_fio" placeholder="ФИО представителя" label="ФИО представителя" />
         <vue-form-input required type="tel" name="contact_phone" placeholder="Телефон" label="Телефон" />
      </div>

      <vue-agent-info />
      <vue-services-info :mainCatsIds="mainCatsIds" :secondaryCatsIds="secondaryCatsIds" :cats="cats"
         @updateService="updateServicePrice" :servicePrices="servicePrices" />
      <vue-price-info :servicePrices="servicePrices" v-model:amountPrice="amountPrice" v-model="sale"  />

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
      }
   },
   components: {
      'vue-agent-info': AgentInfo,
      'vue-services-info': ServicesInfo,
      'vue-price-info': PriceInfo
   },
   data() {
      return {
         cats: JSON.parse(this.stringCats),
         mainCatsIds: JSON.parse(this.stringMainCats),
         secondaryCatsIds: JSON.parse(this.stringSecondaryCats),
         sale: 0,
         amountPrice: 0,
            servicePrices: [
               {
                  'price': 0,
                  'duration': 0
               }, {
                  'price': 0,
                  'duration': 0
               }, {
                  'price': 0,
                  'duration': 0
               }, {
                  'price': 0,
                  'duration': 0
               }, {
                  'price': 0,
                  'duration': 0
               }, {
                  'price': 0,
                  'duration': 0
               },
         ],
      };
   },
   watch: {
      sale: 'recalculateAmountPrice',
      servicePrices: {
         handler: 'recalculateAmountPrice',
         deep: true,
      }
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