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
                                                <span class="text-danger small" v-if="errors.last_name">{{ errors.last_name[0] }}</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-email">First Name</label>
                                                <input type="email" id="input-email" class="form-control form-control-alternative" v-model="tsr.first_name">
                                                <span class="text-danger small" v-if="errors.first_name">{{ errors.first_name[0] }}</span>
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
                                                <label class="form-control-label" for="middle_initial">Middle Initial</label>
                                                <input type="text" id="middle_initial" class="form-control form-control-alternative" v-model="tsr.middle_initial">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-last-name">Suffix</label>
                                                <input type="text" id="input-last-name" class="form-control form-control-alternative" v-model="tsr.suffix">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="role">Company</label>
                                                <select class="form-control" v-model="tsr.company">
                                                    <option v-for="(company,c) in companies" v-bind:key="c" :value="company.id"> {{ company.name }}</option>
                                                </select>
                                                <span class="text-danger" v-if="errors.company  ">{{ errors.company[0] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="role">Location</label>
                                                <select class="form-control" v-model="tsr.location">
                                                    <option v-for="(location,l) in locations" v-bind:key="l" :value="location.id"> {{ location.name }}</option>
                                                </select>
                                                <span class="text-danger" v-if="errors.location  ">{{ errors.location[0] }}</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-last-name">Vendor Code</label>
                                                <input type="text" id="vendor-code" class="form-control form-control-alternative" v-model="tsr.vendor_code">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-4" />
                                <!-- Address -->
                                <h6 class="heading-small text-muted mb-4">Contact information</h6>

                                <div class="pl-lg-4">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-address">Address</label>
                                                <input id="input-address" class="form-control form-control-alternative" type="text" v-model="tsr.address">
                                                <span class="text-danger small" v-if="errors.address">{{ errors.address[0] }}</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-city">Email</label>
                                                <input type="text" id="input-city" class="form-control form-control-alternative" v-model="tsr.email">
                                                <span class="text-danger small" v-if="errors.email">{{ errors.email[0] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-country">Contact Number</label>
                                                <input type="text" id="input-country" class="form-control form-control-alternative" v-model="tsr.contact_number">
                                                <span class="text-danger small" v-if="errors.contact_number">{{ errors.contact_number[0] }}</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-city">Bithday</label>
                                                <input type="date" id="date_of_birth" class="form-control form-control-alternative" v-model="tsr.date_of_birth">
                                                <span class="text-danger small" v-if="errors.date_of_birth">{{ errors.date_of_birth[0] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-city">Date Hired</label>
                                                <input type="date" id="date_hired" class="form-control form-control-alternative" v-model="tsr.date_hired">
                                                <span class="text-danger small" v-if="errors.date_hired">{{ errors.date_hired[0] }}</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="contact_person">Contact Person</label>
                                                <input type="text" id="contact_person" class="form-control form-control-alternative" v-model="tsr.contact_person">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="personal_email">Personal Email</label>
                                                <input type="text" id="personal_email" class="form-control form-control-alternative" v-model="tsr.personal_email">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="plate_number">Plate Number</label>
                                                <input type="text" id="plate_number" class="form-control form-control-alternative" v-model="tsr.plate_number">
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
                location: '',
                vendor_code: '',
                contact_number: '',
                date_of_birth: '',
                date_hired: '',
                contact_person: '',
                personal_email: '',
                plate_number: '',
                company: '',
            },
            companies: [],
            locations: [],
            errors: []
        }
    },
    created(){
        this.fetchCompanies();
        this.fetchLocations();
    },
    methods:{
        fetchCompanies(){
            axios.get('/companies-all')
            .then(response => { 
                this.companies = response.data;
            })
            .catch(error => { 
                this.errors = error.response.data.errors;
            })
        },
        fetchLocations(){
            axios.get('/locations')
            .then(response => { 
                this.locations = response.data;
            })
            .catch(error => { 
                this.errors = error.response.data.errors;
            })
        },
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
                date_hired: tsr.date_hired,
                contact_person: tsr.contact_person,
                personal_email: tsr.personal_email,
                plate_number: tsr.plate_number,
                company: tsr.company,
                location: tsr.location,
                vendor_code: tsr.vendor_code,

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
