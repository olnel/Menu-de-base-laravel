import '../css/app.css';
import './bootstrap';
import { registerSW } from 'virtual:pwa-register';

registerSW({ immediate: true });
import './Icons/icons';
import 'vue3-perfect-scrollbar/style.css';

import {createInertiaApp} from '@inertiajs/vue3';
import {resolvePageComponent} from 'laravel-vite-plugin/inertia-helpers';
import {createApp, h} from 'vue';
import {ZiggyVue} from '../../vendor/tightenco/ziggy';
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import AntdvTheme from "../Theme/antdv-theme.js";
import {PerfectScrollbarPlugin} from 'vue3-perfect-scrollbar';

import Loader from './Components/Loader.vue';
import Layout from "@/Layouts/Layout.vue";

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: async (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue');
        const page = await pages[`./Pages/${name}.vue`]();

        if (name.startsWith('Errors/')) {
            page.default.layout = false;
        } else if (!page.default.layout) {
            page.default.layout = Layout;
        }

        return page;
    },
    setup({el, App, props, plugin}) {
        return createApp({render: () => h(App, props)})
            .use(plugin)
            .use(ZiggyVue)
            .use(PerfectScrollbarPlugin)
            .component('Loader', Loader)
            .component('font-awesome-icon', FontAwesomeIcon)
            .mount(el);
    },
    // progress: {
    //     color: AntdvTheme.token.colorPrimary,
    // },
    progress: false,
});
