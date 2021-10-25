<template>
    <div>
        <form @submit.prevent="submit" autocomplete="off">
            <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                            <li class="breadcrumb-item"><a href="#">Outbound</a></li>
                            <li class="breadcrumb-item"><Link :href="$route('delivery_orders.index')">Delivery Order</Link></li>
                            <template v-if="delivery_order">
                                <li class="breadcrumb-item"><Link :href="$route('delivery_orders.show', delivery_order)">{{ delivery_order.reference }}</Link></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit</li>
                            </template>
                            <li v-else class="breadcrumb-item active" aria-current="page">Create</li>
                        </ol>
                    </nav>
                    <h4 class="mg-b-0 tx-spacing--1">{{ delivery_order ? delivery_order.reference : 'Create Delivery Order' }}</h4>
                </div>
                <div class="d-none d-md-block">
                    <button v-show="!form.processing" class="btn btn-sm pd-x-15 btn-success btn-uppercase mg-l-5" type="submit"><i data-feather="save" class="wd-10 mg-r-5"></i> Save</button>
                    <button v-show="form.processing" class="btn btn-sm pd-x-15 btn-success btn-uppercase mg-l-5" type="button" disabled>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span class="sr-only">Loading...</span>
                    </button>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title" v-if="delivery_order">Edit Good Receive Detail (<span class="tx-bold">{{ delivery_order.reference }}</span>)</h5>
                    <h5 class="card-title" v-else>Good Receive Detail</h5>
                    <hr>
                    <div>
                        <div class="form-group row" v-if="delivery_order">
                            <label for="id" class="col-sm-2 col-form-label">ID</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="id" placeholder="ID" v-model="delivery_order.id" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="outbound_reference" class="col-sm-2 col-form-label">Outbound Reference</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="outbound_reference" placeholder="Outbound Reference" v-model="outbound.reference" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="destination_name" class="col-sm-2 col-form-label">Client Code</label>
                            <div class="col-sm-10">
                                <vue-typeahead-bootstrap
                                    v-model="outbound.client.code"
                                    id="client_code"
                                    placeholder="Client Code"
                                    disabled
                                    :data="[]"
                                />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="client_code" class="col-sm-2 col-form-label">Client Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="client_code" placeholder="Client Name" v-model="outbound.client.name" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="destination_name" class="col-sm-2 col-form-label">Destination Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="destination_name" placeholder="Destination Name" v-model="outbound.destination_name" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="destination_phone" class="col-sm-2 col-form-label">Destination Phone</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="destination_phone" placeholder="Destination Name" v-model="outbound.destination_phone" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="destination_address_1" class="col-sm-2 col-form-label">Destination Address 1</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" rows="3" id="destination_address_1" placeholder="Destination Address 1" v-model="outbound.destination_address_1" disabled></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="destination_address_2" class="col-sm-2 col-form-label">Destination Address 2</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" rows="3" id="destination_address_2" placeholder="Destination Address 2" v-model="outbound.destination_address_2" disabled></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="reference" class="col-sm-2 col-form-label">DO Reference <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="reference" placeholder="DO Reference" v-model="form.reference">
                                <small class="text-danger" v-if="errors.reference">{{ errors.reference }}</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="notes" class="col-sm-2 col-form-label">Notes</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" rows="3" id="notes" placeholder="Notes" v-model="form.notes"></textarea>
                                <small class="text-danger" v-if="errors.notes">{{ errors.notes }}</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="status" class="col-sm-2 col-form-label">Status</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="status" placeholder="Status" v-model="form.status" disabled>
                            </div>
                        </div>
                    </div>
                    <div>
                        <hr>
                        <Table :data="formattedDetails" :columnDefs="columnDefs"/>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue';
import Table from '../../Components/Table';

