<template>
    <div>
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                        <li class="breadcrumb-item"><a href="#">Inbound</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Good Receiving</li>
                    </ol>
                </nav>
                <h4 class="mg-b-0 tx-spacing--1">Good Receiving List</h4>
            </div>
            <div class="d-none d-md-block">
                <a href="#search-modal" data-toggle="modal" class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-l-5"><i data-feather="plus" class="wd-10 mg-r-5"></i> Create Good Receiving</a>
            </div>
        </div>
        <Table :data="data" :columnDefs="columnDefs" view-route="good_receives.show"/>
        <Pagination :data="data"/>

        <div class="modal fade" id="search-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <form class="modal-content tx-14" @submit.prevent="search" autocomplete="off">
                    <div class="modal-header">
                        <h6 class="modal-title" id="exampleModalLabel2">Search Inbound Delivery</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger" role="alert" v-if="errors.inbound">
                            {{ errors.inbound }}
                        </div>
                        <div class="form-group row">
                            <label for="reference" class="col-sm-3 col-form-label">Reference <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="reference" placeholder="Reference" v-model="form.reference">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="client_code" class="col-sm-3 col-form-label">Client Code <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <vue-typeahead-bootstrap
                                    v-model="form.client_code"
                                    id="client_code"
                                    :data="clients"
                                    placeholder="Client Code"
                                    :minMatchingChars="1"
                                    @input="searchClient"
                                    :serializer="item => `${item.name} (${item.code})`"
                                    @hit="setClient($event)"
                                />
                                <small class="text-danger" v-if="errors.client_code">{{ errors.client_code }}</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="client_name" class="col-sm-3 col-form-label">Client Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="client_name" placeholder="Client Name" v-model="selected_client.name" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">Cancel</button>
                        <button v-show="!form.processing" type="submit" class="btn btn-primary tx-13">Search</button>
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
    props: ['data', 'errors'],
    components: {
        Pagination,
        Table,
        Link
    },
    data() {
        return {
            form: this.$inertia.form({
                reference: '',
                client_code: ''
            }),
            clients: [],
            selected_client: {},
            columnDefs: {
                header: [
                    { label: 'ID', style: 'width: 200px', class: 'text-center' },
                    { label: 'Reference' },
                    { label: 'Inbound Reference' },
                    { label: 'Client Code' },
                    { label: 'Receive Date', style: 'width: 200px', class: 'text-center' },
                    { label: 'Status', style: 'width: 200px', class: 'text-center' }
                ],
                row: [
                    { data: 'id', tdClass: 'text-center' },
                    { data: 'reference', spanClass: 'tx-bold' },
                    { data: 'inbound_delivery.reference' },
                    { data: 'inbound_delivery.client.code' },
                    { data: 'receive_date', tdClass: 'text-center' },
                    { data: 'status', tdClass: 'text-center', render: this.getGrStatusLabel  }
                ]
            }
        }
    },
    methods: {
        search() {
            this.form.post(this.$route('good_receives.inbound.search'), {
                onBefore: () => $('#search-modal').modal('hide'),
                onError: () => $('#search-modal').modal('show')
            });
        },
        searchClient: debounce(function() {
            const params = { query: this.form.client_code };
            this.$axios.get(this.$route('clients.list'), { params })
                .then(data => {
                    this.clients = data.data;
                });
        }, 500),
        setClient(event) {
            this.selected_client = event;

            this.form.client_code = '';

            setTimeout(() => {
                this.form.client_code = event.code;
            }, 1);
        }
    }
}
</script>