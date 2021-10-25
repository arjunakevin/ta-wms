<template>
    <div>
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                        <li class="breadcrumb-item"><a href="#">Inventory</a></li>
                        <li class="breadcrumb-item"><Link :href="$route('movement_orders.index')">Movement Order</Link></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ movement_order.reference }}</li>
                    </ol>
                </nav>
                <h4 class="mg-b-0 tx-spacing--1">{{ movement_order.reference }}</h4>
            </div>
        </div>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <Link :href="$route('movement_orders.show', movement_order)" class="nav-link">Detail</Link>
            </li>
            <li class="nav-item">
                <a class="nav-link active">Add to List</a>
            </li>
        </ul>
        <div class="tab-content bd bd-gray-300 bd-t-0 pd-20" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                <form @submit.prevent="submit">
                    <div class="form-group row">
                        <label for="id" class="col-sm-2 col-form-label">Movement ID</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            <span class="tx-bold">{{ movement_order.id }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="reference" class="col-sm-2 col-form-label">Reference</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            <span class="tx-bold">{{ movement_order.reference }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="date" class="col-sm-2 col-form-label">Movement Date</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            <span class="tx-bold">{{ movement_order.date }}</span>
                        </div>
                    </div>
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
                        <label for="product_code" class="col-sm-2 col-form-label">Base Quantity <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="number" v-model="form.base_quantity" class="form-control">
                            <small class="text-danger" v-if="errors.base_quantity">{{ errors.base_quantity }}</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="location_code" class="col-sm-2 col-form-label">Dest. Location Code <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <vue-typeahead-bootstrap
                                v-model="form.location_code"
                                id="location_code"
                                :data="locations"
                                placeholder="Location Code"
                                :minMatchingChars="1"
                                @input="searchLocation"
                                :serializer="item => item.code"
                                @hit="setLocation($event)"
                            />
                            <small class="text-danger" v-if="errors.location_id">{{ errors.location_id }}</small>
                        </div>
                    </div>
                    <div class="text-right">
                        <button v-show="!form.processing" class="btn btn-sm pd-x-15 btn-success btn-uppercase mg-l-5" type="submit"><i data-feather="save" class="wd-10 mg-r-5"></i> Add to Movement</button>
                        <button v-show="form.processing" class="btn btn-sm pd-x-15 btn-success btn-uppercase mg-l-5" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span class="sr-only">Loading...</span>
                        </button>
                    </div>
                    <hr>
                    <Table :data="details" :columnDefs="columnDefs" />
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue';
import Table from '../../Components/Table';
import { debounce } from 'lodash';

export default {
    props: ['movement_order', 'details', 'errors', 'putaway'],
    components: {
        Link,
        Table
    },
    computed: {
        columnDefs() {
            const data = {
                header: [
                    { label: 'Product Code', style: 'width: 250px' },
                    { label: 'Description 1', style: 'width: 200px' },
                    { label: 'Base Quantity', style: 'width: 50px', class: 'text-center' },
                    { label: 'Available Pick Quantity', style: 'width: 50px', class: 'text-center' },
                    { label: 'UoM Name', style: 'width: 50px' }
                ]
            };

            if (this.putaway) {
                data.row = [
                    { data: 'product.code', spanClass: 'tx-bold' },
                    { data: 'product.description_1', render: value => this.limitString(value, 40) },
                    { data: 'base_quantity', tdClass: 'text-center' },
                    { data: 'available_pick_quantity', tdClass: 'text-center' },
                    { data: 'product.uom_name' }
                ]
            } else {
                data.row = [
                    { data: 'outbound_delivery_detail.product.code', spanClass: 'tx-bold' },
                    { data: 'outbound_delivery_detail.product.description_1', render: value => this.limitString(value, 40) },
                    { data: 'base_quantity', tdClass: 'text-center' },
                    { data: 'open_pick_quantity', tdClass: 'text-center' },
                    { data: 'outbound_delivery_detail.product.uom_name' }
                ]
            }

            return data;
        }
    },
    data() {
        return {
            form: this.$inertia.form({
                product_code: '',
                base_quantity: 0,
                location_code: ''
            }),
            selected: {},
            selectedLocation: {},
            products: [],
            locations: []
        }
    },
    methods: {
        searchProduct: debounce(function() {
            if (this.form.product_code != this.selected.code) {
                const params = { query: this.form.product_code };

                this.$axios.get(this.$route('products.list'), { params })
                    .then(data => {
                        this.products = data.data;
                    });
            }
        }, 500),
        setProduct(event) {
            this.selected = event;
            this.form.product_code = '';

            setTimeout(() => {
                this.form.product_code = event.code;
            }, 1);
        },
        searchLocation: debounce(function() {
            if (this.form.location_code != this.selectedLocation.code) {
                const params = { query: this.form.location_code };

                this.$axios.get(this.$route('locations.list'), { params })
                    .then(data => {
                        this.locations = data.data;
                    });
            }
        }, 500),
        setLocation(event) {
            this.selectedLocation = event;
            this.form.location_code = '';

            setTimeout(() => {
                this.form.location_code = event.code;
            }, 1);
        },
        submit() {
            this.form.post(this.$route('movement_order_details.store', this.movement_order));
        }
    }
}
</script>