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
                                                <input type="text" id="Area" class="form-control form-control-alternative" v-model="customer.area">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="classification">Classification</label>
                                                <input type="text" id="classification" class="form-control form-control-alternative" v-model="customer.classification">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="customer_code">Customer Code</label>
                                                <input type="text" id="customer_code" class="form-control form-control-alternative" v-model="customer.customer_code">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="group">Group</label>
                                                <input type="text" id="group" class="form-control form-control-alternative" v-model="customer.group">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-email">Name</label>
                                                <input type="text" id="name" class="form-control form-control-alternative" v-model="customer.name">
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
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="town">Town or City</label>
                                                <input id="town" class="form-control form-control-alternative" type="text" v-model="customer.town_city">
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
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="street">Province</label>
                                                <select class="form-control" v-model="customer.province">
                                                    <option v-for="(province, p) in provinces" v-bind:key="p" :value="province.id">{{ province.name}}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-city">Telephone 1</label>
                                                <input type="text" id="telephone-1" class="form-control form-control-alternative" v-model="customer.telephone_1">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-country">Telephone 2</label>
                                                <input type="text" id="telephone_2" class="form-control form-control-alternative" v-model="customer.telephone_2">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="fax_umber">Fax Number</label>
                                                <input type="text" id="fax_umber" class="form-control form-control-alternative" v-model="customer.fax_number">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="remarks">Remarks</label>
                                                <input type="text" id="remarks" class="form-control form-control-alternative" v-model="customer.remarks">
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
                    group: '',
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
                errors: []
            }
        },
        created(){
            this.fetchRegion();
            this.fetchProvince();
        },
        methods:{
            addCustomer(customer){
                axios.post('/customers',{
                    area : customer.area,
                    classification : customer.classification,
                    customer_code : customer.customer_code,
                    group: customer.group,
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
                    window.location.href = response.data.redirect;
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
            }
        },
    }
</script>
