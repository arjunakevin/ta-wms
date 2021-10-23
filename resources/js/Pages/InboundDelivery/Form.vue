<template>
    <div>
        <form @submit.prevent="submit" autocomplete="off">
            <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                            <li class="breadcrumb-item"><a href="#">Inbound</a></li>
                            <li class="breadcrumb-item"><Link :href="$route('inbounds.index')">Inbound Delivery</Link></li>
                            <template v-if="inbound">
                                <li class="breadcrumb-item"><Link :href="$route('inbounds.show', inbound)">{{ inbound.reference }}</Link></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit</li>
                            </template>
                            <li v-else class="breadcrumb-item active" aria-current="page">Create</li>
                        </ol>
                    </nav>
                    <h4 class="mg-b-0 tx-spacing--1">{{ inbound ? inbound.reference : 'Create Inbound Delivery' }}</h4>
                </div>
                <div class="d-none d-md-block">
                    <button v-show="!editForm" class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-l-5" type="button" @click="editForm = true" :disabled="editing"><i data-feather="edit" class="wd-10 mg-r-5"></i> Edit</button>
                    <div v-show="editForm">
                        <button v-show="!form.processing" class="btn btn-sm pd-x-15 btn-success btn-uppercase mg-l-5" type="submit"><i data-feather="save" class="wd-10 mg-r-5"></i> Save</button>
                        <button v-show="form.processing" class="btn btn-sm pd-x-15 btn-success btn-uppercase mg-l-5" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span class="sr-only">Loading...</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title" v-if="inbound">Edit Inbound Delivery Detail (<span class="tx-bold">{{ inbound.reference }}</span>)</h5>
                    <h5 class="card-title" v-else>Inbound Delivery Detail</h5>
                    <hr>
                    <div>
                        <div class="form-group row" v-if="inbound">
                            <label for="id" class="col-sm-2 col-form-label">ID</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="id" placeholder="ID" v-model="inbound.id" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="reference" class="col-sm-2 col-form-label">Inbound Reference <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="reference" placeholder="Inbound Reference" v-model="form.reference" :disabled="!editForm">
                                <small class="text-danger" v-if="errors.reference">{{ errors.reference }}</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="client_code" class="col-sm-2 col-form-label">Client Code <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <vue-typeahead-bootstrap
                                    v-model="form.client_code"
                                    id="client_code"
                                    :data="clients"
                                    placeholder="Client Code"
                                    :minMatchingChars="1"
                                    @input="searchClient"
                                    :serializer="item => `${item.name} (${item.code})`"
                                    @hit="setClient($event)"
                                    :disabled="!editForm"
                                />
                                <small class="text-danger" v-if="errors.client_code">{{ errors.client_code }}</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="client_code" class="col-sm-2 col-form-label">Client Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="client_code" placeholder="Client Name" v-model="selected_client.name" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="arrival_date" class="col-sm-2 col-form-label">Arrival Date</label>
                            <div class="col-sm-10">
                                <input type="datetime-local" class="form-control" id="arrival_date" placeholder="Arrival Date" v-model="form.arrival_date" :disabled="!editForm">
                                <small class="text-danger" v-if="errors.arrival_date">{{ errors.arrival_date }}</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="po_date" class="col-sm-2 col-form-label">PO Date</label>
                            <div class="col-sm-10">
                                <input type="datetime-local" class="form-control" id="po_date" placeholder="PO Date" v-model="form.po_date" :disabled="!editForm">
                                <small class="text-danger" v-if="errors.po_date">{{ errors.po_date }}</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="notes" class="col-sm-2 col-form-label">Notes</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" rows="3" id="notes" placeholder="Notes" v-model="form.notes" :disabled="!editForm"></textarea>
                                <small class="text-danger" v-if="errors.notes">{{ errors.notes }}</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="status" class="col-sm-2 col-form-label">Status</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="status" placeholder="Status" v-model="status" disabled>
                            </div>
                        </div>
                    </div>
                    <div v-if="inbound">
                        <hr>
                        <button @click="addDetail" class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-b-15" type="button" :disabled="disableAddDetailButton"><i data-feather="plus" class="wd-10 mg-r-5"></i> Add Detail</button>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" style="width: 200px" class="text-center">
                                            Line ID
                                        </th>
                                        <th scope="col" style="width: 250px">
                                            Product Code
                                        </th>
                                        <th scope="col">
                                            Description 1
                                        </th>
                                        <th scope="col" style="width: 150px" class="text-center">
                                            Base Quantity
                                        </th>
                                        <th scope="col" style="width: 150px" class="text-center">
                                            Open Quantity
                                        </th>
                                        <th scope="col" style="width: 100px" class="text-center">
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template v-for="(detail, i) in form.details">
                                        <tr :key="`line-${i}`">
                                            <td scope="row" style="vertical-align: middle">
                                                <input type="text" class="form-control" v-model="detail.line_id" placeholder="Line ID" :disabled="!detail.editing" @keyup.enter="saveDetail(detail, i)">
                                            </td>
                                            <td scope="row" style="vertical-align: middle">
                                                <vue-typeahead-bootstrap
                                                    v-model="detail.code"
                                                    :data="products"
                                                    placeholder="Product Code"
                                                    :minMatchingChars="1"
                                                    @input="searchProduct(detail)"
                                                    :serializer="item => item.code"
                                                    @hit="setProduct($event, detail)"
                                                    :disabled="!detail.editing"
                                                    @keyup.enter="saveDetail(detail, i)"
                                                />
                                            </td>
                                            <td scope="row" style="vertical-align: middle">
                                                <input type="text" class="form-control" v-model="detail.selected.description_1" placeholder="Description 1" disabled>
                                            </td>
                                            <td scope="row" style="vertical-align: middle" class="text-center">
                                                <input type="number" class="form-control" v-model="detail.base_quantity" placeholder="Base Quantity" @input="setOpenQuantity(detail)" :disabled="!detail.selected.id || !detail.editing" @keyup.enter="saveDetail(detail, i)">
                                            </td>
                                            <td scope="row" style="vertical-align: middle" class="text-center">
                                                <input type="number" class="form-control" v-model="detail.open_quantity" placeholder="Open Quantity" disabled>
                                            </td>
                                            <td scope="row" style="vertical-align: middle" class="text-center">
                                                <div class="d-flex">
                                                    <button type="button" class="btn btn-success btn-icon mg-r-5" @click="saveDetail(detail, i)" v-show="detail.editing && !detail.submitting" :disabled="editing && !detail.editing">
                                                        <i data-feather="check"></i>
                                                    </button>
                                                    <button v-show="detail.editing && detail.submitting" class="btn btn-success btn-icon mg-r-5" type="button" disabled>
                                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                        <span class="sr-only">Loading...</span>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-icon" @click="cancelEdit(detail)" v-show="detail.editing && detail.id" :disabled="editing && !detail.editing">
                                                        <i data-feather="x"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-primary btn-icon mg-r-5" @click="editDetail(detail, i)" v-show="!detail.editing" :disabled="editForm || (editing && !detail.editing)">
                                                        <i data-feather="edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-icon" @click="showDeleteDetailModal(detail, i)" :disabled="editForm || (editing && !detail.editing)" v-show="!detail.deleting && (!detail.editing || !detail.id)">
                                                        <i data-feather="trash"></i>
                                                    </button>
                                                    <button v-show="detail.deleting" class="btn btn-danger btn-icon" type="button" disabled>
                                                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                        <span class="sr-only">Loading...</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr :key="`errors-${i}`" v-if="hasDetailError && detailFormIndex == i">
                                            <td scope="row" style="vertical-align: middle">
                                                <small class="text-danger text-left" v-if="errors.line_id">{{ errors.line_id }}</small>
                                            </td>
                                            <td scope="row" style="vertical-align: middle">
                                                <small class="text-danger text-left" v-if="errors.product_id">{{ errors.product_id }}</small>
                                            </td>
                                            <td scope="row" style="vertical-align: middle">
                                            </td>
                                            <td scope="row" style="vertical-align: middle">
                                                <small class="text-danger text-left" v-if="errors.base_quantity">{{ errors.base_quantity }}</small>
                                            </td>
                                            <td scope="row" style="vertical-align: middle">
                                                <small class="text-danger text-left" v-if="errors.open_quantity">{{ errors.open_quantity }}</small>
                                            </td>
                                            <td scope="row" style="vertical-align: middle">
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="modal fade" id="delete-detail-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content tx-14">
                    <div class="modal-header">
                        <h6 class="modal-title" id="exampleModalLabel2">Delete Inbound Detail (Line: {{ deleteDetailData.line_id }})</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="mg-b-0">Deleted data can't be recovered.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary tx-13" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger tx-13" @click="deleteDetail">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue';
