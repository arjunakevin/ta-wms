<template>
    <div>
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                        <li class="breadcrumb-item"><a href="#">Inventory</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Putaway / Picking</li>
                    </ol>
                </nav>
                <h4 class="mg-b-0 tx-spacing--1">Putaway / Picking List</h4>
            </div>
            <div class="d-none d-md-block">
                <a href="#" class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-l-5"><i data-feather="filter" class="wd-10 mg-r-5"></i> Filter</a>
                <a v-show="!form.processing" href="#search-modal" data-toggle="modal" class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-l-5"><i data-feather="plus" class="wd-10 mg-r-5"></i> Create Movement Order</a>
                <button v-show="form.processing" class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-l-5" type="button" disabled>
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span class="sr-only">Loading...</span>
                </button>
            </div>
        </div>
        <div class="alert alert-success" role="alert" v-if="$page.props.flash.message">
            {{ $page.props.flash.message }}
        </div>

        <div class="modal fade" id="search-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <form class="modal-content tx-14" @submit.prevent="search" autocomplete="off">
                    <div class="modal-header">
                        <h6 class="modal-title" id="exampleModalLabel2">Create New Movement</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger" role="alert" v-if="errors.document">
                            {{ errors.document }}
                        </div>
                        <div class="form-group row">
                            <label for="id" class="col-sm-3 col-form-label">GR / DO ID <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="id" placeholder="GR / DO ID" v-model="form.id">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="type" class="col-sm-3 col-form-label">Type <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-control select2">
                                    <option value="1">Putaway</option>
                                    <option value="2">Picking</option>
                                </select>
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
        <Table :data="data" :columnDefs="columnDefs"/>
        <Pagination :data="data"/>
    </div>
</template>

<script>
import Pagination from '../../Components/Pagination';
import Table from '../../Components/Table';

export default {
    props: ['data', 'errors'],
    components: {
        Pagination,
        Table
    },
    computed: {
        processType() {
            return this.process == 1 ? 'Confirm' : 'Cancel';
        },
        confirming() {
            return this.process == 1 && this.processing;
        },
        canceling() {
            return this.process == 0 && this.processing;
        }
    },
    data() {
        return {
            form: this.$inertia.form({
                type: 1,
                id: ''
            }),
            columnDefs: {
                header: [
                    { label: 'ID', style: 'width: 200px', class: 'text-center' },
                    { label: 'Reference' },
                    { label: 'Date' },
                    { label: 'Line Count', style: 'width: 200px', class: 'text-center' },
                    { label: 'Type', style: 'width: 200px', class: 'text-center' }
                ],
                row: [
                    { data: 'id', tdClass: 'text-center' },
                    { data: 'reference', spanClass: 'tx-bold' },
                    { data: 'date' },
                    { data: 'details_count', tdClass: 'text-center' },
                    { data: 'type', tdClass: 'text-center' }
                ]
            }
        }
    },
    methods: {
        search() {
            this.form.post(this.$route('movement_orders.document.search'), {
                onBefore: () => $('#search-modal').modal('hide'),
                onError: () => $('#search-modal').modal('show')
            });
        }
    },
    mounted() {
        $('.select2').select2({
            minimumResultsForSearch: Infinity
        });
    }
}
</script>