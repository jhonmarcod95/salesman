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
                                                <input type="text" id="customer_code" class="form-control form-control-alternative" v-model="customers.customer_code">
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
                                                <select class="form-control" v-model="customers.classification" @change="checkCustomerCode">
                                                    <option v-for="(classification, c) in classifications" v-bind:key="c" :value="classification.id">{{ classification.description}}</option>
                                                </select>
                                                <span class="text-danger small" v-if="errors.classification">{{ errors.classification[0] }}</span>
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
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="google_address">Google Map Address</label>
                                                <input id="google_address" class="form-control form-control-alternative" type="text" v-model="customers.google_address" placeholder="Enter a Location">
                                                <span class="text-danger small" v-if="errors.google_address">{{ errors.google_address[0] }}</span>
                                                
                                                <button type="button" :disabled="showMap" class="btn btn-sm btn-primary mt-2" data-toggle="modal" data-target="#showMap">Show Map</button>
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
    </div>
</template>

<script>
    import Mapbox from 'mapbox-gl-vue';
    import mapboxgl from 'mapbox-gl';
    export default {
       components: {
            Mapbox
       },
       props: ['customerId'],
        data(){
            return{
                customers: [],
                provinces: [],
                classifications:[],
                regions:[],
                errors: [],
                default_code: '',
                default_classification: '',
                show: false,
                pilili_code: '',
                lat:'',
                lng:'',
                accessToken: 'pk.eyJ1IjoiamF5LWx1bWFnZG9uZzEyMyIsImEiOiJjazFxNm5wZGwxNG02M2dtaXF2dHE1YzluIn0.SHUJTfNTrhGoyacA8H7Tbw',
                mapStyle: 'mapbox://styles/mapbox/streets-v11',
                mapCenter: [121.035249, 14.675647],
                showMap:true,
            }
        },
        created(){
            this.mapbox = Mapbox;
            this.fetchRegion();
            this.fetchProvince();
            this.fetchCustomer();
            this.fetchClassification();
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
                vm.getGeocodeCustomer(vm.customers.google_address);
                vm.showMap = false;
                

            });
        },
        methods:{
            updateCustomer(customers){  
                axios.patch(`/customers/${customers.id}`,{
                    classification : customers.classification,
                    customer_code : customers.customer_code,
                    group: customers.group,
                    name: customers.name,
                    street: customers.street,
                    town_city: customers.town_city,
                    region: customers.region,
                    province: customers.province_id,
                    google_address: customers.google_address,
                    lat: this.customers.lat,
                    lng: this.customers.lng,
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
                    this.default_code = this.customers.customer_code;
                    this.default_classification = this.customers.classification;
                    if(this.customers.classification != 1 && this.customers.classification != 2){
                        document.getElementById("customer_code").disabled = true;
                    }else{
                        document.getElementById("customer_code").disabled = false;
                    }
                    this.showEditMap(this.customers.lat,this.customers.lng);
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
                            document.getElementById("customer_code").disabled = true;
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
                            document.getElementById("customer_code").disabled = false;
                        })
                        .catch(error => {
                            this.errors = error.response.data.errors;
                        })
                    }else if(this.default_classification != 8 && this.customers.classification != 1 && this.customers.classification != 2){
                        this.show = false;
                        this.customers.customer_code = this.default_code;
                        document.getElementById("customer_code").disabled = true;
                    }else if(this.default_classification == 8 && this.customers.classification == 8){
                        this.show = false;
                        this.customers.customer_code = this.default_code;
                        document.getElementById("customer_code").disabled = true;
                    }
                    else{
                        this.show = false;
                        this.customers.customer_code = '';
                        document.getElementById("customer_code").disabled = false;
                    }

                }else{
                    if(this.customers.classification != 1 && this.customers.classification != 2){
                        this.customers.customer_code = this.default_code;
                        document.getElementById("customer_code").disabled = true;
                    }else{
                        this.customers.customer_code = '';
                        document.getElementById("customer_code").disabled = false;
                    }

                }
            },
            getGeocodeCustomer(address){
                let v = this;
                axios.get(`/customers-geocode-json/${address.replace(/[/#]/g, '')}`)
                .then(response => { 
                    const mapcontainer = document.getElementById("map");
                    mapcontainer.innerHTML = '';
                    v.customers.lat = response.data.lat;
                    v.customers.lng = response.data.lng;
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

                        map.flyTo({center: [v.customers.lng,v.customers.lat], zoom: 17});
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

                        map.flyTo({center: [v.customers.lng,v.customers.lat], zoom: 17});
                    }
                    
                    marker.on('dragend', onDragEnd)
                    v.showMap = false;
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
