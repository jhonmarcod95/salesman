<template>
    <div>
        <div class="header bg-green pb-6 pt-5 pt-md-6"> </div>
        <!-- Page content -->
        <div class="container-fluid mt--7">
            <div class="row mt-5">
                <div class="col-xl-12 order-xl-1">
                    <div class="card bg-secondary shadow">
                        <div class="card-header bg-white border-0">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Create Account</h3>
                                </div>
                                <div class="col-4 text-right">
                                    <a href="#!" class="btn btn-sm btn-primary">Clear</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form>
                                <h6 class="heading-small text-muted mb-4">Personal information</h6>
                                <div class="pl-lg-4">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-username">Last Name</label>
                                                <input type="text" id="input-username" class="form-control form-control-alternative" v-model="tsr.last_name">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-email">First Name</label>
                                                <input type="email" id="input-email" class="form-control form-control-alternative" v-model="tsr.first_name">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-first-name">Middle Name</label>
                                                <input type="text" id="input-first-name" class="form-control form-control-alternative" v-model="tsr.middle_name">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-last-name">Suffix</label>
                                                <input type="text" id="input-last-name" class="form-control form-control-alternative" v-model="tsr.suffix">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-4" />
                                <!-- Address -->
                                <h6 class="heading-small text-muted mb-4">Contact information</h6>

                                <div class="pl-lg-4">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-address">Address</label>
                                                <input id="input-address" class="form-control form-control-alternative" type="text" v-model="tsr.address">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-city">Email</label>
                                                <input type="text" id="input-city" class="form-control form-control-alternative" v-model="tsr.email">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-country">Contact Number</label>
                                                <input type="text" id="input-country" class="form-control form-control-alternative" v-model="tsr.contact_number">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="text">
                                            <button @click="addTsr(tsr)"  type="button" class="btn btn-primary mt-4">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data(){
        return{
            tsr:{
                last_name: '',
                first_name: '',
                middle_name: '',
                middle_initial: '',
                suffix: '',
                email: '',
                address: '',
                contact_number: '',
                date_of_birth: '',
                date_hired: ''
            },
            errors: []
        }
    },
    methods:{
        addTsr(tsr){
            axios.post('/tsr', {
                last_name: tsr.last_name,
                first_name: tsr.first_name,
                middle_name: tsr.middle_name,
                middle_initial: tsr.middle_initial,
                suffix: tsr.suffix,
                email: tsr.email,
                address: tsr.address,
                contact_number: tsr.contact_number,
                date_of_birth: tsr.date_of_birth,
                date_hired: tsr.date_hired

            })
            .then(response => {
                window.location.href = response.data.redirect;
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            })
        }
    }
}
</script>
