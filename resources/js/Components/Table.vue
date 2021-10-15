<template>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th v-for="(header, i) in columnDefs.header" :key="`header-${i}`" scope="col" :style="`vertical-align: middle; ${header.style}`" :class="header.class">
                        {{ header.label }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <template v-if="rowData.length">
                    <tr v-for="item in rowData" :key="item.id" @dblclick="view(item)" style="cursor: pointer">
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
    computed: {
        rowData() {
            if (this.data.data) {
                return this.data.data;
            }

            return this.data;
        }
    },
    methods: {
        getData(item, key) {
            key.split('.').forEach(k => {
                item = item[k];
            });

            if (item == null) {
                return '-';
            }

            return item;
        },
        view(item) {
            if (!this.viewRoute) {
                return false;
            }

            this.$inertia.get(this.$route(this.viewRoute, item));
        }
    }
}
</script>