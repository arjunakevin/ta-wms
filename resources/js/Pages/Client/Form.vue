<template>
    <form @submit.prevent="submit" autocomplete="off">
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                        <li class="breadcrumb-item"><a href="#">Master Data</a></li>
                        <li class="breadcrumb-item"><Link :href="$route('clients.index')">Client</Link></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ client ? client.code : 'Create' }}</li>
                    </ol>
                </nav>
                <h4 class="mg-b-0 tx-spacing--1">{{ client ? client.code : 'Create Client' }}</h4>
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
                <h5 class="card-title" v-if="client">Edit Client Detail (<span class="tx-bold">{{ client.code }}</span>)</h5>
                <h5 class="card-title" v-else>Client Detail</h5>
                <hr>
                <div>
                    <div class="form-group row" v-if="client">
                        <label for="id" class="col-sm-2 col-form-label">ID</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="id" placeholder="ID" v-model="client.id" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="code" class="col-sm-2 col-form-label">Client Code <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="code" placeholder="Client Code" v-model="form.code">
                            <small class="text-danger" v-if="errors.code">{{ errors.code }}</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 col-form-label">Client Name <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="name" placeholder="Client Name" v-model="form.name">
                            <small class="text-danger" v-if="errors.name">{{ errors.name }}</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="address_1" class="col-sm-2 col-form-label">Address 1 <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <textarea class="form-control" rows="3" id="address_1" placeholder="Address 1" v-model="form.address_1"></textarea>
                            <small class="text-danger" v-if="errors.address_1">{{ errors.address_1 }}</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="address_2" class="col-sm-2 col-form-label">Address 2</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" rows="3" id="address_2" placeholder="Address 2" v-model="form.address_2"></textarea>
                            <small class="text-danger" v-if="errors.address_2">{{ errors.address_2 }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<script>
import { Link } from '@inertiajs/inertia-vue';

export default {
    props: ['client', 'errors'],
    components: {
        Link
    },
    data() {
        return {
            form: this.$inertia.form({
                code: '',
                name: '',
                address_1: '',
                address_2: ''
            })
        }
    },
    methods: {
        submit() {
            if (this.client) {
                this.form.put(this.$route('clients.update', this.client))
            } else {
                this.form.post(this.$route('clients.store'))
            }
        }
    },
    created() {
        if (this.client) {
            this.form = this.$inertia.form({
                code: this.client.code,
                name: this.client.name,
                address_1: this.client.address_1,
                address_2: this.client.address_2
            });
        }
    }
}
</script>