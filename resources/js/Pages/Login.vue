<template>
    <div class="content content-auth center-vd">
        <div class="center">
            <div class="media align-items-stretch justify-content-center ht-100p">
                <form @submit.prevent="submit" class="sign-wrapper mg-lg-r-50 mg-xl-r-60">
                    <div class="pd-t-20 wd-100p">
                        <h4 class="tx-color-01 mg-b-5">Sign In</h4>
                        <p class="tx-color-03 tx-16 mg-b-40">Welcome back! Please signin to continue.</p>
                        <div class="alert alert-danger" role="alert" v-if="errors.auth">
                            {{ errors.auth }}
                        </div>
                        <div class="form-group">
                            <label>Username / Email Address <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="Enter your email address" v-model="form.username">
                            <small class="text-danger" v-if="errors.username">
                                {{ errors.username }}
                            </small>
                        </div>
                        <div class="form-group">
                            <div class="d-flex justify-content-between mg-b-5">
                                <label class="mg-b-0-f">Password <span class="text-danger">*</span></label>
                            </div>
                            <input type="password" class="form-control" placeholder="Enter your password" v-model="form.password">
                            <small class="text-danger" v-if="errors.password">
                                {{ errors.password }}
                            </small>
                        </div>
                        <button v-show="!form.processing" class="btn btn-primary btn-block">Sign In</button>
                        <button v-show="form.processing" class="btn btn-primary btn-block" type="button" disabled>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            <span class="sr-only">Loading...</span>
                        </button>
                    </div>
                </form><!-- sign-wrapper -->
                <div class="media-body pd-y-30 pd-lg-x-50 pd-xl-x-60 align-items-center d-none d-lg-flex pos-relative">
                    <div class="mx-lg-wd-500 mx-xl-wd-550">
                        <img src="https://ouch-cdn2.icons8.com/YkI47wHdshFpsZZvcAHqQHM-SCOT6nyOjSkxVPQKZy0/rs:fit:1621:912/czM6Ly9pY29uczgu/b3VjaC1wcm9kLmFz/c2V0cy9zdmcvMTU5/L2VhNzA0MTYyLWM2/YWYtNDIzNC1iN2M1/LWE3MzM1ZTQ1NmMx/MS5zdmc.png" class="img-fluid" alt="">
                    </div>
                </div><!-- media-body -->
            </div><!-- media -->
        </div><!-- container -->
    </div><!-- content -->
</template>

<script>
import Empty from '../Pages/Layouts/Empty.vue';

export default {
    props: ['errors'],
    layout: Empty,
    data() {
        return {
            form: this.$inertia.form({
                username: '',
                password: ''
            })
        };
    },
    methods: {
        submit() {
            this.form.post(route('login.attempt'));
        }
    }
}
</script>

<style scoped>
@media (min-width: 1200px) {
    .center-vd {
        position: absolute;
        top: 50%;
        left: 50%;
        -moz-transform: translateX(-50%) translateY(-50%);
        -webkit-transform: translateX(-50%) translateY(-50%);
        transform: translateX(-50%) translateY(-50%);
    }
}
</style>