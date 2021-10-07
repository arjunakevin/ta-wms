<template>
    <div>
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                        <li class="breadcrumb-item"><a href="#">Master Data</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Location</li>
                    </ol>
                </nav>
                <h4 class="mg-b-0 tx-spacing--1">Location List</h4>
            </div>
            <div class="d-none d-md-block">
                <Link :href="$route('locations.create')" as="button" class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-l-5"><i data-feather="plus" class="wd-10 mg-r-5"></i> Create Location</Link>
            </div>
        </div>
        <Table :data="data" :columnDefs="columnDefs" view-route="locations.show"/>
        <Pagination :data="data"/>
    </div>
</template>

<script>
import Pagination from '../../Components/Pagination';
import Table from '../../Components/Table';
import { Link } from '@inertiajs/inertia-vue';

export default {
    props: ['data'],
    components: {
        Pagination,
        Table,
        Link
    },
    data() {
        return {
            columnDefs: {
                header: [
                    { label: 'ID', style: 'width: 50px', class: 'text-center' },
                    { label: 'Code' },
                    { label: 'Pick Blocked', style: 'width: 150px', class: 'text-center' },
                    { label: 'Put Blocked', style: 'width: 150px', class: 'text-center' },
                ],
                row: [
                    { data: 'id', tdClass: 'text-center' },
                    { data: 'code', spanClass: 'tx-bold' },
                    { data: 'pick_blocked', tdClass: 'text-center', render: this.renderLabel },
                    { data: 'put_blocked', tdClass: 'text-center', render: this.renderLabel },
                ]
            }
        }
    },
    methods: {
        renderLabel(value) {
            return value ? `<span class="badge badge-danger">Yes</span>` : `<span class="badge badge-success">No</span>`;
        }
    }
}
</script>