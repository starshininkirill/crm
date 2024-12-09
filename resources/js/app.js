import './bootstrap';
import '../css/app.css';

import './bootstrap';
import 'tinymce/tinymce';
import 'tinymce/skins/ui/oxide/skin.min.css';
import 'tinymce/icons/default/icons';
import 'tinymce/themes/silver/theme';
import 'tinymce/models/dom/model';
import 'tinymce/plugins/lists';


import { createApp } from 'vue/dist/vue.esm-bundler';
import CalendarMonth from './components/CalendarMonth.vue';
import UserSaleReportForm from './components/UserSaleReportForm.vue'
import ContractCreateForm from './components/contractCreate/ContractCreateForm.vue'
import AgentInfo from './components/contractCreate/AgentInfo.vue'
import components from './components/UI'
import PaymentCreateForm from './components/paymentCreate/PaymentCreateForm.vue';



const app = createApp({
    components: {
        'vue-calendar-month': CalendarMonth,
        'vue-user-sale-report-form': UserSaleReportForm,
        'vue-contract-create-form': ContractCreateForm,
        'vue-payment-create-form': PaymentCreateForm,
        'vue-contract-agent-info': AgentInfo,
    }
});

components.forEach(component => {
    app.component(component.name, component);
});

app.mount('#app');

tinymce.init({
    selector: '#tinyredactor',
    skin: false,
    content_css: false,
    plugins: ['lists'],
    license_key: 'gpl',
    toolbar: 'undo redo | blocks | ' +
        'bold italic backcolor | alignleft aligncenter ' +
        'alignright alignjustify | bullist numlist outdent indent | ' +
        'removeformat | help',
});