import { debounce } from 'lodash';

export default {
    props: ['inbound', 'details', 'errors'],
    components: {
        Link
    },
    data() {
        return {
            form: this.$inertia.form({
                reference: '',
                client_code: '',
                arrival_date: '',
                po_date: '',
                notes: '',
                status: this.getInboundStatus(1),
                details: []
            }),
            clients: [],
            selected_client: {},
            products: [],
            editForm: true,
            editing: false,
            detailFormIndex: null,
            deleteDetailData: {},
            deleteDetailIndex: null
        }
    },
    computed: {
        status() {
            const status = this.inbound ? this.inbound.status : 1;

            return this.getInboundStatus(status);
        },
        disableAddDetailButton() {
            return this.editForm || this.editing;
        },
        hasDetailError() {
            const keys = ['line_id', 'product_id', 'base_quantity', 'open_quantity'];

            for (const key in this.errors) {
                if (keys.includes(key)) {
                    return true;
                }
            }

            return false;
        }
    },
    methods: {
        submit() {
            const options = {
                onSuccess: () => this.editForm = false
            };

            if (this.inbound) {
                this.form.put(this.$route('inbounds.update', this.inbound), options);
            } else {
                this.form.post(this.$route('inbounds.store'), options);
            }
        },
        searchClient: debounce(function() {
            if (this.selected_client.code != this.form.client_code) {
                const params = { query: this.form.client_code };
                this.$axios.get(this.$route('clients.list'), { params })
                    .then(data => {
                        this.clients = data.data;
                    });
            }
        }, 500),
        setClient(event) {
            this.selected_client = event;
            this.form.client_code = '';

            setTimeout(() => {
                this.form.client_code = event.code;
            }, 1);
        },
        searchProduct: debounce(function(product) {
            if (product.code != product.selected.code) {
                const params = { client_id: this.selected_client.id, query: product.code };

                this.$axios.get(this.$route('products.list'), { params })
                    .then(data => {
                        this.products = data.data;
                    });
            }
        }, 500),
        setProduct(event, product) {
            product.selected = event;
            product.code = '';

            setTimeout(() => {
                product.code = event.code;
            }, 1);
        },
        addDetail() {
            this.detailFormIndex = null;

            this.editing = true;

            this.form.details.push({
                id: null,
                line_id: '',
                code: '',
                base_quantity: 0,
                open_quantity: 0,
                selected: {},
                editing: true,
                submitting: false,
                deleting: false
            });

            setTimeout(() => {
                feather.replace();
            }, 1);
        },
        saveDetail(data, index) {
            data.submitting = true;

            const formData = {
                ...data,
                inbound_delivery_id: this.inbound.id
            };

            const options = {
                onBefore: () => data.submitting = true,
                onSuccess: res => {
                    data.editing = false;
                    data.submitting = false;
                    this.editing = false;
                    this.detailFormIndex = null;

                    this.form.details = this.mapDetails(res.props.details);
                },
                onError: () => {
                    data.submitting = false;
                    this.detailFormIndex = index;
                }
            };

            if (data.id) {
                this.$inertia.put(this.$route('inbound_details.update', data.id), formData, options);
            } else {
                this.$inertia.post(this.$route('inbound_details.store'), formData, options);
            }
        },
        cancelEdit(data) {
            for (const key in data) {
                if (key == 'old') continue;

                data[key] = data.old[key];
            }

            this.editing = false;
        },
        editDetail(data, index) {
            this.editing = true;
            data.editing = true;

            this.detailFormIndex = index;

            setTimeout(() => {
                feather.replace();
            }, 1000);
        },
        showDeleteDetailModal(data, index) {
            $('#delete-detail-modal').modal('show');

            this.deleteDetailData = data;
            this.deleteDetailIndex = index;
        },
        deleteDetail() {
            const options = {
                onBefore: () => {
                    $('#delete-detail-modal').modal('hide');
                    this.deleteDetailData.deleting = true;
                },
                onSuccess: () => {
                    this.form.details.splice(this.deleteDetailIndex, 1);
                    this.editing = false;
                    this.detailFormIndex = null;
                },
                onError: () => {
                    this.deleteDetailData.deleting = false;
                    this.detailFormIndex = this.deleteDetailIndex;
                }
            };
            
            if (this.deleteDetailData.id) {
                this.$inertia.delete(this.$route('inbound_details.destroy', this.deleteDetailData.id), options);
            } else {
                options.preserveState = false;
                options.resetOnSuccess = false;

                this.$inertia.reload(options);
            }
        },
        setOpenQuantity(product) {
            if (product.id) {
                product.open_quantity = (product.base_quantity - product.original_base_quantity) + product.original_open_quantity;
            } else {
                product.open_quantity = product.base_quantity;
            }
        },
        mapDetails(details) {
            return details.map(detail => {
                const data = {
                    id: detail.id,
                    line_id: detail.line_id,
                    code: detail.product.code,
                    base_quantity: detail.base_quantity,
                    original_base_quantity: detail.base_quantity,
                    open_quantity: detail.open_quantity,
                    original_open_quantity: detail.open_quantity,
                    selected: detail.product,
                    editing: false,
                    submitting: false,
                    deleting: false
                };

                return { ...data, old: data };
            })
        }
    },
    created() {
        if (this.inbound) {
            this.form = this.$inertia.form({
                reference: this.inbound.reference,
                client_code: this.inbound.client.code,
                arrival_date: this.inbound.arrival_date_formatted,
                po_date: this.inbound.po_date_formatted,
                notes: this.inbound.notes,
                status: this.getInboundStatus(this.inbound.status),
                details: this.mapDetails(this.details)
            });

            this.selected_client = this.inbound.client;
            this.clients.push(this.selected_client);
            this.editForm = false;
        }
    }
}
</script>