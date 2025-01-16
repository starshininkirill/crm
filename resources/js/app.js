import './bootstrap';
import '../css/app.css';
import { createApp, h } from 'vue'
import { createInertiaApp, Link } from '@inertiajs/vue3'
import { ZiggyVue } from 'ziggy-js';
import Layout from './Layouts/Layout.vue';
import AdminLayout from './Layouts/AdminLayout.vue';

import 'tinymce/tinymce';
import 'tinymce/skins/ui/oxide/skin.min.css';
import 'tinymce/icons/default/icons';
import 'tinymce/themes/silver/theme';
import 'tinymce/models/dom/model';
import 'tinymce/plugins/lists';


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


createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    const page = pages[`./Pages/${name}.vue`];

    if(name.startsWith('Admin/')){
      page.default.layout = page.default.layout || AdminLayout;
    }else{
      page.default.layout = page.default.layout || Layout;
    }

    return pages[`./Pages/${name}.vue`]
  },
  setup({ el, App, props, plugin }) {
    const vueApp = createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue)
      .component('Link', Link)
      .mount(el)
  },
})