import './bootstrap';
import '../css/app.css';


import { createApp } from 'vue/dist/vue.esm-bundler';
import CalendarMonth from './components/CalendarMonth.vue';
import UserSaleReportForm from './components/UserSaleReportForm.vue'
import ContractCreateForm from './components/contractCreate/ContractCreateForm.vue'
import FormInput from './components/UI/FormInput.vue'

const app = createApp({
    components: {
        'vue-calendar-month': CalendarMonth,
        'vue-user-sale-report-form': UserSaleReportForm,
        'vue-contract-create-form': ContractCreateForm,
        'vue-form-input': FormInput
    }
});

app.mount('#app');