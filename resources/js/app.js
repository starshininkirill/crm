import './bootstrap';
import '../css/app.css';


import { createApp } from 'vue/dist/vue.esm-bundler';
import CalendarMonth from './components/CalendarMonth.vue';

const app = createApp({
    components: {
        'vue-calendar-month': CalendarMonth,
    }
});

app.mount('#app');