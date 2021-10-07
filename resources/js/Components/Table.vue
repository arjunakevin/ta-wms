<template>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th v-for="(header, i) in columnDefs.header" :key="`header-${i}`" scope="col" :style="header.style" :class="header.class">
                        {{ header.label }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <template v-if="data.data.length">
                    <tr v-for="item in data.data" :key="item.id" @dblclick="view(item)" style="cursor: pointer">
                        <td v-for="(r, j) in columnDefs.row" :key="`row-${j}`" scope="row" style="vertical-align: middle" :class="r.tdClass">
                            <span :class="r.spanClass" v-html="r.render ? r.render(getData(item, r.data)) : getData(item, r.data)">
                            </span>
                        </td>
                    </tr>
                </template>
                <tr v-else>
                    <td :colspan="columnDefs.header.length" class="text-center">No Data</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    props: ['data', 'columnDefs', 'view-route'],
    methods: {
        getData(item, key) {
            key.split('.').forEach(k => {
                item = item[k];
            });

            return item;
        },
        view(item) {
            this.$inertia.get(this.$route(this.viewRoute, item));
        }
    }
}
</script>