import Vue from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue';
import { InertiaProgress } from '@inertiajs/progress';
import Layout from './Pages/Layouts/Main';
import VueTypeaheadBootstrap from 'vue-typeahead-bootstrap';

InertiaProgress.init();

const axios = require('axios').default;

Vue.prototype.$axios = axios;
Vue.prototype.$route = route;

Vue.component('vue-typeahead-bootstrap', VueTypeaheadBootstrap);

createInertiaApp({
    resolve: async name => {
        const page = await require(`./Pages/${name}`).default;
        
        page.layout = page.layout || Layout;

        return page;
    },
    setup({ el, App, props }) {
        new Vue({
            render: h => h(App, props),
        }).$mount(el);
    },
});