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
                                    <h3 class="mb-0">Create Customer</h3>
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
                                                <label class="form-control-label" for="customer_code">Customer Code</label>
                                                <input type="text" id="customer_code" class="form-control form-control-alternative" v-model="customer.customer_code">
                                                <span class="text-danger" v-if="errors.customer_code">{{ errors.customer_code[0] }}</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-email">Name</label>
                                                <input type="text" id="name" class="form-control form-control-alternative" v-model="customer.name">
                                                <span class="text-danger" v-if="errors.name">{{ errors.name[0] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="classification">Classification</label>
                                                  <select class="form-control" v-model="customer.classification" @change="checkCustomerCode">
                                                    <option v-for="(classification, c) in classifications" v-bind:key="c" :value="classification.id">{{ classification.description}}</option>
                                                    <span class="text-danger" v-if="errors.description">{{ errors.description[0] }}</span>
                                                </select>
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
                                                <input id="street" class="form-control form-control-alternative" type="text" v-model="customer.street">
                                                <span class="text-danger" v-if="errors.street">{{ errors.street[0] }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="town">Town or City</label>
                                                <input id="town" class="form-control form-control-alternative" type="text" v-model="customer.town_city">
                                                <span class="text-danger" v-if="errors.town_city">{{ errors.town_city[0] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                          <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="town">Region</label>
                                                <select class="form-control" v-model="customer.region">
                                                    <option v-for="(region, r) in regions" v-bind:key="r" :value="region.id">{{ region.name}}</option>
                                                </select>
                                                 <span class="text-danger" v-if="errors.region">{{ errors.region[0] }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="street">Province</label>
                                                <select class="form-control" v-model="customer.province">
                                                    <option v-for="(province, p) in provinces" v-bind:key="p" :value="province.id">{{ province.name}}</option>
                                                    <span class="text-danger" v-if="errors.province">{{ errors.province[0] }}</span>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-city">Telephone 1</label>
                                                <input type="text" id="telephone-1" class="form-control form-control-alternative" v-model="customer.telephone_1">
                                                <span class="text-danger" v-if="errors.telephone_1">{{ errors.telephone_1[0] }}</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-country">Telephone 2</label>
                                                <input type="text" id="telephone_2" class="form-control form-control-alternative" v-model="customer.telephone_2">
                                                <span class="text-danger" v-if="errors.telephone_2">{{ errors.telephone_2[0] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="fax_umber">Fax Number</label>
                                                <input type="text" id="fax_umber" class="form-control form-control-alternative" v-model="customer.fax_number">
                                                <span class="text-danger" v-if="errors.fax_number">{{ errors.fax_number[0] }}</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="remarks">Remarks</label>
                                                <input type="text" id="remarks" class="form-control form-control-alternative" v-model="customer.remarks">
                                                <span class="text-danger" v-if="errors.remarks">{{ errors.remarks[0] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="text">
                                            <button @click="addCustomer(customer)" type="button" class="btn btn-primary mt-4">Save</button>
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
                customer:{
                    area: '',
                    classfication: '',
                    customer_code: '',
                    name: '',
                    street: '',
                    town_city: '',
                    region: '',
                    province: '',
                    telephone_1: '',
                    telephone_2: '',
                    fax_number: '',
                    remarks: ''
                },
                provinces: [],
                regions:[],
                classifications:[],
                errors: []
            }
        },
        created(){
            this.fetchRegion();
            this.fetchProvince();
            this.fetchClassification();
        },
        methods:{
            addCustomer(customer){
                axios.post('/customers',{
                    classification : customer.classification,
                    customer_code : customer.customer_code,
                    name: customer.name,
                    street: customer.street,
                    town_city: customer.town_city,
                    region: customer.region,
                    province: customer.province,
                    telephone_1: customer.telephone_1,
                    telephone_2: customer.telephone_2,
                    fax_number: customer.fax_number,
                    remarks: customer.remarks
                })
                .then(response => { 
                    if(confirm('Customer Successful Added')){
                        window.location.href = response.data.redirect;
                    }
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                })
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
            },
            checkCustomerCode(){
                if(this.customer.classification == 3){
                    axios.get('/check-customer-code')
                    .then(response => { 
                        this.customer.customer_code = response.data;
                        document.getElementById("customer_code").disabled = true;
                    })
                    .catch(error => {
                        this.errors = error.response.data.errors;
                    })
                }
                else{
                    this.customer.customer_code = '';
                    document.getElementById("customer_code").disabled = false;
                }
            }
        },
    }
</script>
