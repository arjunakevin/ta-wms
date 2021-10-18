<template>
    <div>
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                        <li class="breadcrumb-item"><a href="#">Inventory</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Movement Order</li>
                    </ol>
                </nav>
                <h4 class="mg-b-0 tx-spacing--1">Movement Order List</h4>
            </div>
            <div class="d-none d-md-block">
                <button v-show="!confirming" class="btn btn-sm pd-x-15 btn-success btn-uppercase mg-l-5" @click="showProcessModal(1)"><i data-feather="check" class="wd-10 mg-r-5"></i> Confirm</button>
                <button v-show="confirming" class="btn btn-sm pd-x-15 btn-success btn-uppercase mg-l-5" type="button" disabled>
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span class="sr-only">Loading...</span>
                </button>
                <button v-show="!canceling" class="btn btn-sm pd-x-15 btn-danger btn-uppercase mg-l-5" @click="showProcessModal(0)"><i data-feather="x" class="wd-10 mg-r-5"></i> Cancel</button>
                <button v-show="canceling" class="btn btn-sm pd-x-15 btn-danger btn-uppercase mg-l-5" type="button" disabled>
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span class="sr-only">Loading...</span>
                </button>
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
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th scope="col" style="vertical-align: middle;" class="text-center">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="check-all" v-model="selectAll">
                                <label class="custom-control-label" for="check-all">&nbsp;</label>
                            </div>
                        </th>
                        <th scope="col" style="vertical-align: middle;">
                            ID
                        </th>
                        <th scope="col" style="vertical-align: middle;">
                            Date
                        </th>
                        <th scope="col" style="vertical-align: middle;">
                            Reference
                        </th>
                        <th scope="col" style="vertical-align: middle;">
                            Product Code
                        </th>
                        <th scope="col" style="vertical-align: middle;">
                            Description 1
                        </th>
                        <th scope="col" style="vertical-align: middle; width: 50px" class="text-center">
                            Base Quantity
                        </th>
                        <th scope="col" style="vertical-align: middle; width: 100px" class="text-center">
                            Type
                        </th>
                        <th scope="col" style="vertical-align: middle;">
                            Src. / Dest. Location
                        </th>
                        <th scope="col" style="vertical-align: middle; width: 100px" class="text-center">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <template v-if="data.data.length">
                        <tr v-for="item in data.data" :key="item.id" style="cursor: pointer" @click="toggleSelected(item.id)">
                            <td scope="row" style="vertical-align: middle" class="text-center">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" :id="`check-${item.id}`" v-model="selected" :value="item.id">
                                    <label class="custom-control-label" :for="`check-${item.id}`">&nbsp;</label>
                                </div>
                            </td>
                            <td scope="row" style="vertical-align: middle">
                                {{ item.movement_order_id }}
                            </td>
                            <td scope="row" style="vertical-align: middle">
                                {{ item.movement_order.date }}
                            </td>
                            <td scope="row" style="vertical-align: middle" class="tx-bold">
                                {{ item.movement_order.reference }}
                            </td>
                            <td scope="row" style="vertical-align: middle" class="tx-bold">
                                {{ item.product.code }}
                            </td>
                            <td scope="row" style="vertical-align: middle">
                                {{ limitString(item.product.description_1, 40) }}
                            </td>
                            <td scope="row" style="vertical-align: middle" class="text-center">
                                {{ item.base_quantity }}
                            </td>
                            <td scope="row" style="vertical-align: middle" class="text-center">
                                {{ item.is_pick ? 'Pick' : 'Put' }}
                            </td>
                            <td scope="row" style="vertical-align: middle" class="tx-bold">
                                {{ item.location.code }}
                            </td>
                            <td scope="row" style="vertical-align: middle" class="text-center">
                                <span v-html="getMovementStatusLabel(item.status)"></span>
                            </td>
                        </tr>
                    </template>
                    <tr v-else>
                        <td colspan="10" class="text-center">No Data</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <Pagination :data="data"/>

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
        <div class="modal fade" id="process-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content tx-14">
                    <div class="modal-header">
                        <h6 class="modal-title" id="exampleModalLabel2">{{ processType }} Movement Order</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="mg-b-0">{{ processType }} {{ selected.length }} movement order(s)?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">No</button>
                        <button type="button" :class="`btn btn-${process == 1 ? 'success' : 'danger'} tx-13`" @click="submitProcess">Yes</button>
                    </div>
                </div>
            </div>
        </div>
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
            selected: [],
            selectAll: false,
            process: 1,
            processing: false
        }
    },
    watch: {
        selectAll(data, old) {
            if (old) {
                this.selected = [];
            } else {
                this.selected = this.data.data.map(i => i.id);
            }
        }
    },
    methods: {
        search() {
            this.form.post(this.$route('movement_orders.document.search'), {
                onBefore: () => $('#search-modal').modal('hide'),
                onError: () => $('#search-modal').modal('show')
            });
        },
        toggleSelected(id) {
            if (this.selected.includes(id)) {
                this.selected.forEach((s, i) => {
                    if (s == id) {
                        this.selected.splice(i, 1);
                    }
                });
            } else {
                this.selected.push(id);
            }

        },
        showProcessModal(process) {
            this.process = process;

            $('#process-modal').modal('show');
        },
        submitProcess() {
            const route = this.process == 1 ? this.$route('movement_orders.process.confirm') : this.$route('movement_orders.process.cancel');
            const data = {
                id: this.selected
            };

            this.$inertia.post(route, data, {
                onBefore: () => {
                    this.processing = true;

                    $('#process-modal').modal('hide');
                },
                onFinish: () => {
                    this.processing = false;
                }
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