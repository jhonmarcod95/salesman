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
                                                <div v-if="customer.status == '1' || customer.status == '2'" style="border-radius:10px;border:1px solid red;padding:5px 10px 10px 5px;">
                                                    <label class="form-control-label" for="customer_code">
                                                        Select Customer Code from SAP
                                                    </label>
                                                    <div v-if="customer_codes.length > 0">
                                                         <multiselect
                                                            v-model="customercodeSelect"
                                                            :options="customer_codes"
                                                            :multiple="false"
                                                            track-by="id"
                                                            :custom-label="customLabelCustomerCode"
                                                            :max="5"
                                                            placeholder="Select Customer Code"
                                                            id="selected_customer"
                                                            @input="onChangeCustomerCode(customercodeSelect)"
                                                        >
                                                        </multiselect>
                                                         <span class="text-danger small" v-if="errors.customer_code">{{ errors.customer_code[0] }}</span>
                                                    </div>
                                                    <div v-else>
                                                       <span class="text-primary">Please wait a moment.. Getting Customer Codes.. </span>
                                                    </div>
                                                </div>
                                                <div v-else>
                                                    <span v-if="show">Last customer code: {{ pilili_code }}<br></span>
                                                    <label class="form-control-label" for="customer_code">Customer Code</label>
                                                    <input type="text" id="customer_code" class="form-control form-control-alternative" v-model="customer.customer_code">
                                                    
                                                </div>
                                                
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-email">Name</label>
                                                <input type="text" id="name" class="form-control form-control-alternative" v-model="customer.name">
                                                <span class="text-danger small" v-if="errors.name">{{ errors.name[0] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="classification">Classification</label>
                                                  <select class="form-control" v-model="customer.classification" @change="checkCustomerCode">
                                                    <option v-for="(classification, c) in classifications" v-bind:key="c" :value="classification.id">{{ classification.description}}</option>
                                                  </select>
                                                  <span class="text-danger small" v-if="errors.classification">{{ errors.classification[0] }}</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                           <div class="form-group">
                                                <label class="form-control-label" for="classification">Status</label>
                                                <select class="form-control" v-model="customer.status" @change="statusCustomerCode">
                                                    <option v-for="(status, c) in statuses" v-bind:key="c" :value="status.id">{{ status.description}}</option>
                                                </select>
                                                <span class="text-danger small" v-if="errors.status">{{ errors.status[0] }}</span>
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
                                                <span class="text-danger small" v-if="errors.street">{{ errors.street[0] }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="town">Town or City</label>
                                                <input id="town" class="form-control form-control-alternative" type="text" v-model="customer.town_city">
                                                <span class="text-danger small" v-if="errors.town_city">{{ errors.town_city[0] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <!--<div class="col-md-6">-->
                                            <!--<div class="form-group">-->
                                                <!--<label class="form-control-label" for="town">Region</label>-->
                                                <!--<select class="form-control" v-model="customer.region">-->
                                                    <!--<option v-for="(region, r) in regions" v-bind:key="r" :value="region.id">{{ region.name}}</option>-->
                                                <!--</select>-->
                                                 <!--<span class="text-danger" v-if="errors.region">{{ errors.region[0] }}</span>-->
                                            <!--</div>-->
                                        <!--</div>-->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="street">Province</label>
                                                <select class="form-control" v-model="customer.province">
                                                    <option v-for="(province, p) in provinces" v-bind:key="p" :value="province.id">{{ province.name}}</option>
                                                </select>
                                                <span class="text-danger small" v-if="errors.province">{{ errors.province[0] }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="google_address">Google Map Address</label>
                                                <input id="google_address" class="form-control form-control-alternative" type="text" v-model="customer.google_address" placeholder="Enter a Location">
                                                <span class="text-danger small" v-if="errors.google_address">{{ errors.google_address[0] }}</span>

                                                <button type="button" :disabled="showMap" class="btn btn-sm btn-primary mt-2" data-toggle="modal" data-target="#showMap">Show Map</button>
                                            </div>

                                            
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-city">Telephone 1</label>
                                                <input type="text" id="telephone-1" class="form-control form-control-alternative" v-model="customer.telephone_1">
                                                <span class="text-danger small" v-if="errors.telephone_1">{{ errors.telephone_1[0] }}</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-country">Telephone 2</label>
                                                <input type="text" id="telephone_2" class="form-control form-control-alternative" v-model="customer.telephone_2">
                                                <span class="text-danger small" v-if="errors.telephone_2">{{ errors.telephone_2[0] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="fax_umber">Fax Number</label>
                                                <input type="text" id="fax_umber" class="form-control form-control-alternative" v-model="customer.fax_number">
                                                <span class="text-danger small" v-if="errors.fax_number">{{ errors.fax_number[0] }}</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="remarks">Remarks</label>
                                                <input type="text" id="remarks" class="form-control form-control-alternative" v-model="customer.remarks">
                                                <span class="text-danger small" v-if="errors.remarks">{{ errors.remarks[0] }}</span>
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
        <!-- Map Modal -->
        <div class="modal fade" id="showMap" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title-default">Map</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="mapContainer" style="width:100%;height:300px;">
                    <div id="map"></div>
                    <pre id='coordinates' class='coordinates'></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link  ml-auto" data-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<script>
    import Mapbox from 'mapbox-gl-vue';
    import mapboxgl from 'mapbox-gl';

    import Multiselect from 'vue-multiselect';

    export default {
         components: {
            Mapbox,Multiselect
        },
        props:['companyId'],
        data(){
            return{
                customer:{
                    area: '',
                    classification: '',
                    customer_code: '',
                    name: '',
                    street: '',
                    google_address: '',
                    town_city: '',
                    region: '',
                    province: '',
                    telephone_1: '',
                    telephone_2: '',
                    fax_number: '',
                    remarks: '',
                    lat: '',
                    lng: ''
                },
                accessToken: 'pk.eyJ1IjoiamF5LWx1bWFnZG9uZzEyMyIsImEiOiJjazFxNm5wZGwxNG02M2dtaXF2dHE1YzluIn0.SHUJTfNTrhGoyacA8H7Tbw',
                mapStyle: 'mapbox://styles/mapbox/streets-v11',
                mapCenter: [121.035249, 14.675647],
                showMap:true,
                show: false,
                pilili_code: '',
                provinces: [],
                regions:[],
                classifications:[],
                statuses:[],
                errors: [],
                customer_codes : [],
                customercodeSelect : ''
            }   
        },
        created(){
            this.fetchCustomerCodes();
            this.fetchRegion();
            this.fetchProvince();
            this.fetchClassification();
            this.fetchStatus();
        },
        mounted() {
            let vm  = this;
            // Create the search box and link it to the UI element.
            var input = document.getElementById('google_address');
            var searchBox = new google.maps.places.Autocomplete(input, {
                 componentRestrictions: {country: 'ph'}
            });
    
            searchBox.addListener('place_changed', function() {
                var place = searchBox.getPlace();

                if (place.length == 0) {
                    return;
                }

                if (!place.geometry) {
                    // User entered the name of a Place that was not suggested and
                    // pressed the Enter key, or the Place Details request failed.
                    console.log("No details available for input: '" + place.name + "'");
                    return;
                }

                //Map
                vm.customer.google_address = document.getElementById("google_address").value;
                vm.mapbox = Mapbox;
                vm.getGeocodeCustomer(vm.customer.google_address);
                vm.showMap = false;

                //Bind town or city of address to input
                // place.address_components.filter(function(address){
                //     address.types.filter(function(types) {
                //         if(types == 'locality'){
                //             vm.customer.town_city = address.long_name;
                //         }
                //     });
                // });

            });
        },
        methods:{
            onChangeCustomerCode(){
                this.customer.name = this.customercodeSelect.name
            },
            statusCustomerCode(){
                if(this.customer.status == 1 || this.customer.status == 2){
                   this.customercodeSelect = '';    
                }
            },
            customLabelCustomerCode (customer) {
                return `${customer.customer_code  }` + ' - ' + `${customer.name  }`
            },
            fetchCustomerCodes(){
                axios.get('/customer-codes-all')
                .then(response => { 
                    this.customer_codes = response.data;
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                })
            },
            addCustomer(customer){

                if(this.customer.status == 1 || this.customer.status == 2){
                    if(this.customercodeSelect.customer_code){
                        customer.customer_code = this.customercodeSelect.customer_code;
                    }
                }

                axios.post('/customers',{
                    classification : customer.classification,
                    status : customer.status,
                    customer_code : customer.customer_code,
                    name: customer.name,
                    street: customer.street,
                    google_address: customer.google_address,
                    lat: customer.lat,
                    lng: customer.lng,
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
            fetchStatus(){
                axios.get('/customers-status-options')
                .then(response => { 
                    this.statuses = response.data;
                })
                .catch(error =>{
                    this.errors = error.response.data.errors;
                })
            },
            fetchClassification(){
                axios.get('/customers-classification-options')
                .then(response => { 
                    this.classifications = response.data;
                })
                .catch(error =>{
                    this.errors = error.response.data.errors;
                })
            },
            checkCustomerCode(){

                if(this.customer.classification != 1 && this.customer.classification != 2 && this.customer.classification != 8){
                    axios.post('/check-customer-code',{
                        classification: this.customer.classification,
                        company_id: this.companyId
                        
                    })
                    .then(response => {
                        this.customer.customer_code = '';
                        this.customer.customer_code = response.data;
                        this.show = false;
                        document.getElementById("customer_code").disabled = true;
                    })
                    .catch(error => {
                        this.errors = error.response.data.errors;
                    })
                }else if(this.customer.classification == 8 && this.companyId != 5){
                    axios.post('/check-customer-code',{
                        classification: this.customer.classification,
                        company_id: this.companyId
                    })
                    .then(response => {
                        this.customer.customer_code = '';
                        this.customer.customer_code = response.data;
                        this.show = false;
                        document.getElementById("customer_code").disabled = true;
                    })
                    .catch(error => {
                        this.errors = error.response.data.errors;
                    })
                }else if(this.customer.classification == 8 && this.companyId == 5){
                    axios.post('/check-customer-code',{
                        classification: this.customer.classification,
                        company_id: this.companyId
                    })
                    .then(response => {
                        this.customer.customer_code = '';
                        this.show = true;
                        this.pilili_code = response.data
                        document.getElementById("customer_code").disabled = false;
                    })
                    .catch(error => {
                        this.errors = error.response.data.errors;
                    })
                }else{
                    this.show = false;
                    this.customer.customer_code = '';
                    document.getElementById("customer_code").disabled = false;
                }
            },
            getGeocodeCustomer(address){
                let v = this;
                axios.get(`/customers-geocode-json/${address.replace(/[/#]/g, '')}`)
                .then(response => { 
                    const mapcontainer = document.getElementById("map");
                    mapcontainer.innerHTML = '';
                    v.customer.lat = response.data.lat;
                    v.customer.lng = response.data.lng;
                    mapboxgl.accessToken = v.accessToken;
                    var map = new mapboxgl.Map({
                        container: 'map',
                        style: v.mapStyle,
                        center: [v.customer.lng,v.customer.lat],
                        minzoom:23,
                        maxZoom:20,
                        zoom: 17,
                        maxBounds: [[110.446227,2.949317], [131.509814,21.637444 ]],
                        height:100
                    });
                    
                    map.resize();

                    map.addControl(new mapboxgl.NavigationControl());
                    map.addControl(new mapboxgl.FullscreenControl());

                    var marker = new mapboxgl.Marker({
                        draggable: true
                    })
                    .setLngLat([v.customer.lng,v.customer.lat])
                    .addTo(map);
                    
                    coordinates.style.display = 'none';
                    function onDragEnd() {   
                        var lngLat = marker.getLngLat();
                        coordinates.style.display = 'block';
                        coordinates.innerHTML = 'Longitude: ' + lngLat.lng + '<br />Latitude: ' + lngLat.lat;
                        v.customer.lng = lngLat.lng;
                        v.customer.lat = lngLat.lat;

                        // map.flyTo({center: [v.customer.lng,v.customer.lat], zoom: 17})
                    }
                    
                    marker.on('dragend', onDragEnd)
                
                   
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                })
            }
        },
    }
</script>

<style>
    
     #map{
        position: relative;
        height: 100%;
        width: 100%;
        background-color:#75CFF0!important;
    }

    .building {
        background-image: url('/img/map/customer.png');
        background-size: cover;
        width: 35px;
        height: 35px;
        cursor: pointer;
    }

    .modal-xl{
        width: 400px!important;
    }

    .mapboxgl-ctrl-logo{
        display:none!important;
    }
    .mapboxgl-ctrl.mapboxgl-ctrl-attrib{
        display:none!important;
    }

    .coordinates {
        background: rgba(0,0,0,0.5);
        color: #fff;
        position: absolute;
        bottom: 40px;
        left: 10px;
        padding:5px 10px;
        margin: 0;
        font-size: 11px;
        line-height: 18px;
        border-radius: 3px;
        display: none;
    }

</style>
