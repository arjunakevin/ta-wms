<template>
    <div>
        <aside class="aside aside-fixed">
        <div class="aside-header">
            <Link :href="$route('home')" class="aside-logo">Goodang<span>WMS</span></Link>
            <a href="" class="aside-menu-link">
            <i data-feather="menu"></i>
            <i data-feather="x"></i>
            </a>
        </div>
        <div class="aside-body">
            <div class="aside-loggedin">
            <div class="d-flex align-items-center justify-content-start">
                <a href="" class="avatar"><img src="/profile.png" class="rounded-circle" alt=""></a>
                <div class="aside-alert-link">
                <Link :href="$route('logout')" method="post" data-toggle="tooltip" title="Sign out"><i data-feather="log-out"></i></Link>
                </div>
            </div>
            <div class="aside-loggedin-user">
                <a href="#loggedinMenu" class="d-flex align-items-center justify-content-between mg-b-2" data-toggle="collapse">
                <h6 class="tx-semibold mg-b-0">{{ $page.props.auth.user.name }}</h6>
                <i data-feather="chevron-down"></i>
                </a>
                <p class="tx-color-03 tx-12 mg-b-0">{{ $page.props.auth.user.client_id ? 'Client' : 'Administrator' }}</p>
            </div>
            <div class="collapse" id="loggedinMenu">
                <ul class="nav nav-aside mg-b-0">
                <li class="nav-item"><a href="" class="nav-link"><i data-feather="log-out"></i> <span>Sign Out</span></a></li>
                </ul>
            </div>
            </div><!-- aside-loggedin -->
            <ul class="nav nav-aside">
                <template v-for="(menu, index) in sidebar">
                    <li class="nav-item" :key="`menu-${index}`" v-if="menu.link">
                        <Link :href="menu.link" class="nav-link">
                            <i :data-feather="menu.icon"></i> <span>{{ menu.label }}</span>
                        </Link>
                    </li>
                    <li v-else :key="`menu-${index}`" class="nav-label" :class="{ 'mg-t-25': index > 0 }">
                        {{ menu.label }}
                    </li>
                </template>
            </ul>
        </div>
        </aside>

        <div class="content ht-100v pd-0">
            <div class="content-header">
            </div><!-- content-header -->
            <div class="content-body">
                <div class="pd-x-0">
                    <slot />
                </div><!-- container -->
            </div>
        </div>
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue';

export default {
    components: {
        Link
    },
    computed: {
        sidebar() {
            if (this.$page.props.auth.user.client_id) {
                return [
                    {
                        label: 'Dashboard',
                    },
                    {
                        label: 'Dashboard',
                        link: route('home'),
                        icon: 'pie-chart'
                    },
                    {
                        label: 'Inbound',
                    },
                    {
                        label: 'Inbound Delivery',
                        link: route('inbounds.index'),
                        icon: 'arrow-down'
                    },
                    {
                        label: 'Good Receiving',
                        link: route('good_receives.index'),
                        icon: 'file-plus'
                    },
                    {
                        label: 'Inventory',
                    },
                    {
                        label: 'Inventory List',
                        link: route('inventories.index'),
                        icon: 'archive'
                    },
                    {
                        label: 'Outbound',
                    },
                    {
                        label: 'Outbound Delivery',
                        link: route('outbounds.index'),
                        icon: 'arrow-up'
                    },
                    {
                        label: 'Delivery Order',
                        link: route('delivery_orders.index'),
                        icon: 'file-minus'
                    },
                    {
                        label: 'Master Data',
                    },
                    {
                        label: 'Product',
                        link: route('products.index'),
                        icon: 'gift'
                    }
                ];
            } else {
                return [
                    {
                        label: 'Dashboard',
                    },
                    {
                        label: 'Dashboard',
                        link: route('home'),
                        icon: 'pie-chart'
                    },
                    {
                        label: 'Inbound',
                    },
                    {
                        label: 'Inbound Delivery',
                        link: route('inbounds.index'),
                        icon: 'arrow-down'
                    },
                    {
                        label: 'Good Receiving',
                        link: route('good_receives.index'),
                        icon: 'file-plus'
                    },
                    {
                        label: 'Inventory',
                    },
                    {
                        label: 'Inventory List',
                        link: route('inventories.index'),
                        icon: 'archive'
                    },
                    {
                        label: 'Putaway / Picking',
                        link: route('movement_orders.list'),
                        icon: 'file-text'
                    },
                    {
                        label: 'Movement Order',
                        link: route('movement_orders.index'),
                        icon: 'check-circle'
                    },
                    {
                        label: 'Outbound',
                    },
                    {
                        label: 'Outbound Delivery',
                        link: route('outbounds.index'),
                        icon: 'arrow-up'
                    },
                    {
                        label: 'Delivery Order',
                        link: route('delivery_orders.index'),
                        icon: 'file-minus'
                    },
                    {
                        label: 'Master Data',
                    },
                    {
                        label: 'Location',
                        link: route('locations.index'),
                        icon: 'box'
                    },
                    {
                        label: 'Client',
                        link: route('clients.index'),
                        icon: 'pocket'
                    },
                    {
                        label: 'Product',
                        link: route('products.index'),
                        icon: 'gift'
                    }
                ];
            }
        }
    },
    updated() {
        feather.replace();
    }
}
</script>