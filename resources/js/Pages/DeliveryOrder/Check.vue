<template>
    <div>
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                        <li class="breadcrumb-item"><a href="#">Outbound</a></li>
                        <li class="breadcrumb-item"><Link :href="$route('delivery_orders.index')">Delivery Order</Link></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ delivery_order.reference }}</li>
                    </ol>
                </nav>
                <h4 class="mg-b-0 tx-spacing--1">{{ delivery_order.reference }}</h4>
            </div>
        </div>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <Link :href="$route('delivery_orders.show', delivery_order)" class="nav-link">Delivery Order</Link>
            </li>
            <li class="nav-item">
                <a class="nav-link active">Item Check</a>
            </li>
        </ul>
        <div class="tab-content bd bd-gray-300 bd-t-0 pd-20" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                <div class="alert alert-success" role="alert" v-if="completed">
                    Item checking is completed.
                </div>
                <div class="alert alert-danger" role="alert" v-else-if="!pick_completed">
                    Delivery order is not fully picked.
                </div>
                <div class="alert alert-danger" role="alert" v-else-if="!completed">
                    Item checking is not completed.
                </div>
                <form @submit.prevent="submit">
                    <div class="form-group row">
                        <label for="id" class="col-sm-2 col-form-label">Delivery Order ID</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            <span class="tx-bold">{{ delivery_order.id }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="reference" class="col-sm-2 col-form-label">Delivery Order Reference</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            <span class="tx-bold">{{ delivery_order.reference }}</span>
                        </div>
                    </div>
                    <div v-if="!completed && pick_completed">
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
    props: ['delivery_order', 'details', 'errors', 'pick_completed'],
    components: {
        Link,
        Pagination,
        Table
    },
    computed: {
        completed() {
            return this.delivery_order.status >= 5 || this.delivery_order.status < 0; 
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
                    { label: 'UoM Name', style: 'width: 50px' }
                ],
                row: [
                    { data: 'outbound_delivery_detail.line_id', tdClass: 'text-center' },
                    { data: 'outbound_delivery_detail.product.code', spanClass: 'tx-bold' },
                    { data: 'outbound_delivery_detail.product.description_1', render: value => this.limitString(value, 40) },
                    { data: 'base_quantity', tdClass: 'text-center' },
                    { data: 'check_quantity', tdClass: 'text-center' },
                    { data: 'open_check_quantity', tdClass: 'text-center' },
                    { data: 'outbound_delivery_detail.product.uom_name' }
                ]
            },
            products: []
        }
    },
    methods: {
        searchProduct: debounce(function() {
            if (this.form.product_code != this.selected.code) {
                const params = { client_id: this.delivery_order.outbound_delivery.client_id, query: this.form.product_code };

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
            this.form.post(this.$route('delivery_orders.check.submit', this.delivery_order));
        }
    }
}
</script>