import './bootstrap';
import '../css/app.css';


import { createApp } from 'vue/dist/vue.esm-bundler';
import CalendarDay from './components/CalendarDay.vue';

const app = createApp({
    components: {
        'calendar-day': CalendarDay,
    }
});

app.mount('#app');