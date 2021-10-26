<template>
    <div>
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                        <li class="breadcrumb-item"><a href="#">Master Data</a></li>
                        <li class="breadcrumb-item"><Link :href="$route('users.index')">User</Link></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ user.name }}</li>
                    </ol>
                </nav>
                <h4 class="mg-b-0 tx-spacing--1">{{ user.name }}</h4>
            </div>
            <div class="d-none d-md-block">
                <Link :href="$route('users.edit', user)" as="button" type="button" class="btn btn-sm pd-x-15 btn-white btn-uppercase mg-l-5"><i data-feather="edit" class="wd-10 mg-r-5"></i> Edit</Link>
                <a v-show="!deleting" href="#delete-modal" data-toggle="modal" class="btn btn-sm pd-x-15 btn-white btn-uppercase"><i data-feather="trash" class="wd-10 mg-r-5"></i> Delete</a>
                <button v-show="deleting" class="btn btn-sm pd-x-15 btn-white btn-uppercase" type="button" disabled>
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    <span class="sr-only">Loading...</span>
                </button>
            </div>
        </div>
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Detail</a>
            </li>
        </ul>
        <div class="tab-content bd bd-gray-300 bd-t-0 pd-20" id="myTabContent">
            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                <form>
                    <div class="form-group row">
                        <label for="id" class="col-sm-2 col-form-label">User ID</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            <span class="tx-bold">{{ user.id }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="code" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            <span class="tx-bold">{{ user.name }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="barcode" class="col-sm-2 col-form-label">Username</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            <span>{{ user.username }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="barcode" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            <span>{{ user.email }}</span>
                        </div>
                    </div>
                    <div class="form-group row" v-if="user.client.code">
                        <label for="client_code" class="col-sm-2 col-form-label">Client</label>
                        <div class="col-sm-10 d-flex align-items-center">
                            <span>{{ user.client.name }} (<span class="tx-bold">{{ user.client.code }}</span>)</span>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel2" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content tx-14">
                    <div class="modal-header">
                        <h6 class="modal-title" id="exampleModalLabel2">Delete User</h6>
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
    props: ['user'],
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
            this.$inertia.delete(this.$route('users.destroy', this.user), {
                onBefore: () => {
                    this.deleting = true;

                    $('#delete-modal').modal('hide');
                }
            });
        }
    }
}
</script>