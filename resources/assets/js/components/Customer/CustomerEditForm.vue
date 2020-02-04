<template>
    <div>
        <loader v-if="loading"></loader>
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
                            </div>
                        </div>
                        <div class="card-body">
                            <form>
                                <h6 class="heading-small text-muted mb-4">Customer information</h6>
                                <div class="pl-lg-4">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <span v-if="show">Last customer code: {{ pilili_code }}<br></span>
                                                <label class="form-control-label" for="customer_code">Customer Code</label>
                                                <input type="text" id="customer_code" class="form-control form-control-alternative" v-model="customers.customer_code" :disabled="disabledCustomerCode" >
                                                <span class="text-danger small" v-if="errors.customer_code">{{ errors.customer_code[0] }}</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-email">Name</label>
                                                <input type="text" id="name" class="form-control form-control-alternative" v-model="customers.name">
                                                <span class="text-danger small" v-if="errors.name">{{ errors.name[0] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                           <div class="form-group">
                                                <label class="form-control-label" for="classification">Classification</label>
                                                <select class="form-control" v-model="customers.classification">
                                                    <option v-for="(classification, c) in classifications" v-bind:key="c" :value="classification.id">{{ classification.description}}</option>
                                                </select>
                                                <span class="text-danger small" v-if="errors.classification">{{ errors.classification[0] }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="col-lg-6">
                                           <div class="form-group">
                                                <label class="form-control-label" for="classification">Status</label>
                                                <select class="form-control" v-model="customers.status" @change="disableCustomerCode">
                                                    <option v-for="(status, c) in statuses" v-bind:key="c" :value="status.id">{{ status.description}}</option>
                                                </select>
                                                <span class="text-danger small" v-if="errors.status">{{ errors.status[0] }}</span>
                                            </div>
                                        </div>

                                        <div class="col-lg-12" v-if="customers.classification == '5' || customers.classification == '8' || customers.classification == '9'">
                                           <div class="form-group">
                                                <label class="form-control-label" for="classification">Select Customer Dealer</label>
                                                <multiselect
                                                        v-model="customerDealer"
                                                        :options="customerDealerOptions"
                                                        :multiple="false"
                                                        track-by="id"
                                                        :custom-label="customLabelCustomerDealer"
                                                        placeholder="Customer Dealer"
                                                        id="selected_customer_dealer"
                                                >
                                                </multiselect>

                                                <span class="text-danger small" v-if="errors.customerDealer">{{ errors.customerDealer[0] }}</span>
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
                                                <span class="text-danger small" v-if="errors.street">{{ errors.street[0] }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="town">Town or City</label>
                                                <input id="town" class="form-control form-control-alternative" type="text" v-model="customers.town_city">
                                                <span class="text-danger small" v-if="errors.town_city">{{ errors.town_city[0] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <!--<div class="col-md-6">-->
                                            <!--<div class="form-group">-->
                                                <!--<label class="form-control-label" for="town">Region</label>-->
                                                <!--<select class="form-control" v-model="customers.region">-->
                                                    <!--<option v-for="(region, r) in regions" v-bind:key="r" :value="region.id">{{ region.name}}</option>-->
                                                <!--</select>-->
                                                <!--<span class="text-danger" v-if="errors.region">{{ errors.region[0] }}</span>-->
                                            <!--</div>-->
                                        <!--</div>-->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="street">Province</label>
                                                <select class="form-control" v-model="customers.province_id">
                                                    <option v-for="(province, p) in provinces" v-bind:key="p" :value="province.id">{{ province.name}}</option>
                                                </select>
                                                <span class="text-danger small" v-if="errors.province">{{ errors.province[0] }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-control-label" for="google_address">Google Map Address</label>
                                                <input id="google_address" class="form-control form-control-alternative" type="text" v-model="customers.google_address" placeholder="Enter a Location">
                                                <span class="text-danger small" v-if="errors.google_address">{{ errors.google_address[0] }}</span>
                                                
                                                <button type="button" :disabled="showMap" class="btn btn-sm btn-primary mt-2" data-toggle="modal" data-target="#showMap">Show Map</button>
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label class="form-control-label" for="meet_up_location_address">Add Meet Up Location</label>
                                                <input id="meet_up_location_address" class="form-control form-control-alternative" type="text" v-model="customers.meet_up_location_address" placeholder="Enter a Location">
                                                <span class="text-danger small" v-if="errors.meet_up_location_address">{{ errors.meet_up_location_address[0] }}</span>

                                                <button type="button" :disabled="showMapMeetUpLocation" class="btn btn-sm btn-primary mt-2" data-toggle="modal" data-target="#showMapMeetUpLocation">Show Map </button>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-city">Telephone 1</label>
                                                <input type="text" id="telephone-1" class="form-control form-control-alternative" v-model="customers.telephone_1">
                                                <span class="text-danger small" v-if="errors.telephone_1">{{ errors.telephone_1[0] }}</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-country">Telephone 2</label>
                                                <input type="text" id="telephone_2" class="form-control form-control-alternative" v-model="customers.telephone_2">
                                                <span class="text-danger small" v-if="errors.telephone_2">{{ errors.telephone_2[0] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="fax_umber">Fax Number</label>
                                                <input type="text" id="fax_umber" class="form-control form-control-alternative" v-model="customers.fax_number">
                                                <span class="text-danger small" v-if="errors.fax_number">{{ errors.fax_number[0] }}</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="remarks">Remarks</label>
                                                <input type="text" id="remarks" class="form-control form-control-alternative" v-model="customers.remarks">
                                                <span class="text-danger small" v-if="errors.remarks">{{ errors.remarks[0] }}</span>
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
                    <Mapbox 
                        :accessToken="accessToken" 
                        :map-options="{
                            style: mapStyle,
                            center: mapCenter,
                            minzoom:23,
                            maxZoom:18,
                            zoom: 3,
                            maxBounds: [[110.446227,2.949317], [131.509814,21.637444 ]]
                        }"
                        :scale-control="{
                            show: true,
                            position: 'top-left'
                        }"
                        :fullscreen-control="{
                            show: true,
                            position: 'top-left'
                        }"
                    /> 
                    <pre id='coordinates' class='coordinates'></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link  ml-auto" data-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div>

        <!-- Map Meet Up Modal -->
        <div class="modal fade" id="showMapMeetUpLocation" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title-default">Map</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="mapContainer" style="width:100%;height:300px;">
                    <div id="map_meet_up"></div>
                    <pre id='coordinates_meet_up' class='coordinates'></pre>
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
    import loader from '../Loader';
    import Multiselect from 'vue-multiselect';
    export default {
        components: {
            Mapbox,Multiselect
        },
       props: ['customerId'],
        data(){
            return{
                customers: [],
                provinces: [],
                classifications:[],
                customerDealerOptions:[],
                customerDealer:[],
                statuses:[],
                regions:[],
                errors: [],
                default_code: '',
                default_classification: '',
                show: false,
                pilili_code: '',
                lat:'',
                lng:'',
                meet_up_lat: '',
                meet_up_lng: '',
                accessToken: 'pk.eyJ1IjoiamF5LWx1bWFnZG9uZzEyMyIsImEiOiJjazFxNm5wZGwxNG02M2dtaXF2dHE1YzluIn0.SHUJTfNTrhGoyacA8H7Tbw',
                mapStyle: 'mapbox://styles/mapbox/streets-v11',
                mapCenter: [121.035249, 14.675647],
                showMap:true,
                showMapMeetUpLocation:true,
                loading: false,
                disabledCustomerCode:true,
            }
        },
        created(){
            this.mapbox = Mapbox;
            this.fetchRegion();
            this.fetchProvince();
            this.fetchCustomerDealers();
            this.fetchCustomer();
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
                vm.customers.google_address = document.getElementById("google_address").value;
                 
                vm.getGeocodeCustomerEdit(vm.customers.google_address);
            });

            //Meet Up location Address 
            var input_meet_up = document.getElementById('meet_up_location_address');
            var searchBoxMeetUp = new google.maps.places.Autocomplete(input_meet_up, {
                 componentRestrictions: {country: 'ph'}
            });

            searchBoxMeetUp.addListener('place_changed', function() {
                var place = searchBoxMeetUp.getPlace();

                if (place.length == 0) {
                    return;
                }
                if (!place.geometry) {
                    return;
                }
                //Map
                vm.customers.meet_up_location_address = document.getElementById("meet_up_location_address").value;
                vm.getGeocodeCustomerMeetUp(vm.customers.meet_up_location_address);
            });

        },
        methods:{
            fetchCustomerDealers(){
                axios.get('/customer-dealers')
                .then(response => { 
                    this.customerDealerOptions = response.data;
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                })
            },
            fetchCustomerDealer(customer_dealer_id){
                let v = this;
                 axios.get('/customer-dealers')
                .then(response => { 
                    var costumer_dealer = response.data;
                    var customer_dealer_filtered = costumer_dealer.filter(customer => {
                        if(customer.id == customer_dealer_id){
                            return customer;
                        }
                    });
                    this.customerDealer = customer_dealer_filtered ? customer_dealer_filtered[0] : []; 
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                })
            },
            customLabelCustomerDealer (customer) {
                return `${ customer.name + ' (' + customer.company.name  + ')' }`
            },
            updateCustomer(customers){  
                let v = this;
                
                if(customers.classification == 5 || customers.classification == 8 || customers.classification == 9){
                    v.customerDealer = v.customerDealer;
                }else{
                    v.customerDealer = [];
                }
             
                axios.patch(`/customers/${customers.id}`,{
                    classification : customers.classification,
                    status : customers.status,
                    customer_code : customers.customer_code,
                    group: customers.group,
                    name: customers.name,
                    street: customers.street,
                    town_city: customers.town_city,
                    region: customers.region,
                    province: customers.province_id,
                    google_address: customers.google_address,
                    lat: v.customers.lat,
                    lng: v.customers.lng,
                    meet_up_location_address: v.customers.meet_up_location_address,
                    meet_up_lat: v.customers.meet_up_lat,
                    meet_up_lng: v.customers.meet_up_lng,
                    telephone_1: customers.telephone_1,
                    telephone_2: customers.telephone_2,
                    fax_number: customers.fax_number,
                    remarks: customers.remarks,
                    customerDealer: v.customerDealer
                })
                .then(response => {
                    window.location.href = response.data.redirect;
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
            fetchCustomer(){
                axios.get(`/customers/show/${this.customerId}`)
                .then(response => {
                    this.customers = response.data;
                    this.default_code = this.customers.customer_code;
                    this.default_classification = this.customers.classification;
                    this.fetchCustomerDealer(this.customers.customer_dealer_id);
                    this.disableCustomerCode();
                    this.showEditMap(this.customers.lat,this.customers.lng);
                    this.showEditMapMeetUp(this.customers.meet_up_lat,this.customers.meet_up_lng);
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
                axios.get('/customers-classification-options')
                .then(response => { 
                    this.classifications = response.data;
                })
                .catch(error =>{
                    this.errors = error.response.data.errors;
                })
            },
            checkCustomerCode(){
                if(this.customers.company_id == 5){ //Pilili company_code generation
                    if(this.default_classification == 8 && this.customers.classification != 8  && this.customers.classification != 1 && this.customers.classification != 2){
                        axios.post('/check-customer-code',{
                            classification: this.customers.classification,
                            company_id: this.customers.company_id
                        })
                        .then(response => {
                            this.customers.customer_code = '';
                            this.customers.customer_code = response.data;
                            this.show = false;
                            // document.getElementById("customer_code").disabled = true;
                        })
                        .catch(error => {
                            this.errors = error.response.data.errors;
                        })
                    }else if(this.default_classification != 8 && this.customers.classification == 8){
                        axios.post('/check-customer-code',{
                            classification: this.customers.classification,
                            company_id: this.customers.company_id
                        })
                        .then(response => {
                            this.customers.customer_code = '';
                            this.show = true;
                            this.pilili_code = response.data
                            // document.getElementById("customer_code").disabled = false;
                        })
                        .catch(error => {
                            this.errors = error.response.data.errors;
                        })
                    }else if(this.default_classification != 8 && this.customers.classification != 1 && this.customers.classification != 2){
                        this.show = false;
                        this.customers.customer_code = this.default_code;
                        // document.getElementById("customer_code").disabled = true;
                    }else if(this.default_classification == 8 && this.customers.classification == 8){
                        this.show = false;
                        this.customers.customer_code = this.default_code;
                        // document.getElementById("customer_code").disabled = true;
                    }
                    else{
                        this.show = false;
                        this.customers.customer_code = '';
                        // document.getElementById("customer_code").disabled = false;
                    }

                }else{
                    // if(this.customers.classification != 1 && this.customers.classification != 2){
                    //     this.customers.customer_code = this.default_code;
                    //     document.getElementById("customer_code").disabled = true;
                    // }else{
                    //     this.customers.customer_code = '';
                    //     document.getElementById("customer_code").disabled = false;
                    // }

                }
            },
            getGeocodeCustomerEdit(address){
                let v = this;
                v.loading = true;
                axios.get(`/customers-geocode-json/${address.replace(/[/#]/g, '')}`)
                .then(response => { 
                    const mapcontainer = document.getElementById("map");
                    mapcontainer.innerHTML = '';
                    v.customers.lat = response.data.lat;
                    v.customers.lng = response.data.lng;
                    mapboxgl.accessToken = v.accessToken;

                    if(response.data.lat && response.data.lng){
                        v.showMap = false;
                    }
                
                    v.loading = false;
                    
                    var map = new mapboxgl.Map({
                        container: 'map',
                        style: v.mapStyle,
                        center: [v.customers.lng,v.customers.lat],
                        minzoom:23,
                        maxZoom:20,
                        zoom: 17,
                        maxBounds: [[110.446227,2.949317], [131.509814,21.637444 ]],
                        height:100
                    });
                    
                    map.addControl(new mapboxgl.NavigationControl());
                    map.addControl(new mapboxgl.FullscreenControl());
                
                    var marker = new mapboxgl.Marker({
                        draggable: true
                    })
                    .setLngLat([v.customers.lng,v.customers.lat])
                    .addTo(map);
                    coordinates.style.display = 'none';
                    function onDragEnd() {   
                        var lngLat = marker.getLngLat();
                        coordinates.style.display = 'block';
                        coordinates.innerHTML = 'Longitude: ' + lngLat.lng + '<br />Latitude: ' + lngLat.lat;
                        v.customers.lng = lngLat.lng;
                        v.customers.lat = lngLat.lat;

                        // map.flyTo({center: [v.customers.lng,v.customers.lat], zoom: 17});
                    }
                    
                    marker.on('dragend', onDragEnd)
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                })
            },
            showEditMap(lat,lng){
                if(lat && lng){
                    let v = this;
                    const mapcontainer = document.getElementById("map");
                    mapcontainer.innerHTML = '';
                    mapboxgl.accessToken = v.accessToken;
                    var map = new mapboxgl.Map({
                        container: 'map',
                        style: v.mapStyle,
                        center: [v.customers.lng,v.customers.lat],
                        minzoom:23,
                        maxZoom:20,
                        zoom: 17,
                        maxBounds: [[110.446227,2.949317], [131.509814,21.637444 ]],
                        height:100
                    });
                    
                    map.addControl(new mapboxgl.NavigationControl());
                    map.addControl(new mapboxgl.FullscreenControl());
                
                    var marker = new mapboxgl.Marker({
                        draggable: true
                    })
                    .setLngLat([v.customers.lng,v.customers.lat])
                    .addTo(map);
                    
                     coordinates.style.display = 'none';
                    function onDragEnd() {   
                        var lngLat = marker.getLngLat();
                        coordinates.style.display = 'block';
                        coordinates.innerHTML = 'Longitude: ' + lngLat.lng + '<br />Latitude: ' + lngLat.lat;
                        v.customers.lng = lngLat.lng;
                        v.customers.lat = lngLat.lat;

                        // map.flyTo({center: [v.customers.lng,v.customers.lat], zoom: 17});
                    }
                    
                    marker.on('dragend', onDragEnd)
                    v.showMap = false;
                }
            },

            getGeocodeCustomerMeetUp(address){
                let v = this;
                v.loading = true;
                axios.get(`/customers-geocode-json/${address.replace(/[/#]/g, '')}`)
                .then(response => { 
                    const mapcontainer = document.getElementById("map_meet_up");
                    mapcontainer.innerHTML = '';
                    v.customers.meet_up_lat = response.data.lat;
                    v.customers.meet_up_lng = response.data.lng;
                    mapboxgl.accessToken = v.accessToken;

                    if(v.customers.meet_up_lat && v.customers.meet_up_lng){
                        v.showMapMeetUpLocation = false;
                    }

                    v.loading = false;
                    
                    var map = new mapboxgl.Map({
                        container: 'map_meet_up',
                        style: v.mapStyle,
                        center: [v.customers.meet_up_lng,v.customers.meet_up_lat],
                        minzoom:23,
                        maxZoom:20,
                        zoom: 17,
                        maxBounds: [[110.446227,2.949317], [131.509814,21.637444 ]],
                        height:100
                    });
                    
                    map.addControl(new mapboxgl.NavigationControl());
                    map.addControl(new mapboxgl.FullscreenControl());
                
                    var marker = new mapboxgl.Marker({
                        draggable: true
                    })
                    .setLngLat([v.customers.meet_up_lng,v.customers.meet_up_lat])
                    .addTo(map);
                    coordinates_meet_up.style.display = 'none';
                    function onDragEnd() {   
                        var lngLat = marker.getLngLat();
                        coordinates_meet_up.style.display = 'block';
                        coordinates_meet_up.innerHTML = 'Longitude: ' + lngLat.lng + '<br />Latitude: ' + lngLat.lat;
                        v.customers.meet_up_lng = lngLat.lng;
                        v.customers.meet_up_lat = lngLat.lat;

                        // map.flyTo({center: [v.customers.lng,v.customers.lat], zoom: 17});
                    }
                    
                    marker.on('dragend', onDragEnd)
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                })
            },

            showEditMapMeetUp(lat,lng){
                if(lat && lng){
                    let v = this;
                    const mapcontainer = document.getElementById("map_meet_up");
                    mapcontainer.innerHTML = '';
                    mapboxgl.accessToken = v.accessToken;
                    var map = new mapboxgl.Map({
                        container: 'map_meet_up',
                        style: v.mapStyle,
                        center: [v.customers.meet_up_lng,v.customers.meet_up_lat],
                        minzoom:23,
                        maxZoom:20,
                        zoom: 17,
                        maxBounds: [[110.446227,2.949317], [131.509814,21.637444 ]],
                        height:100
                    });
                    
                    map.addControl(new mapboxgl.NavigationControl());
                    map.addControl(new mapboxgl.FullscreenControl());
                
                    var marker = new mapboxgl.Marker({
                        draggable: true
                    })
                    .setLngLat([v.customers.meet_up_lng,v.customers.meet_up_lat])
                    .addTo(map);
                    
                    coordinates_meet_up.style.display = 'none';
                    function onDragEnd() {   
                        var lngLat = marker.getLngLat();
                        coordinates_meet_up.style.display = 'block';
                        coordinates_meet_up.innerHTML = 'Longitude: ' + lngLat.lng + '<br />Latitude: ' + lngLat.lat;
                        v.customers.meet_up_lng = lngLat.lng;
                        v.customers.meet_up_lat = lngLat.lat;
                    }
                    
                    marker.on('dragend', onDragEnd)
                    v.showMapMeetUpLocation = false;
                }
            },

            disableCustomerCode(){
                if(this.customers.status == 3){
                    this.disabledCustomerCode = true;
                }else{
                    this.disabledCustomerCode = false;
                }
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
    /* #map { position:absolute; top:0; bottom:0; width:100%; } */
    #map_meet_up{
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

    .coordinates_meet_up {
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

    .multiselect__tags {
        min-height: 45px!important;
    }

    .multiselect__single {
        padding-top: 5px!important;
    }

</style>
