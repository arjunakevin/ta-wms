<template>
    <form @submit.prevent="submit" autocomplete="off">
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                        <li class="breadcrumb-item"><a href="#">Inventory</a></li>
                        <li class="breadcrumb-item"><Link :href="$route('movement_orders.index')">Movement Order</Link></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ movement_order ? movement_order.reference : 'Create' }}</li>
                    </ol>
                </nav>
                <h4 class="mg-b-0 tx-spacing--1">{{ movement_order ? movement_order.reference : 'Create Movement Order' }}</h4>
            </div>
            <div class="d-none d-md-block">
                <button v-show="!form.processing" class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-l-5" type="submit"><i data-feather="save" class="wd-10 mg-r-5"></i> Save</button>
                <button v-show="form.processing" class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-l-5" type="button" disabled>
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span class="sr-only">Loading...</span>
                </button>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title" v-if="movement_order">Edit Movement List (<span class="tx-bold">{{ movement_order.reference }}</span>)</h5>
                <h5 class="card-title" v-else>Movement Order Detail</h5>
                <hr>
                <div>
                    <div class="form-group row" v-if="movement_order">
                        <label for="id" class="col-sm-2 col-form-label">ID</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="id" placeholder="ID" v-model="movement_order.id" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="type" class="col-sm-2 col-form-label">Movement Type</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="type" placeholder="Movement Type" :value="type == 1 ? 'Putaway' : 'Picking'" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="document_id" class="col-sm-2 col-form-label">Document ID</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="document_id" placeholder="Document ID" :value="document.id" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="document_reference" class="col-sm-2 col-form-label">Document Reference</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="document_reference" placeholder="Document Reference" :value="document.reference" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="reference" class="col-sm-2 col-form-label">Movement Reference <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="reference" placeholder="Movement Reference" v-model="form.reference">
                            <small class="text-danger" v-if="errors.reference">{{ errors.reference }}</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="date" class="col-sm-2 col-form-label">Movement Date <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="datetime-local" class="form-control" id="date" placeholder="Movement Date" v-model="form.date">
                            <small class="text-danger" v-if="errors.date">{{ errors.date }}</small>
                        </div>
                    </div>
                </div>
                <hr>
                <Table :data="details" :columnDefs="columnDefs" />
            </div>
        </div>
    </form>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue';
import Pagination from '../../Components/Pagination';
import Table from '../../Components/Table';

export default {
    props: ['movement_order', 'errors', 'type', 'document', 'details'],
    components: {
        Link,
        Table,
        Pagination
    },
    computed: {
        columnDefs() {
            const data = {
                header: [
                    { label: 'Product Code', style: 'width: 250px' },
                    { label: 'Description 1', style: 'width: 200px' },
                    { label: 'Base Quantity', style: 'width: 50px', class: 'text-center' },
                    { label: this.type == 1 ? 'Available Put Quantity' : 'Pick Quantity', style: 'width: 50px', class: 'text-center' },
                    { label: 'UoM Name', style: 'width: 50px' }
                ]
            };

            if (this.type == 1) {
                data.row = [
                    { data: 'product.code', spanClass: 'tx-bold' },
                    { data: 'product.description_1', render: value => this.limitString(value, 40) },
                    { data: 'base_quantity', tdClass: 'text-center' },
                    { data: 'available_pick_quantity', tdClass: 'text-center' },
                    { data: 'product.uom_name' }
                ]
            } else {
                data.row = [
                    { data: 'outbound_delivery_detail.product.code', spanClass: 'tx-bold' },
                    { data: 'outbound_delivery_detail.product.description_1', render: value => this.limitString(value, 40) },
                    { data: 'base_quantity', tdClass: 'text-center' },
                    { data: 'open_pick_quantity', tdClass: 'text-center' },
                    { data: 'outbound_delivery_detail.product.uom_name' }
                ]
            }

            return data;
        }
    },
    data() {
        return {
            form: this.$inertia.form({
                reference: '',
                date: '',
                type: this.type,
                document_id: this.document.id
            })
        }
    },
    methods: {
        submit() {
            this.form.post(this.$route('movement_orders.store'))
        }
    }
}
</script>