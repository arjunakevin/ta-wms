<template>
    <form @submit.prevent="submit" autocomplete="off">
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                        <li class="breadcrumb-item"><a href="#">Master Data</a></li>
                        <li class="breadcrumb-item"><Link :href="$route('users.index')">User</Link></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ user ? user.name : 'Create' }}</li>
                    </ol>
                </nav>
                <h4 class="mg-b-0 tx-spacing--1">{{ user ? user.name : 'Create User' }}</h4>
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
                <h5 class="card-title" v-if="user">Edit User Detail (<span class="tx-bold">{{ user.name }}</span>)</h5>
                <h5 class="card-title" v-else>User Detail</h5>
                <hr>
                <div>
                    <div class="form-group row" v-if="user">
                        <label for="id" class="col-sm-2 col-form-label">ID</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="id" placeholder="ID" v-model="user.id" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 col-form-label">Name <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="name" placeholder="Name" v-model="form.name">
                            <small class="text-danger" v-if="errors.name">{{ errors.name }}</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="username" class="col-sm-2 col-form-label">Username <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="username" placeholder="Username" v-model="form.username">
                            <small class="text-danger" v-if="errors.username">{{ errors.username }}</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="email" class="col-sm-2 col-form-label">Email <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="email" placeholder="Email" v-model="form.email">
                            <small class="text-danger" v-if="errors.email">{{ errors.email }}</small>
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
                    <div class="form-group row" v-if="!user">
                        <label for="password" class="col-sm-2 col-form-label">Password <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="password" class="form-control" id="password" placeholder="Password" v-model="form.password">
                            <small class="text-danger" v-if="errors.password">{{ errors.password }}</small>
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
    props: ['user', 'errors'],
    components: {
        Link
    },
    data() {
        return {
            form: this.$inertia.form({
                name: '',
                username: '',
                email: '',
                password: ''
            }),
            clients: [],
            selected_client: {}
        }
    },
    methods: {
        submit() {
            if (this.user) {
                this.form.put(this.$route('users.update', this.user))
            } else {
                this.form.post(this.$route('users.store'))
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
        if (this.user) {
            this.form = this.$inertia.form({
                name: this.user.name,
                username: this.user.username,
                client_code: this.user.client.code,
                email: this.user.email
            });

            this.selected_client = this.user.client;
        }
    }
}
</script>