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

const getInboundStatus = status => {
    if (status == 1) return 'Unreceived';
    if (status == 2) return 'Partially Received';
    if (status == -99) return 'Fully Received';
}

const getGrStatus = status => {
    if (status == 1) return 'Draft';
    if (status == 2) return 'Partially Checked';
    if (status == 3) return 'Fully Checked';
    if (status == 4) return 'Received';
}

Vue.mixin({
    methods: {
        limitString: (str, limit = 100) => str.substring(0, limit) + '...',
        getInboundStatusLabel: status => {
            const state = status > 0 ? 'badge-primary' : 'badge-success';
            let label = getInboundStatus(status);
            
            return `<span class="badge ${state}">${label}</span>`;
        },
        getInboundStatus: status => getInboundStatus(status),
        getGrStatusLabel: status => {
            const state = status > 0 ? 'badge-primary' : 'badge-success';
            let label = getGrStatus(status);
            
            return `<span class="badge ${state}">${label}</span>`;
        },
        getMovementStatusLabel: status => {
            const state = {
                '0': 'badge-primary',
                '1': 'badge-success',
                '-1': 'badge-danger',
            };

            const label = {
                '0': 'Open',
                '1': 'Confirmed',
                '-1': 'Canceled',
            };

            const movementState = state[status];
            const movementLabel = label[status];
            
            return `<span class="badge ${movementState}">${movementLabel}</span>`;
        },
        getGrStatus: status => getGrStatus(status)
    }
})

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