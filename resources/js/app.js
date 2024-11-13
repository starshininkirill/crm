import './bootstrap';
import '../css/app.css';


import { createApp } from 'vue/dist/vue.esm-bundler';
import CalendarMonth from './components/CalendarMonth.vue';
import UserSaleReportForm from './components/UserSaleReportForm.vue'
import ContractCreateForm from './components/contractCreate/ContractCreateForm.vue'
import AgentInfo from './components/contractCreate/AgentInfo.vue'
import components from './components/UI'



const app = createApp({
    components: {
        'vue-calendar-month': CalendarMonth,
        'vue-user-sale-report-form': UserSaleReportForm,
        'vue-contract-create-form': ContractCreateForm,
        'vue-contract-agent-info': AgentInfo,
    }
});

components.forEach(component => {
    app.component(component.name, component);
});

app.mount('#app');