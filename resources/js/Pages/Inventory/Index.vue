<template>
    <div>
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                        <li class="breadcrumb-item"><a href="#">Inventory</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Inventory List</li>
                    </ol>
                </nav>
                <h4 class="mg-b-0 tx-spacing--1">Inventory List</h4>
            </div>
            <div class="d-none d-md-block">
                <a href="#" class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-l-5"><i data-feather="filter" class="wd-10 mg-r-5"></i> Filter</a>
            </div>
        </div>
        <Table :data="data" :columnDefs="columnDefs"/>
        <Pagination :data="data"/>
    </div>
</template>

<script>
import Pagination from '../../Components/Pagination';
import Table from '../../Components/Table';

export default {
    props: ['data'],
    components: {
        Pagination,
        Table
    },
    data() {
        return {
            columnDefs: {
                header: [
                    { label: 'Product Code' },
                    { label: 'Description 1' },
                    { label: 'Location Code' },
                    { label: 'Document ID' },
                    { label: 'Base Quantity', style: 'width: 150px', class: 'text-center' },
                    { label: 'Pick Quantity', style: 'width: 150px', class: 'text-center' },
                    { label: 'Put Quantity', style: 'width: 150px', class: 'text-center' }
                ],
                row: [
                    { data: 'product.code', spanClass: 'tx-bold' },
                    { data: 'product.description_1', render: value => this.limitString(value, 40) },
                    { data: 'location', spanClass: 'tx-bold', render: location => location.code || location },
                    { data: 'documentable_id', tdClass: 'text-center' },
                    { data: 'base_quantity', tdClass: 'text-center' },
                    { data: 'pick_quantity', tdClass: 'text-center' },
                    { data: 'put_quantity', tdClass: 'text-center' }
                ]
            }
        }
    }
}
</script>