<template>
    <form @submit.prevent="submit" autocomplete="off">
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                        <li class="breadcrumb-item"><a href="#">Master Data</a></li>
                        <li class="breadcrumb-item"><Link :href="$route('products.index')">Product</Link></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ product ? product.code : 'Create' }}</li>
                    </ol>
                </nav>
                <h4 class="mg-b-0 tx-spacing--1">{{ product ? product.code : 'Create Product' }}</h4>
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
                <h5 class="card-title" v-if="product">Edit Product Detail (<span class="tx-bold">{{ product.code }}</span>)</h5>
                <h5 class="card-title" v-else>Product Detail</h5>
                <hr>
                <div>
                    <div class="form-group row" v-if="product">
                        <label for="id" class="col-sm-2 col-form-label">ID</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="id" placeholder="ID" v-model="product.id" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="code" class="col-sm-2 col-form-label">Product Code <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="code" placeholder="Product Code" v-model="form.code">
                            <small class="text-danger" v-if="errors.code">{{ errors.code }}</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="barcode" class="col-sm-2 col-form-label">Product Barcode</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="barcode" placeholder="Product Barcode" v-model="form.barcode">
                            <small class="text-danger" v-if="errors.barcode">{{ errors.barcode }}</small>
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
                            />
                            <small class="text-danger" v-if="errors.client_id">{{ errors.client_id }}</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="client_code" class="col-sm-2 col-form-label">Client Name</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="client_code" placeholder="Client Name" v-model="selected_client.name" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="description_1" class="col-sm-2 col-form-label">Description 1 <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <textarea class="form-control" rows="3" id="description_1" placeholder="Description 1" v-model="form.description_1"></textarea>
                            <small class="text-danger" v-if="errors.description_1">{{ errors.description_1 }}</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="description_2" class="col-sm-2 col-form-label">Description 2</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" rows="3" id="description_2" placeholder="Description 2" v-model="form.description_2"></textarea>
                            <small class="text-danger" v-if="errors.description_2">{{ errors.description_2 }}</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="uom_name" class="col-sm-2 col-form-label">UoM Name <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="uom_name" placeholder="UoM Name" v-model="form.uom_name">
                            <small class="text-danger" v-if="errors.uom_name">{{ errors.uom_name }}</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 pt-0">Status <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <div class="custom-control custom-radio">
                                <input type="radio" name="status" class="custom-control-input" v-model="form.is_active" value="1" id="status_active">
                                <label class="custom-control-label" for="status_active">Active</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" name="status" class="custom-control-input" v-model="form.is_active" value="0" id="status_inactive">
                                <label class="custom-control-label" for="status_inactive">Inactive</label>
                            </div>
                            <small class="text-danger" v-if="errors.is_active">{{ errors.is_active }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue';
import { debounce } from 'lodash';

export default {
    props: ['product', 'errors'],
    components: {
        Link
    },
    data() {
        return {
            form: this.$inertia.form({
                code: '',
                barcode: '',
                client_code: '',
                description_1: '',
                description_2: '',
                uom_name: '',
                is_active: 1
            }),
            clients: [],
            selected_client: {}
        }
    },
    methods: {
        submit() {
            if (this.product) {
                this.form.put(this.$route('products.update', this.product))
            } else {
                this.form.post(this.$route('products.store'))
            }
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
            this.form.client_code = event.code;
        }
    },
    created() {
        if (this.product) {
            this.form = this.$inertia.form({
                code: this.product.code,
                barcode: this.product.barcode,
                client_code: this.product.client.code,
                description_1: this.product.description_1,
                description_2: this.product.description_2,
                is_active: this.product.is_active
            });

            this.selected_client = this.product.client;
        }
    }
}
</script>