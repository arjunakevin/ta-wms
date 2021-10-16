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
        </div>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <Link :href="$route('grs.show', good_receive)" class="nav-link">Good Receiving</Link>
            </li>
            <li class="nav-item">
                <a class="nav-link active">Item Check</a>
            </li>
        </ul>
        <div class="tab-content bd bd-gray-300 bd-t-0 pd-20" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                <div class="alert alert-danger" role="alert" v-if="!completed">
                    Item checking is not completed.
                </div>
                <div class="alert alert-success" role="alert" v-else>
                    Item checking is completed.
                </div>
                <form @submit.prevent="submit">
                    <div class="form-group row">
                        <label for="id" class="col-sm-2 col-form-label">Good Receive ID</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            <span class="tx-bold">{{ good_receive.id }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="reference" class="col-sm-2 col-form-label">Good Receive Reference</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            <span class="tx-bold">{{ good_receive.reference }}</span>
                        </div>
                    </div>
                    <div v-if="!completed">
                        <div class="form-group row">
                            <label for="product_code" class="col-sm-2 col-form-label">Product Code <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <vue-typeahead-bootstrap
                                    v-model="form.product_code"
                                    id="product_code"
                                    :data="products"
                                    placeholder="Product Code"
                                    :minMatchingChars="1"
                                    @input="searchProduct"
                                    :serializer="item => item.code"
                                    @hit="setProduct($event)"
                                />
                                <small class="text-danger" v-if="errors.product_id">{{ errors.product_id }}</small>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="product_code" class="col-sm-2 col-form-label">Check Quantity <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="number" v-model="form.base_quantity" class="form-control">
                                <small class="text-danger" v-if="errors.base_quantity">{{ errors.base_quantity }}</small>
                            </div>
                        </div>
                        <div class="text-right">
                            <button v-show="!form.processing" class="btn btn-sm pd-x-15 btn-success btn-uppercase mg-l-5" type="submit"><i data-feather="save" class="wd-10 mg-r-5"></i> Save</button>
                            <button v-show="form.processing" class="btn btn-sm pd-x-15 btn-success btn-uppercase mg-l-5" type="button" disabled>
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                <span class="sr-only">Loading...</span>
                            </button>
                        </div>
                    </div>
                    <hr>
                    <Table :data="details" :columnDefs="columnDefs" />
                    <Pagination :data="details"/>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue';
import Pagination from '../../Components/Pagination';
import Table from '../../Components/Table';
import { debounce } from 'lodash';

export default {
    props: ['good_receive', 'details', 'errors'],
    components: {
        Link,
        Pagination,
        Table
    },
    computed: {
        completed() {
            return this.good_receive.status == 3;
        }
    },
    data() {
        return {
            form: this.$inertia.form({
                product_code: '',
                base_quantity: 0
            }),
            selected: {},
            columnDefs: {
                header: [
                    { label: 'Line ID', style: 'width: 100px', class: 'text-center' },
                    { label: 'Product Code', style: 'width: 250px' },
                    { label: 'Description 1', style: 'width: 200px' },
                    { label: 'Base Quantity', style: 'width: 50px', class: 'text-center' },
                    { label: 'Check Quantity', style: 'width: 50px', class: 'text-center' },
                    { label: 'Remaining Quantity', style: 'width: 50px', class: 'text-center' },
                ],
                row: [
                    { data: 'inbound_delivery_detail.line_id', tdClass: 'text-center' },
                    { data: 'inbound_delivery_detail.product.code', spanClass: 'tx-bold' },
                    { data: 'inbound_delivery_detail.product.description_1', render: value => this.limitString(value, 40) },
                    { data: 'base_quantity', tdClass: 'text-center' },
                    { data: 'check_quantity', tdClass: 'text-center' },
                    { data: 'open_check_quantity', tdClass: 'text-center' },
                ]
            },
            products: []
        }
    },
    methods: {
        searchProduct: debounce(function() {
            if (this.form.product_code != this.selected.code) {
                const params = { client_id: this.good_receive.inbound_delivery.client_id, query: this.form.product_code };

                this.$axios.get(this.$route('products.list'), { params })
                    .then(data => {
                        this.products = data.data;
                    });
            }
        }, 500),
        setProduct(event) {
            this.selected = event;
            this.form.product_codecode = '';

            setTimeout(() => {
                this.form.product_codecode = event.code;
            }, 1);
        },
        submit() {
            this.form.post(this.$route('grs.check.submit', this.good_receive));
        }
    }
}
</script>