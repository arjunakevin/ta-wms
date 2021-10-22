<template>
    <div>
        <aside class="aside aside-fixed">
        <div class="aside-header">
            <a href="../../index.html" class="aside-logo">dash<span>forge</span></a>
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
                <a href="" class="new" data-toggle="tooltip" title="You have 2 unread messages"><i data-feather="message-square"></i></a>
                <a href="" class="new" data-toggle="tooltip" title="You have 4 new notifications"><i data-feather="bell"></i></a>
                <a href="" data-toggle="tooltip" title="Sign out"><i data-feather="log-out"></i></a>
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
                <li class="nav-item"><a href="" class="nav-link"><i data-feather="edit"></i> <span>Edit Profile</span></a></li>
                <li class="nav-item"><a href="" class="nav-link"><i data-feather="user"></i> <span>View Profile</span></a></li>
                <li class="nav-item"><a href="" class="nav-link"><i data-feather="settings"></i> <span>Account Settings</span></a></li>
                <li class="nav-item"><a href="" class="nav-link"><i data-feather="help-circle"></i> <span>Help Center</span></a></li>
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
                <div class="content-search">
                <i data-feather="search"></i>
                <input type="search" class="form-control" placeholder="Search...">
                </div>
                <nav class="nav">
                <a href="" class="nav-link"><i data-feather="help-circle"></i></a>
                <a href="" class="nav-link"><i data-feather="grid"></i></a>
                <a href="" class="nav-link"><i data-feather="align-left"></i></a>
                </nav>
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
                        link: route('grs.index'),
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
                        link: '#',
                        icon: 'arrow-up'
                    },
                    {
                        label: 'Delivery Order',
                        link: '#',
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
                        link: route('grs.index'),
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
                        link: '#',
                        icon: 'arrow-up'
                    },
                    {
                        label: 'Delivery Order',
                        link: '#',
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