export default {
    props: ['delivery_order', 'outbound', 'details', 'errors'],
    components: {
        Link,
        Table
    },
    data() {
        return {
            form: this.$inertia.form({
                outbound_delivery_id: this.outbound.id,
                reference: '',
                notes: '',
                status: this.getDoStatus(1)
            })
        }
    },
    computed: {
        formattedDetails() {
            if (this.delivery_order) {
                return this.details.map(detail => {
                    return {
                        line_id: detail.outbound_delivery_detail.line_id,
                        product_code: detail.outbound_delivery_detail.product.code,
                        product_description: detail.outbound_delivery_detail.product.description_1,
                        base_quantity: detail.base_quantity,
                        receive_quantity: detail.receive_quantity,
                        open_check_quantity: detail.open_check_quantity,
                        open_putaway_quantity: 0,
                        product_uom_name: detail.outbound_delivery_detail.product.uom_name
                    }
                });
            }

            return this.details.map(detail => {
                return {
                    line_id: detail.line_id,
                    product_code: detail.product.code,
                    product_description: detail.product.description_1,
                    outbound_base_quantity: detail.open_quantity,
                    base_quantity: detail.open_quantity,
                    product_uom_name: detail.product.uom_name
                }
            });
        },
        columnDefs() {
            if (this.delivery_order) {
                return {
                    header: [
                        { label: 'Line ID', style: 'width: 100px', class: 'text-center' },
                        { label: 'Product Code', style: 'width: 250px' },
                        { label: 'Description 1', style: 'width: 200px' },
                        { label: 'Base Quantity', style: 'width: 50px', class: 'text-center' },
                        { label: 'Open Check Quantity', style: 'width: 50px', class: 'text-center' },
                        { label: 'Receive Quantity', style: 'width: 50px', class: 'text-center' },
                        { label: 'Open Putaway Quantity', style: 'width: 50px', class: 'text-center' },
                        { label: 'UoM Name', style: 'width: 50px' },
                    ],
                    row: [
                        { data: 'line_id', tdClass: 'text-center' },
                        { data: 'product_code', spanClass: 'tx-bold' },
                        { data: 'product_description', render: value => this.limitString(value, 40) },
                        { data: 'base_quantity', tdClass: 'text-center' },
                        { data: 'open_check_quantity', tdClass: 'text-center' },
                        { data: 'receive_quantity', tdClass: 'text-center' },
                        { data: 'receive_quantity', tdClass: 'text-center', render: () => 0 },
                        { data: 'product_uom_name' },
                    ]
                }
            }

            return {
                    header: [
                        { label: 'Line ID', style: 'width: 100px', class: 'text-center' },
                        { label: 'Product Code', style: 'width: 250px' },
                        { label: 'Description 1', style: 'width: 200px' },
                        { label: 'Outbound Open Quantity', style: 'width: 50px', class: 'text-center' },
                        { label: 'Base Quantity', style: 'width: 50px', class: 'text-center' },
                        { label: 'UoM Name', style: 'width: 50px' }
                    ],
                    row: [
                        { data: 'line_id', tdClass: 'text-center' },
                        { data: 'product_code', spanClass: 'tx-bold' },
                        { data: 'product_description', render: value => this.limitString(value, 40) },
                        { data: 'outbound_base_quantity', tdClass: 'text-center' },
                        { data: 'base_quantity', tdClass: 'text-center' },
                        { data: 'product_uom_name' }
                    ]
                }
        }
    },
    methods: {
        submit() {
            const options = {
                onSuccess: () => this.editForm = false
            };

            if (this.delivery_order) {
                this.form.put(this.$route('delivery_orders.update', this.delivery_order), options);
            } else {
                this.form.post(this.$route('delivery_orders.store'), options);
            }
        },
    },
    created() {
        if (this.delivery_order) {
            this.form = this.$inertia.form({
                outbound_delivery_id: this.delivery_order.outbound_delivery_id,
                reference: this.delivery_order.reference,
                receive_date: this.delivery_order.receive_date_formatted,
                notes: this.delivery_order.notes,
                status: this.getDoStatus(this.delivery_order.status)
            });
        }
    }
}
</script>