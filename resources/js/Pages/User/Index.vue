<template>
    <div>
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                        <li class="breadcrumb-item"><a href="#">Master Data</a></li>
                        <li class="breadcrumb-item active" aria-current="page">User</li>
                    </ol>
                </nav>
                <h4 class="mg-b-0 tx-spacing--1">User List</h4>
            </div>
            <div class="d-none d-md-block">
                <Link :href="$route('users.create')" as="button" class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-l-5"><i data-feather="plus" class="wd-10 mg-r-5"></i> Create User</Link>
            </div>
        </div>
        <Table :data="data" :columnDefs="columnDefs" view-route="users.show"/>
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
                    { label: 'Client Code', style: 'width: 200px' },
                    { label: 'Name', style: 'width: 200px' },
                    { label: 'Username' },
                    { label: 'Email' },
                ],
                row: [
                    { data: 'id', tdClass: 'text-center' },
                    { data: 'client.code' },
                    { data: 'name', spanClass: 'tx-bold' },
                    { data: 'username' },
                    { data: 'email' },
                ]
            }
        }
    }
}
</script>