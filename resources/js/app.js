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
    const label  = {
        '1': 'Unreceived',
        '2': 'Partially Received',
        '-1': 'Fully Received'
    };

    return label[status];
}

const getGrStatus = status => {
    const label = {
        '1': 'Draft',
        '2': 'Partially Checked',
        '3': 'Fully Checked',
        '4': 'Received',
        '5': 'Partial Putaway',
        '-1': 'Full Putaway',
    };

    return label[status];
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
            const state = {
                '1': 'badge-primary',
                '2': 'badge-primary',
                '3': 'badge-primary',
                '4': 'badge-info',
                '5': 'badge-warning',
                '-1': 'badge-success',
            };

            const grState = state[status];
            const grLabel = getGrStatus(status);
            
            return `<span class="badge ${grState}">${grLabel}</span>`;
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