<template>
    <ul class="pagination mg-b-0 justify-content-end pagination-outline">
        <li v-for="(link, index) in data.links" :key="`link-${index}`" class="page-item" :class="{ disabled: !link.url, active: link.active }">
            <Link :href="link.url" class="page-link"><span v-html="link.label" ></span></Link>
        </li>
    </ul>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue';

export default {
    components: {
        Link
    },
    props: ['data']
}
</script>