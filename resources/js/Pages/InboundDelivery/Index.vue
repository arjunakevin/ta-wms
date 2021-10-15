<template>
    <div>
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                        <li class="breadcrumb-item"><a href="#">Inbound</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Inbound Delivery</li>
                    </ol>
                </nav>
                <h4 class="mg-b-0 tx-spacing--1">Inbound Delivery List</h4>
            </div>
            <div class="d-none d-md-block">
                <Link :href="$route('inbounds.create')" as="button" class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-l-5"><i data-feather="plus" class="wd-10 mg-r-5"></i> Create Inbound Delivery</Link>
            </div>
        </div>
        <Table :data="data" :columnDefs="columnDefs" view-route="inbounds.show"/>
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
                    { label: 'ID', style: 'width: 200px', class: 'text-center' },
                    { label: 'Reference' },
                    { label: 'Client Code' },
                    { label: 'Arrival Date', style: 'width: 200px', class: 'text-center' },
                    { label: 'PO Date', style: 'width: 200px', class: 'text-center' },
                    { label: 'Line Count', style: 'width: 150px', class: 'text-center' },
                    { label: 'Status', style: 'width: 200px', class: 'text-center' }
                ],
                row: [
                    { data: 'id', tdClass: 'text-center' },
                    { data: 'reference', spanClass: 'tx-bold' },
                    { data: 'client.code' },
                    { data: 'arrival_date', tdClass: 'text-center' },
                    { data: 'po_date', tdClass: 'text-center' },
                    { data: 'details_count', tdClass: 'text-center' },
                    { data: 'status', tdClass: 'text-center', render: this.getInboundStatusLabel  }
                ]
            }
        }
    }
}
</script>