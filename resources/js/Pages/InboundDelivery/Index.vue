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
                <a v-show="!form.processing" href="#search-modal" data-toggle="modal" class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-l-5"><i data-feather="filter" class="wd-10 mg-r-5"></i> Filter</a>
                <button v-show="form.processing" class="btn btn-sm pd-x-15 btn-primary btn-uppercase" type="button" disabled>
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span class="sr-only">Loading...</span>
                </button>
                <Link :href="$route('inbounds.create')" as="button" class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-l-5"><i data-feather="plus" class="wd-10 mg-r-5"></i> Create Inbound Delivery</Link>
            </div>
        </div>
        <Table :data="data" :columnDefs="columnDefs" view-route="inbounds.show"/>
        <Pagination :data="data"/>

        <div class="modal fade" id="search-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <form class="modal-content tx-14" @submit.prevent="filter" autocomplete="off">
                    <div class="modal-header">
                        <h6 class="modal-title" id="exampleModalLabel2">Filter</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <label for="id" class="col-sm-3 col-form-label">ID</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="id" placeholder="ID" v-model="form.id">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="reference" class="col-sm-3 col-form-label">Reference</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="reference" placeholder="Reference" v-model="form.reference">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="client_code" class="col-sm-3 col-form-label">Client Code</label>
                            <div class="col-sm-9">
                                <vue-typeahead-bootstrap
                                    v-model="form.client_code"
                                    id="client_code"
                                    :data="clients"
                                    placeholder="Client Code"
                                    :minMatchingChars="1"
                                    @input="searchClient"
                                    :serializer="item => `${item.code}`"
                                />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="arrival_date_from" class="col-sm-3 col-form-label">Arrival Date From</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" placeholder="Arrival Date From" v-model="form.arrival_date_from">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="arrival_date_to" class="col-sm-3 col-form-label">Arrival Date To</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" placeholder="Arrival Date To" v-model="form.arrival_date_to">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="po_date_from" class="col-sm-3 col-form-label">PO Date From</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" placeholder="PO Date From" v-model="form.po_date_from">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="po_date_to" class="col-sm-3 col-form-label">PO Date To</label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" placeholder="PO Date To" v-model="form.po_date_to">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="type" class="col-sm-3 col-form-label">Status</label>
                            <div class="col-sm-9">
                                <select2 v-model="form.status">
                                    <option value="open">Open</option>
                                    <option value="closed">Closed</option>
                                </select2>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">Cancel</button>
                        <button v-show="!form.processing" type="submit" class="btn btn-primary tx-13">Filter</button>
                        <button v-show="form.processing" class="btn btn-primary tx-13" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span class="sr-only">Loading...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import Pagination from '../../Components/Pagination';
import Table from '../../Components/Table';
import { Link } from '@inertiajs/inertia-vue';
import { debounce } from 'lodash';

export default {
    props: ['data'],
    components: {
        Pagination,
        Table,
        Link
    },
    data() {
        return {
            form: this.$inertia.form({
                id: '',
                reference: '',
                client_code: '',
                arrival_date_from: null,
                arrival_date_to: null,
                po_date_from: null,
                po_date_to: null,
                status: 'open'
            }),
            clients: [],
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
    },
    methods: {
        filter() {
            this.form.get(this.$route('inbounds.index'), {
                preserveState: true,
                onBefore: () => {
                    $('#search-modal').modal('hide');
                }
            });
        },
        searchClient: debounce(function() {
            const params = { query: this.form.client_code };
            this.$axios.get(this.$route('clients.list'), { params })
                .then(data => {
                    this.clients = data.data;
                });
        }, 500),
    }
}
</script>