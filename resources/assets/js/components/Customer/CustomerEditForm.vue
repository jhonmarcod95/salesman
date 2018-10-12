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
                                    <h3 class="mb-0">Edit Customer</h3>
                                </div>
                                <div class="col-4 text-right">
                                    <a href="#!" class="btn btn-sm btn-primary">Clear</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form>
                                <h6 class="heading-small text-muted mb-4">Customer information</h6>
                                <div class="pl-lg-4">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-username">Area</label>
                                                <input type="text" id="Area" class="form-control form-control-alternative" v-model="customers.area">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                           <div class="form-group">
                                                <label class="form-control-label" for="classification">Classification</label>
                                                <select class="form-control" v-model="customers.classification">
                                                    <option v-for="(classification, c) in classifications" v-bind:key="c" :value="classification.id">{{ classification.description}}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="customer_code">Customer Code</label>
                                                <input type="text" id="customer_code" class="form-control form-control-alternative" v-model="customers.customer_code">
                                                <span class="error" v-if="errors.customer_code">{{ errors.customer_code[0] }}</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="group">Group</label>
                                                <input type="text" id="group" class="form-control form-control-alternative" v-model="customers.group">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-email">Name</label>
                                                <input type="text" id="name" class="form-control form-control-alternative" v-model="customers.name">
                                                <span class="error" v-if="errors.name">{{ errors.name[0] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="my-4" />
                                <!-- Address -->
                                <h6 class="heading-small text-muted mb-4">Contact information</h6>

                                <div class="pl-lg-4">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="street">Street</label>
                                                <input id="street" class="form-control form-control-alternative" type="text" v-model="customers.street">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="town">Town or City</label>
                                                <input id="town" class="form-control form-control-alternative" type="text" v-model="customers.town_city">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                          <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="town">Region</label>
                                                <select class="form-control" v-model="customers.region">
                                                    <option v-for="(region, r) in regions" v-bind:key="r" :value="region.id">{{ region.name}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="street">Province</label>
                                                <select class="form-control" v-model="customers.province">
                                                    <option v-for="(province, p) in provinces" v-bind:key="p" :value="province.id">{{ province.name}}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-city">Telephone 1</label>
                                                <input type="text" id="telephone-1" class="form-control form-control-alternative" v-model="customers.telephone_1">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-country">Telephone 2</label>
                                                <input type="text" id="telephone_2" class="form-control form-control-alternative" v-model="customers.telephone_2">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="fax_umber">Fax Number</label>
                                                <input type="text" id="fax_umber" class="form-control form-control-alternative" v-model="customers.fax_number">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="remarks">Remarks</label>
                                                <input type="text" id="remarks" class="form-control form-control-alternative" v-model="customers.remarks">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="text">
                                            <button @click="updateCustomer(customers)" type="button" class="btn btn-primary mt-4">Save</button>
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
       props: ['customerId'],
        data(){
            return{
                customers: [],
                provinces: [],
                classifications:[],
                regions:[],
                errors: []
            }
        },
        created(){
            this.fetchRegion();
            this.fetchProvince();
            this.fetchCustomer();
            this.fetchClassification();
        },
        methods:{
            updateCustomer(customers){  
                axios.patch(`/customers/${customers.id}`,{
                    area : customers.area,
                    classification : customers.classification,
                    customer_code : customers.customer_code,
                    group: customers.group,
                    name: customers.name,
                    street: customers.street,
                    town_city: customers.town_city,
                    region: customers.region,
                    province: customers.province,
                    telephone_1: customers.telephone_1,
                    telephone_2: customers.telephone_2,
                    fax_number: customers.fax_number,
                    remarks: customers.remarks
                })
                .then(response => {
                    window.location.href = response.data.redirect;
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                })
            },
            fetchCustomer(){
                axios.get(`/customers/show/${this.customerId}`)
                .then(response => {
                    this.customers = response.data;
                })
                .catch(error =>{
                    this.errors = error.response.data.errors;
                });
            },
            fetchRegion(){
                axios.get('/regions')
                .then(response => { 
                    this.regions = response.data;
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                })
            },
            fetchProvince(){
                axios.get('/provinces')
                .then(response => { 
                    this.provinces = response.data;
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                })
            },
            fetchClassification(){
                axios.get('/customers-classification-all')
                .then(response => { 
                    this.classifications = response.data;
                })
                .catch(error =>{
                    this.errors = error.response.data.errors;
                })
            }
        },
    }
</script>
