import './bootstrap';
import '../css/app.css';
import { createApp, h } from 'vue'
import { createInertiaApp, Link } from '@inertiajs/vue3'
import { ZiggyVue } from 'ziggy-js';
import Layout from './Layouts/Layout.vue';
import AdminLayout from './Layouts/AdminLayout.vue';
import { initTinyMCE } from './utils/tinyMCEInit';
import * as helpers from './utils/helpers';

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    const page = pages[`./Pages/${name}.vue`];

    if (name.startsWith('Admin/')) {
      page.default.layout = page.default.layout || AdminLayout;
    } else {
      page.default.layout = page.default.layout || Layout;
    }

    return pages[`./Pages/${name}.vue`]
  },
  setup({ el, App, props, plugin }) {
    const vueApp = createApp({ render: () => h(App, props) });
    vueApp.use(plugin)
          .use(ZiggyVue)
          .component('Link', Link);

    for (const [key, value] of Object.entries(helpers)) {
      vueApp.config.globalProperties[`${key}`] = value;
    }

    vueApp.mount(el);

    initTinyMCE();
  },
})
