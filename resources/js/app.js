import './bootstrap';
import '../css/app.css';


import { createApp } from 'vue/dist/vue.esm-bundler';
import CalendarMonth from './components/CalendarMonth.vue';
import UserSaleReportForm from './components/UserSaleReportForm.vue'

const app = createApp({
    components: {
        'vue-calendar-month': CalendarMonth,
        'vue-user-sale-report-form': UserSaleReportForm
    }
});

app.mount('#app');