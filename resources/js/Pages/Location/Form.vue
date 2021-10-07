<template>
    <form @submit.prevent="submit" autocomplete="off">
        <div class="d-sm-flex align-items-center justify-content-between mg-b-20 mg-lg-b-25 mg-xl-b-30">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style1 mg-b-10">
                        <li class="breadcrumb-item"><a href="#">Master Data</a></li>
                        <li class="breadcrumb-item"><Link :href="$route('locations.index')">Location</Link></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ location ? location.code : 'Create' }}</li>
                    </ol>
                </nav>
                <h4 class="mg-b-0 tx-spacing--1">{{ location ? location.code : 'Create Location' }}</h4>
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
                <h5 class="card-title" v-if="location">Edit Location Detail (<span class="tx-bold">{{ location.code }}</span>)</h5>
                <h5 class="card-title" v-else>Location Detail</h5>
                <hr>
                <div>
                    <div class="form-group row" v-if="location">
                        <label for="id" class="col-sm-2 col-form-label">ID</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="id" placeholder="ID" v-model="location.id" disabled>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="code" class="col-sm-2 col-form-label">Location Code <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="code" placeholder="Location Code" v-model="form.code">
                            <small class="text-danger" v-if="errors.code">{{ errors.code }}</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 pt-0">Pick Blocked <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <div class="custom-control custom-radio">
                                <input type="radio" name="pick_blocked" class="custom-control-input" v-model="form.pick_blocked" value="0" id="pick_status_open">
                                <label class="custom-control-label" for="pick_status_open">No</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" name="pick_blocked" class="custom-control-input" v-model="form.pick_blocked" value="1" id="pick_status_blocked">
                                <label class="custom-control-label" for="pick_status_blocked">Yes</label>
                            </div>
                            <small class="text-danger" v-if="errors.pick_blocked">{{ errors.pick_blocked }}</small>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-sm-2 pt-0">Pick Blocked <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <div class="custom-control custom-radio">
                                <input type="radio" name="put_blocked" class="custom-control-input" v-model="form.put_blocked" value="0" id="put_status_open">
                                <label class="custom-control-label" for="put_status_open">No</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" name="put_blocked" class="custom-control-input" v-model="form.put_blocked" value="1" id="put_status_blocked">
                                <label class="custom-control-label" for="put_status_blocked">Yes</label>
                            </div>
                            <small class="text-danger" v-if="errors.put_blocked">{{ errors.put_blocked }}</small>
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
    props: ['location', 'errors'],
    components: {
        Link
    },
    data() {
        return {
            form: this.$inertia.form({
                code: '',
                pick_blocked: 0,
                put_blocked: 0
            })
        }
    },
    methods: {
        submit() {
            if (this.location) {
                this.form.put(this.$route('locations.update', this.location))
            } else {
                this.form.post(this.$route('locations.store'))
            }
        }
    },
    created() {
        if (this.location) {
            this.form = this.$inertia.form({
                code: this.location.code,
                pick_blocked: this.location.pick_blocked,
                put_blocked: this.location.put_blocked
            });
        }
    }
}
</script>