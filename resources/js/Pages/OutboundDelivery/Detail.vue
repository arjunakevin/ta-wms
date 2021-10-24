<template>
    <div>
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                        <li class="breadcrumb-item"><a href="#">Outbound</a></li>
                        <li class="breadcrumb-item"><Link :href="$route('outbounds.index')">Outbound Delivery</Link></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ outbound.reference }}</li>
                    </ol>
                </nav>
                <h4 class="mg-b-0 tx-spacing--1">{{ outbound.reference }}</h4>
            </div>
            <div class="d-none d-md-block">
                <Link :href="$route('outbounds.edit', outbound)" as="button" type="button" class="btn btn-sm pd-x-15 btn-primary btn-uppercase mg-l-5"><i data-feather="edit" class="wd-10 mg-r-5"></i> Edit</Link>
                <a v-show="!deleting" href="#delete-modal" data-toggle="modal" class="btn btn-sm pd-x-15 btn-danger btn-uppercase"><i data-feather="trash" class="wd-10 mg-r-5"></i> Delete</a>
                <button v-show="deleting" class="btn btn-sm pd-x-15 btn-danger btn-uppercase" type="button" disabled>
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span class="sr-only">Loading...</span>
                </button>
            </div>
        </div>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active">Outbound Delivery</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">Delivery Order</a>
            </li>
        </ul>
        <div class="tab-content bd bd-gray-300 bd-t-0 pd-20" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                <form>
                    <div class="alert alert-danger" role="alert" v-if="errors.message">
                        {{ errors.message }}
                    </div>
                    <div class="form-group row">
                        <label for="id" class="col-sm-2 col-form-label">Outbound ID</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            <span class="tx-bold">{{ outbound.id }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="reference" class="col-sm-2 col-form-label">Outbound Reference</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            <span class="tx-bold">{{ outbound.reference }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="client_code" class="col-sm-2 col-form-label">Client</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            <span>{{ outbound.client.name }} (<span class="tx-bold">{{ outbound.client.code }}</span>)</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 col-form-label">Destination Name</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            <span class="tx-bold">{{ outbound.destination_name }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 col-form-label">Destination Phone</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            <span class="tx-bold">{{ outbound.destination_phone }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="address_1" class="col-sm-2 col-form-label">Destination Address 1</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            <span class="tx-bold">{{ outbound.destination_address_1 }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="address_2" class="col-sm-2 col-form-label">Destination Address 2</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            <span class="tx-bold">{{ outbound.destination_address_2 || '-' }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="req_del_date" class="col-sm-2 col-form-label">Request Delivery Date</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            <span class="tx-bold">{{ outbound.request_delivery_date }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="po_reference" class="col-sm-2 col-form-label">PO Reference</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            <span class="tx-bold">{{ outbound.po_reference || '-' }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="notes" class="col-sm-2 col-form-label">Notes</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            <span class="tx-bold">{{ outbound.notes || '-' }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 pt-0">Status</label>
                        <div class="col-sm-10" v-html="getOutboundStatusLabel(outbound.status)">
                        </div>
                    </div>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 100px" class="text-center">
                                        Line ID
                                    </th>
                                    <th scope="col" style="width: 200px">
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
                                    <th scope="col" style="width: 150px">
                                        UoM Name
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="detail in details" :key="`line-${detail.line_id}`">
                                    <td scope="row" style="vertical-align: middle" class="text-center">
                                        <span class="tx-bold">
                                            {{ detail.line_id }}
                                        </span>
                                    </td>
                                    <td scope="row" style="vertical-align: middle">
                                        <span class="tx-bold">
                                            {{ detail.product.code }}
                                        </span>
                                    </td>
                                    <td scope="row" style="vertical-align: middle">
                                        {{ limitString(detail.product.description_1, 70) }}
                                    </td>
                                    <td scope="row" style="vertical-align: middle" class="text-center">
                                        {{ detail.base_quantity }}
                                    </td>
                                    <td scope="row" style="vertical-align: middle" class="text-center">
                                        {{ detail.open_quantity }}
                                    </td>
                                    <td scope="row" style="vertical-align: middle">
                                        {{ detail.product.uom_name }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content tx-14">
                    <div class="modal-header">
                        <h6 class="modal-title" id="exampleModalLabel2">Delete Outbound Delivery</h6>
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
    </div>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue';

export default {
    props: ['outbound', 'details', 'errors'],
    components: {
        Link
    },
    data() {
        return {
            deleting: false
        }
    },
    methods: {
        submitDeletion() {
            this.$inertia.delete(this.$route('outbounds.destroy', this.outbound), {
                onBefore: () => {
                    this.deleting = true;

                    $('#delete-modal').modal('hide');
                },
                onFinish: () => this.deleting = false
            });
        }
    }
}
</script>