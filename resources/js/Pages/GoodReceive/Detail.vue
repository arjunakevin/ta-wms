<template>
    <div>
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                        <li class="breadcrumb-item"><a href="#">Inbound</a></li>
                        <li class="breadcrumb-item"><Link :href="$route('grs.index')">Good Receiving</Link></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ good_receive.reference }}</li>
                    </ol>
                </nav>
                <h4 class="mg-b-0 tx-spacing--1">{{ good_receive.reference }}</h4>
            </div>
            <div class="d-none d-md-block">
                <a v-show="!receiving" href="#receive-modal" data-toggle="modal" class="btn btn-sm pd-x-15 btn-success btn-uppercase"><i data-feather="check" class="wd-10 mg-r-5"></i> Receive</a>
                <button v-show="receiving" class="btn btn-sm pd-x-15 btn-success btn-uppercase" type="button" disabled>
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span class="sr-only">Loading...</span>
                </button>
                <Link :href="$route('grs.edit', good_receive)" as="button" type="button" class="btn btn-sm pd-x-15 btn-primary btn-uppercase"><i data-feather="edit" class="wd-10 mg-r-5"></i> Edit</Link>
                <a v-show="!deleting" href="#delete-modal" data-toggle="modal" class="btn btn-sm pd-x-15 btn-danger btn-uppercase"><i data-feather="trash" class="wd-10 mg-r-5"></i> Delete</a>
                <button v-show="deleting" class="btn btn-sm pd-x-15 btn-danger btn-uppercase" type="button" disabled>
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span class="sr-only">Loading...</span>
                </button>
            </div>
        </div>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active">Good Receiving</a>
            </li>
            <li class="nav-item">
                <Link :href="$route('grs.check', good_receive)" class="nav-link">Item Check</Link>
            </li>
        </ul>
        <div class="tab-content bd bd-gray-300 bd-t-0 pd-20" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                <div class="alert alert-danger" role="alert" v-if="errors.message">
                    {{ errors.message }}
                </div>
                <div class="alert alert-success" role="alert" v-if="$page.props.flash.message">
                    {{ $page.props.flash.message }}
                </div>
                <form>
                    <div class="form-group row">
                        <label for="id" class="col-sm-2 col-form-label">Good Receive ID</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            <span class="tx-bold">{{ good_receive.id }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inbound_reference" class="col-sm-2 col-form-label">Inbound Reference</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            <span class="tx-bold">{{ good_receive.inbound_delivery.reference }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="reference" class="col-sm-2 col-form-label">Good Receive Reference</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            <span class="tx-bold">{{ good_receive.reference }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="client_code" class="col-sm-2 col-form-label">Client</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            <span>{{ good_receive.inbound_delivery.client.name }} (<span class="tx-bold">{{ good_receive.inbound_delivery.client.code }}</span>)</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="receive_date" class="col-sm-2 col-form-label">Receive Date</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            <span class="tx-bold">{{ good_receive.receive_date }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="notes" class="col-sm-2 col-form-label">Notes</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            <span class="tx-bold">{{ good_receive.notes || '-' }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 pt-0">Status</label>
                        <div class="col-sm-10" v-html="getGrStatusLabel(good_receive.status)">
                        </div>
                    </div>
                    <hr>
                    <Table :data="formattedDetails" :columnDefs="columnDefs" />
                    <Pagination :data="details"/>
                </form>
            </div>
        </div>

        <div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content tx-14">
                    <div class="modal-header">
                        <h6 class="modal-title" id="exampleModalLabel2">Delete Good Receiving</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="mg-b-0">Deleted data can't be recovered.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger tx-13" @click="submitDeletion">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="receive-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content tx-14">
                    <div class="modal-header">
                        <h6 class="modal-title" id="exampleModalLabel2">Receive Product</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="mg-b-0">Receiving will add product stock to inventory.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success tx-13" @click="receive">Receive</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue';
import Pagination from '../../Components/Pagination';
import Table from '../../Components/Table';

export default {
    props: ['good_receive', 'details', 'errors'],
    components: {
        Link,
        Pagination,
        Table
    },
    data() {
        return {
            deleting: false,
            receiving: false,
            columnDefs: {
                header: [
                    { label: 'Line ID', style: 'width: 100px', class: 'text-center' },
                    { label: 'Product Code', style: 'width: 250px' },
                    { label: 'Description 1', style: 'width: 200px' },
                    { label: 'Base Quantity', style: 'width: 50px', class: 'text-center' },
                    { label: 'Open Check Quantity', style: 'width: 50px', class: 'text-center' },
                    { label: 'Receive Quantity', style: 'width: 50px', class: 'text-center' },
                    { label: 'Open Putaway Quantity', style: 'width: 50px', class: 'text-center' },
                ],
                row: [
                    { data: 'line_id', tdClass: 'text-center' },
                    { data: 'product_code', spanClass: 'tx-bold' },
                    { data: 'product_description', render: value => this.limitString(value, 40) },
                    { data: 'base_quantity', tdClass: 'text-center' },
                    { data: 'open_check_quantity', tdClass: 'text-center' },
                    { data: 'receive_quantity', tdClass: 'text-center' },
                    { data: 'open_putaway_quantity', tdClass: 'text-center' }
                ]
            },
            formattedDetails: this.details.data.map(detail => {
                return {
                    line_id: detail.inbound_delivery_detail.line_id,
                    product_code: detail.inbound_delivery_detail.product.code,
                    product_description: detail.inbound_delivery_detail.product.description_1,
                    base_quantity: detail.base_quantity,
                    receive_quantity: detail.receive_quantity,
                    open_check_quantity: detail.open_check_quantity,
                    open_putaway_quantity: detail.inventory ? detail.inventory.available_pick_quantity : 0
                }
            })
        }
    },
    methods: {
        submitDeletion() {
            this.$inertia.delete(this.$route('grs.destroy', this.good_receive), {
                onBefore: () => {
                    this.deleting = true;

                    $('#delete-modal').modal('hide');
                },
                onFinish: () => this.deleting = false
            });
        },
        receive() {
            this.$inertia.post(this.$route('grs.receive', this.good_receive), null, {
                preserveState: false,
                resetOnSuccess: false,
                onBefore: () => {
                    this.receiving = true;

                    $('#receive-modal').modal('hide');
                },
                onFinish: () => this.receiving = false
            });
        }
    }
}
</script>