<template>
<div>
    <loader v-if="loading"></loader>
    <div class="header bg-green pb-6 pt-5 pt-md-6"></div>
    <div class="container-fluid mt--7">

            <div class="row mt-5">
                <div class="col">
                    <div class="card shadow">

                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="mb-0">Map Analytics Report - Customers</h3>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="row col-sm-12">
                                <div class="col-md-3 float-left">
                                    <div class="form-group">

                                        <label for="customerSelect" class="form-control-label">Select Classification</label> 
                                        <multiselect
                                                v-model="classificationIds"
                                                :options="classificationOptions"
                                                :multiple="true"
                                                track-by="id"
                                                :custom-label="customLabelClassification"
                                                placeholder="Select Classification"
                                                id="selected_classification"
                                        >
                                        </multiselect>
                                        <span class="text-danger small" v-if="errors.selectedClassifications">{{ errors.selectedClassifications[0] }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3 float-left">
                                    <div class="form-group">

                                        <label for="customerSelect" class="form-control-label">Select Company</label> 
                                        <multiselect
                                                v-model="companyIds"
                                                :options="companyOptions"
                                                :multiple="true"
                                                track-by="id"
                                                :custom-label="customLabelCompany"
                                                placeholder="Select Company"
                                                id="selected_company"
                                        >
                                        </multiselect>
                                        <span class="text-danger small" v-if="errors.selectedCompanies">{{ errors.selectedCompanies[0] }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3 float-left">
                                    <div class="form-group">

                                        <label for="customerSelect" class="form-control-label">Select Status</label> 
                                        <multiselect
                                                v-model="statusIds"
                                                :options="statusOptions"
                                                :multiple="true"
                                                track-by="id"
                                                :custom-label="customLabelStatus"
                                                placeholder="Select Status"
                                                id="selected_status"
                                        >
                                        </multiselect>
                                        <span class="text-danger small" v-if="errors.selectedStatuses">{{ errors.selectedStatuses[0] }}</span>

                                    </div>
                                </div>
                                <div class="col-md-3 float-left">
                                    <div class="form-group">

                                        <label for="customerSelect" class="form-control-label">Select Province</label> 
                                        <multiselect
                                                v-model="provinceId"
                                                :options="provinceOptions"
                                                :multiple="false"
                                                track-by="id"
                                                :custom-label="customLabelProvince"
                                                placeholder="Select Province"
                                                id="selected_province"
                                        >
                                        </multiselect>
                                        <span class="text-danger small" v-if="errors.selectedProvinces">{{ errors.selectedProvinces[0] }}</span>

                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-sm btn-primary" @click="getCustomerLocations"> Apply Filter</button>
                                    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#showCustomerList"> Show List ({{ Object.keys(customersList).length }})</button>
                                </div>

                            </div>

                            <div class="row ml-3 mt-3 mb-3 pl-3">
                                <h4 class="mt-3">Legend:</h4>
                                <div class="col-sm-2">
                                    <div class="icon icon-shape bg-primary text-white rounded-circle shadow" style="font-size: .8rem!important;">{{ countActive }}</div>
                                    <span class="text-sm"> Active</span>
                                </div>
                                <div class="col-sm-2">
                                    <div class="icon icon-shape bg-success text-white rounded-circle shadow" style="font-size: .8rem!important;">{{ countProspect }}</div>
                                    <span class="text-sm"> Prospect</span>
                                </div>
                                <div class="col-sm-2">
                                    <div class="icon icon-shape bg-warning text-white rounded-circle shadow" style="font-size: .8rem!important;">{{ countInactive }}</div>
                                    <span class="text-sm"> Inactive</span>
                                </div>
                                <div class="col-sm-2">
                                    <div class="icon icon-shape bg-danger text-white rounded-circle shadow" style="font-size: .8rem!important;">{{ countBlocklist }}</div>
                                    <span class="text-sm"> Blocklisted</span>
                                </div>
                                <div class="col-sm-3 mr-2">
                                    <div class="icon icon-shape bg-default text-white rounded-circle shadow" style="font-size: .8rem!important;">{{ countDefault }}</div>
                                    <span class="text-sm">Unidentified Customer</span>
                                </div>
                            </div>

                        </div>
            
                        
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
                        /> 

                    </div>
                </div>  
            </div>

    </div>  


    <!-- Customers List Modal -->

    <div class="modal fade" id="showCustomerList" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal-title-default">Map Customer(s ) List</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="col-md-4 float-left">
                    <div class="form-group">
                        <label for="name" class="form-control-label">Search</label> 
                        <input type="text" class="form-control" placeholder="Search" v-model="keywords" id="name">
                    </div>

                    <json-excel class = "btn btn-sm btn-default" :data= "customersList" :fields = "json_fields" name= "appointment_duration_report.xls">Export to Excel</json-excel> 
                </div>
                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                        <tr>
                            <th scope="col"></th>
                            <th scope="col">No. of Visits</th>
                            <th scope="col">Customer Code</th>
                            <th scope="col">Name</th>
                            <th scope="col">Classification</th>
                            <th scope="col">Address</th>
                            <th scope="col">Town or City</th>
                            <th scope="col">Province</th>
                            <th scope="col">Telephone 1</th>
                            <th scope="col">Telephone 2</th>
                            <th scope="col">Status</th>
                        </tr>
                        </thead>
                            <tbody v-if="customersList">
                                <tr v-for="(customer, e) in filteredQueues" v-bind:key="e"> 
                                    <td class="text-right">
                                        <div class="dropdown" v-if="customer.visits.length > 0">
                                            <a class="btn btn-sm btn-icon-only text-light" href="#" role="button"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                <a class="dropdown-item" @click="customerDetailsModal(customer)">View Visits</a>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ customer.visits ? customer.visits.length : '' }}</td>
                                    <td>{{ customer.customer_code }}</td>
                                    <td>{{ customer.name }}</td>
                                    <td>{{ customer.classifications ? customer.classifications.description : '' }}</td>
                                    <td>{{ customer.google_address }}</td>
                                    <td>{{ customer.town_city }}</td>
                                    <td>{{ customer.provinces ? customer.provinces.name : '' }}</td>
                                    <td>{{ customer.telephone_1 }}</td>
                                    <td>{{ customer.telephone_2 }}</td>
                                    <td>{{ customer.statuses ? customer.statuses.description : ''  }}</td>
                                </tr>
                            </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td>No data available in the table</td>
                                    </tr>
                            </tbody>
                    </table>
                        
                </div>

                <div class="row mb-3 mt-3 ml-1" v-if="filteredQueues.length">
                    <div class="col-6 text-left">
                            <span>{{ filteredQueues.length }} Filtered Customer(s)</span><br>
                            <span>{{ Object.keys(customersList).length }} Total Customer(s)</span>
                    </div>
                    <div class="col-6 text-right">
                        
                            <nav aria-label="...">
                                <ul class="pagination justify-content-end mb-0">
                                    <li class="page-item">
                                        <button :disabled="!showPreviousLink()" class="page-link" v-on:click="setPage(currentPage - 1)"> <i class="fas fa-angle-left"></i> </button>
                                    </li>
                                    <li class="page-item">
                                        Page {{ currentPage + 1 }} of {{ totalPages }}
                                    </li>
                                    <li class="page-item">
                                        <button :disabled="!showNextLink()" class="page-link" v-on:click="setPage(currentPage + 1)"><i class="fas fa-angle-right"></i> </button>
                                    </li>
                                </ul>
                            </nav>
                    
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link  ml-auto" data-dismiss="modal">Close</button>
            </div>
            </div>
        </div>
    </div>

    <!-- Customer Details -->

      
    <div id="customer-details-modal" tabindex="1" class="customer-modal">
        <div class="customer-modal-content">
            <div class="customer-modal-header">
                <span class="customer-close" @click="closecustomerDetailsModal">&times;</span>
                <h4 class="mt-3">{{ customer.name }}</h4>
            </div>
            <div class="customer-modal-body mt-3">
                <div class="row">
                    <div class="col-md-12 mb-5">
                        <h5>CUSTOMER INFORMATION</h5>
                        <div class="col-md-12">
                            <span>Customer code: {{ customer.customer_code }}</span><br>
                            <span>Classification: {{ customer.classifications ? customer.classifications.description : "" }}</span><br>
                            <span>Status: {{ customer.statuses ? customer.statuses.description : "" }}</span><br>
                            <span>Address: {{ customer.google_address }}</span><br>
                            <span>Town or City: {{ customer.town_city }}</span><br>
                            <span>Provinces: {{ customer.provinces ? customer.provinces.name : "" }}</span><br>
                            <span>Telephone 1: {{ customer.telephone_1 }}</span><br>
                            <span>Telephone 2: {{ customer.telephone_2 }}</span><br>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 float-left">
                    <div class="form-group">
                        <label for="visit_keywords" class="form-control-label">Search</label> 
                        <input type="text" class="form-control" placeholder="Search" v-model="visit_keywords" id="visit_keywords">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                        <tr>
                            <th scope="col"></th>
                            <th scope="col">Employee Name</th>
                            <th scope="col">SCHEDULE IN / OUT</th>
                            <th scope="col">IN / OUT</th>
                            <th scope="col">RENDERED</th>
                            <th scope="col">REMARKS</th>
                        </tr>
                        </thead>
                         <tbody v-if="customervisitList">
                            <tr v-for="(visit, e) in filteredVisitQueues" v-bind:key="e"> 
                                <td></td>
                                <td>{{ visit.user ? visit.user.name : '' }}</td>
                                <td>{{ visit.start_time }} - {{ visit.end_time }}</td>
                                <td>{{ visit.attendances ? visit.attendances.sign_in : '' }} - {{ visit.attendances ? visit.attendances.sign_out : '' }}</td>
                                <td>{{ visit.attendances ? rendered(visit.attendances.sign_out, visit.attendances.sign_in) : '' }}</td>
                                <td>{{ visit.attendances ? visit.attendances.remarks : '' }}</td>
                            </tr>    
                        </tbody>    
                    </table>
                </div>

                <div class="row mb-3 mt-3 ml-1" v-if="filteredVisitQueues.length">
                    <div class="col-6 text-left">
                            <span>{{ filteredVisitQueues.length }} Filtered Visit(s)</span><br>
                            <span>{{ Object.keys(customervisitList).length }} Total Visit(s)</span>
                    </div>
                    <div class="col-6 text-right">
                        <nav aria-label="...">
                            <ul class="pagination justify-content-end mb-0">
                                <li class="page-item">
                                    <button :disabled="!visitshowPreviousLink()" class="page-link" v-on:click="visitsetPage(visitcurrentPage - 1)"> <i class="fas fa-angle-left"></i> </button>
                                </li>
                                <li class="page-item">
                                    Page {{ visitcurrentPage + 1 }} of {{ visittotalPages }}
                                </li>
                                <li class="page-item">
                                    <button :disabled="!visitshowNextLink()" class="page-link" v-on:click="visitsetPage(visitcurrentPage + 1)"><i class="fas fa-angle-right"></i> </button>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                

            </div>
            <div class="customer-modal-footer">
        
            </div>
        </div>
    </div>

</div>  
</template>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<script>
    import moment from 'moment';
    import Multiselect from 'vue-multiselect';
    import Mapbox from 'mapbox-gl-vue';
    import mapboxgl from 'mapbox-gl';

    import JsonExcel from 'vue-json-excel'

    import XLSX from 'xlsx'; 

    export default {
        components: {
            Mapbox,
            Multiselect,
            JsonExcel
        },
        data() {
            return {
                // accessToken: 'pk.eyJ1IjoiY2hhcmxpZWRvdGF1IiwiYSI6ImNpazlpdzh0ZTA5d3Z2Y200emhqbml1OGEifQ.uoA6t5rO18m0BgNGPXsm5A',
                accessToken: 'pk.eyJ1IjoiamF5LWx1bWFnZG9uZzEyMyIsImEiOiJjazFxNm5wZGwxNG02M2dtaXF2dHE1YzluIn0.SHUJTfNTrhGoyacA8H7Tbw',
                mapStyle: 'mapbox://styles/mapbox/streets-v11',
                mapCenter: [121.035249, 14.675647],
                mapDefaultLayer: [],
                classificationIds:[],
                statusIds:[],
                provinceId:[],
                companyIds:[],
                classificationOptions:[],
                companyOptions:[],
                statusOptions:[],
                provinceOptions:[],
                customers : [],
                customersList : [],
                customer : [],
                customervisitList : [],
                customer_visits : [],
                usersList : [],
                keywords : '',
                currentPage: 0,
                itemsPerPage: 10,
                visit_keywords : '',
                visitcurrentPage: 0,
                visititemsPerPage: 10,
                loading: false,
                errors: [],
                countActive: 0,
                countProspect: 0,
                countInactive: 0,
                countBlocklist: 0,
                countDefault: 0,
                loading: false,
                json_fields : {
                    'NO. OF VISITS' : {
                        callback: (value) => {
                            return value.visits.length;
                        }
                    },
                    // 'CUSTOMER CODE' : '',
                    // 'NAME' : ''
                }
            };
        },
        created(){
             this.mapbox = Mapbox;
             this.fetchClassification();
             this.fetchCompany();
             this.fetchStatus();
             this.fetchProvince();
        },
        methods:{
            exportxlsx(){
                var tbl = document.getElementById('table-0');
                var wb = XLSX.utils.table_to_book(tbl);
            },
            customerDetailsModal(customer_details){
                this.customervisitList = [];
                var modal = document.getElementById('customer-details-modal');
                modal.style.display = "block";
                this.customer = customer_details;
                this.fetchCustomerVisits(customer_details.customer_code);
            },
            closecustomerDetailsModal(){
                var modal = document.getElementById('customer-details-modal');
                modal.style.display = "none";
            },
            fetchCustomerVisits(customer_code){
                axios.get('/customer-visits/' + customer_code)
                .then(response => { 
                    this.customer_visits = response.data ? response.data : [];
                    this.customervisitList = Object.assign({},  this.customer_visits);
                })
                .catch(error =>{
                    this.errors = error.response.data.errors;
                })
            },
            getCustomerLocations(){
                this.loading = true;
                this.errors = [];
                axios.post('/customer-locations', {
                    selectedClassifications: this.classificationIds,
                    selectedCompanies: this.companyIds,
                    selectedStatuses: this.statusIds,
                    selectedProvince: this.provinceId,
                })
                .then(response =>{
                    this.customers = response.data ? response.data : [];
                    this.customersList = Object.assign({},  this.customers);
                    this.createCustomersMap();
                })
                .catch(error => {
                    this.loading = false;
                    this.errors = error.response.data.errors;
                    if(this.errors.startDate[0] || this.errors.endDate[0]){
                        return this.errors;
                    }else{
                        alert('Unable to load Map. Please try again.');
                        window.location.reload();
                    }
                });
            },
            fetchClassification(){
                axios.get('/customers-classification-options')
                .then(response => { 
                    this.classificationOptions = response.data;
                })
                .catch(error =>{
                    this.errors = error.response.data.errors;
                })
            },
            fetchCompany(){
                axios.get('/companies-all')
                .then(response => { 
                    this.companyOptions = response.data;
                })
                .catch(error =>{
                    this.errors = error.response.data.errors;
                })
            },
            fetchStatus(){
                axios.get('/customers-status-options')
                .then(response => { 
                    this.statusOptions = response.data;
                })
                .catch(error =>{
                    this.errors = error.response.data.errors;
                })
            },
            fetchProvince(){
                axios.get('/provinces')
                .then(response => { 
                    this.provinceOptions = response.data;
                })
                .catch(error =>{
                    this.errors = error.response.data.errors;
                })
            },
            customLabelClassification (classification) {
                return `${classification.description}`
            },
            customLabelCompany (company) {
                return `${company.name}`
            },
            customLabelStatus (status) {
                return `${status.description}`
            },
            customLabelProvince (province) {
                return `${province.name}`
            },
            createCustomersMap(){
                let v = this;
                v.countActive= 0,
                v.countProspect= 0,
                v.countInactive= 0,
                v.countBlocklist= 0,
                v.countDefault= 0,
                
                document.getElementById('map').innerHTML = "";

                v.customerCoordinates = [];
                mapboxgl.accessToken = this.accessToken;
                var map = new mapboxgl.Map({
                    container: 'map',
                    style: this.mapStyle,
                    center: this.mapCenter,
                    minzoom:23,
                    maxZoom:20,
                    zoom: 3,
                    maxBounds: [[110.446227,2.949317], [131.509814,21.637444 ]]
                });

                map.addControl(new mapboxgl.NavigationControl());
                map.addControl(new mapboxgl.FullscreenControl());

                
                
                //Re center Map when Selected Province
                if(v.provinceId){
                    var current_zoom = map.getZoom();
                    var province_data = [];
                    axios.get('/customers-geocode-json/' + v.provinceId.name + ' philippines')
                    .then(response => { 
                        province_data = response.data;
                        if(province_data.lng && province_data.lat){
                            map.flyTo({center: [province_data.lng,province_data.lat],zoom: current_zoom > 10 ? current_zoom : 10});
                        }
                    })
                    .catch(error =>{
                        console.log(error.response.data);
                    })
                    
                }
                

                const geojson = JSON.parse(JSON.stringify(this.customers));

                if(geojson){
                    geojson.forEach((marker) => {
                        
                        var lng = 0;
                        var lat = 0;     
                        var classification = "";     
                        
                        if(marker.lng && marker.lng){
                            var lng = marker.lng ? marker.lng : 0;
                            var lat = marker.lat ? marker.lat : 0;
                        }
                        if(marker.classifications){
                            if(marker.classifications.id == '1' || marker.classifications.id == '2' || marker.classifications.id == '3' || marker.classifications.id == '4'){
                                classification = "Unidentified Customer";
                            }else{
                                classification = marker.classifications.description;
                            }
                            
                        }

                        const el = document.createElement('div')
                        el.id = 'customer-marker' + marker.id 
                        el.title = 'Name: ' + marker.name + '\nAddress: ' + marker.google_address + '\nClassification: ' + classification;
                        el.className = v.checkClassification(marker.classification,marker.status);

                        

                        new mapboxgl.Marker(el)
                        .setLngLat([lng,lat])
                        .addTo(map)

                        el.addEventListener('click', e => {
                            e.stopPropagation();

                            let disabled='';
                            if(marker.visits){
                                if(marker.visits.length > 0){
                                    disabled = '<button id="customer-'+ marker.id +'" class="btn btn-outline-primary btn-sm" '+ disabled +'> '+ marker.visits.length +' Visit(s) </button>'; 
                                }else{
                                    disabled = '<button id="customer-'+ marker.id +'" class="btn btn-outline-primary btn-sm" '+ disabled +'> View Details </button>';
                                }
                            }

                            new mapboxgl.Popup()
                            .setLngLat([lng,lat])
                            .setHTML('<div class="text-center"><h4 class="map-pop-up-text mb-0">' + marker.name + '</h4><h5>'+marker.town_city +'</h5>'+disabled+'</div>')
                            .addTo(map);

                            const view_details = document.getElementById('customer-'+ marker.id)   

                            view_details.addEventListener('click', e => {
                                e.stopPropagation();
                                v.customerDetailsModal(marker);
                                // alert(marker.name);
                            });

                        }, true);        

                    });
                     this.loading = false;
                }
            },
            checkClassification(classification_id,status){
                let v=this;
                //Change Name
                if(classification_id){
                    
                    if(classification_id == '1' || classification_id == '2' || classification_id == '3' || classification_id == '4' || classification_id == '15'){
                        
                        v.countDefault +=1;
                        return 'default-pin';

                    }else{

                        //Active
                        if(classification_id == '14'){
                            if(status == '1'){
                                v.countActive +=1
                                return 'active-change-name';
                            }
                            else if(status == '3'){
                                v.countProspect +=1;
                                return 'prospect-change-name';
                            }
                            else if(status == '2'){
                                v.countInactive +=1;
                                return 'inactive-change-name';
                            }
                            else if(status == '4'){
                                v.countBlocklist +=1;
                                return 'blocklist-change-name';
                            }else{
                                v.countDefault +=1;
                                return 'default-change-name';
                            }
                        }

                        //Trucking
                        if(classification_id == '13'){
                        
                            if(status == '1'){
                                v.countActive +=1;
                                return 'active-trucking';
                            }
                            else if(status == '3'){
                                v.countProspect +=1;
                                return 'prospect-trucking';
                            }
                            else if(status == '2'){
                                v.countInactive +=1
                                return 'inactive-trucking';
                            }
                            else if(status == '4'){
                                v.countBlocklist +=1;
                                return 'blocklist-trucking';
                            }else{
                                v.countDefault +=1;
                                return 'default-trucking';
                            }
                        }
                        
                        //Shipping
                        if(classification_id == '12'){
                            if(status == '1'){
                                v.countActive +=1;
                                return 'active-shipping';
                            }
                            else if(status == '3'){
                                v.countProspect +=1;
                                return 'prospect-shipping';
                            }
                            else if(status == '2'){
                                v.countInactive +=1
                                return 'inactive-shipping';
                            }
                            else if(status == '4'){
                                v.countBlocklist +=1;
                                return 'blocklist-shipping';
                            }else{
                                v.countDefault +=1;
                                return 'default-shipping';
                            }
                        }

                        //Hauler
                        if(classification_id == '11'){
                            if(status == '1'){
                                v.countActive +=1;
                                return 'active-hauler';
                            }
                            else if(status == '3'){
                                v.countProspect +=1;
                                return 'prospect-hauler';
                            }
                            else if(status == '2'){
                                v.countInactive +=1;
                                return 'inactive-hauler';
                            }
                            else if(status == '4'){
                                v.countBlocklist +=1;
                                return 'blocklist-hauler';
                            }else{
                                v.countDefault +=1;
                                return 'default-hauler';
                            }
                        }

                        //Direct
                        if(classification_id == '10'){
                            if(status == '1'){
                                v.countActive +=1;
                                return 'active-direct';
                            }
                            else if(status == '3'){
                                v.countProspect +=1;
                                return 'prospect-direct';
                            }
                            else if(status == '2'){
                                v.countInactive +=1;
                                return 'inactive-direct';
                            }
                            else if(status == '4'){
                                v.countBlocklist +=1;
                                return 'blocklist-direct';
                            }else{
                                v.countDefault +=1;
                                return 'default-direct';
                            }
                        }

                        //Enduser of sub-dealer
                        if(classification_id == '9'){
                            if(status == '1'){
                                v.countActive +=1;
                                return 'active-end-user-subdealer';
                            }
                            else if(status == '3'){
                                v.countProspect +=1;
                                return 'prospect-end-user-subdealer';
                            }
                            else if(status == '2'){
                                v.countInactive +=1;
                                return 'inactive-end-user-subdealer';
                            }
                            else if(status == '4'){
                                v.countBlocklist +=1;
                                return 'blocklist-end-user-subdealer';
                            }else{
                                v.countDefault +=1;
                                return 'default-end-user-subdealer';
                            }
                        }

                        //Enduser of Distributor/dealer
                        if(classification_id == '8'){
                            if(status == '1'){
                                v.countActive +=1;
                                return 'active-end-user-distributor';
                            }
                            else if(status == '3'){
                                v.countProspect +=1;
                                return 'prospect-end-user-distributor';
                            }
                            else if(status == '2'){
                                v.countInactive +=1;
                                return 'inactive-end-user-distributor';
                            }
                            else if(status == '4'){
                                v.countBlocklist +=1;
                                return 'blocklist-end-user-distributor';
                            }else{
                                v.countDefault +=1;
                                return 'default-end-user-distributor';
                            }
                        }

                        //LFUGGOC-Office
                        if(classification_id == '6'){
                            if(status == '1'){
                                v.countActive +=1;
                                return 'active-office';
                            }
                            else if(status == '3'){
                                v.countProspect +=1;
                                return 'prospect-office';
                            }
                            else if(status == '2'){
                                v.countInactive +=1;
                                return 'inactive-office';
                            }
                            else if(status == '4'){
                                v.countBlocklist +=1
                                return 'blocklist-office';
                            }else{
                                v.countDefault +=1;
                                return 'default-office';
                            }
                        }

                        //Sub-Dealer
                        if(classification_id == '5'){
                            if(status == '1'){
                                v.countActive +=1;
                                return 'active-subdealer';
                            }
                            else if(status == '3'){
                                v.countProspect +=1;
                                return 'prospect-subdealer';
                            }
                            else if(status == '2'){
                                v.countInactive +=1;
                                return 'inactive-subdealer';
                            }
                            else if(status == '4'){
                                v.countBlocklist +=1;
                                return 'blocklist-subdealer';
                            }else{  
                                v.countDefault +=1;
                                return 'default-subdealer';
                            }
                        }
                    }
                   
                } else {
                    v.countDefault +=1;
                    return 'default-pin';

                }

            },

             rendered(endTime, startTime){ 
                if(endTime && startTime){
                    var ms = moment(endTime,"YYYY/MM/DD HH:mm a").diff(moment(startTime,"YYYY/MM/DD HH:mm a"));
                    var d = moment.duration(ms);
                    var hours = Math.floor(d.asHours());
                    var minutes = moment.utc(ms).format("mm");
                    return hours + 'h '+ minutes+' min.'; 
                }else{
                    return '-';
                }                                  
            },

            setPage(pageNumber) {
                this.currentPage = pageNumber;
            },

            resetStartRow() {
                this.currentPage = 0;
            },

            showPreviousLink() {
                return this.currentPage == 0 ? false : true;
            },
            showNextLink() {
                return this.currentPage == (this.totalPages - 1) ? false : true;
            },
            visitsetPage(pageNumber) {
                this.visitcurrentPage = pageNumber;
            },
            visitresetStartRow() {
                this.visitcurrentPage = 0;
            },
            visitshowPreviousLink() {
                return this.visitcurrentPage == 0 ? false : true;
            },
            visitshowNextLink() {
                return this.visitcurrentPage == (this.visittotalPages - 1) ? false : true;
            }
        },
        computed:{
            filteredCustomers(){
                let self = this;
                return Object.values(self.customersList).filter(customer => {
                    if(customer){
                       return customer.name.toLowerCase().includes(this.keywords.toLowerCase())
                    }
                });
            },
            filteredVisits(){
                let self = this;
                return Object.values(self.customervisitList).filter(customer_visit => {
                    if(customer_visit.user){
                        return customer_visit.code.toLowerCase().includes(this.visit_keywords.toLowerCase()) || customer_visit.user.name.toLowerCase().includes(this.visit_keywords.toLowerCase())
                    }else{
                        return customer_visit.code.toLowerCase().includes(this.visit_keywords.toLowerCase()) 
                    }
                });
            },
            totalPages() {
                return Math.ceil(Object.values(this.filteredCustomers).length / this.itemsPerPage)
            },
            visittotalPages() {
                return Math.ceil(Object.values(this.filteredVisits).length / this.visititemsPerPage)
            },
            filteredQueues() {
                var index = this.currentPage * this.itemsPerPage;
                var queues_array = Object.values(this.filteredCustomers).slice(index, index + this.itemsPerPage);
                if(this.currentPage >= this.totalPages) {
                    this.currentPage = this.totalPages - 1
                }
                if(this.currentPage == -1) {
                    this.currentPage = 0;
                }
                return queues_array;
            },
            filteredVisitQueues() {
                var index = this.visitcurrentPage * this.visititemsPerPage;
                var queues_array = Object.values(this.filteredVisits).slice(index, index + this.visititemsPerPage);
                if(this.visitcurrentPage >= this.visittotalPages) {
                    this.visitcurrentPage = this.visittotalPages - 1
                }
                if(this.visitcurrentPage == -1) {
                    this.visitcurrentPage = 0;
                }
                return queues_array;
            }
        }
    }
</script>

<style>
    #map{
        height: 1100px;
        width: 100%;
        top: 10%;
        background-color:#75CFF0!important;
    }

    .mapboxgl-ctrl-logo{
        display:none!important;
    }
    .mapboxgl-ctrl.mapboxgl-ctrl-attrib{
        display:none!important;
    }
    .text-white{
        color:white!important;
    }

    .modal-xl{
        max-width: 1140px!important;
    }


    /* Customer Details */

    /* The Modal (background) */
    .customer-modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 10000;
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        
    }

    /* The Close Button */
    .customer-close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    }

    .customer-close:hover,
    .customer-close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
    }

    /* Modal Header */
    .customer-modal-header {
        padding: 2px 16px;
        background-color: #fefefe;
        color: white;
        border: 0 solid rgba(0, 0, 0, .2);
        border-radius: .4375rem;
    }

    /* Modal Body */
    .customer-modal-body {padding: 2px 16px;}

    /* Modal Footer */
    .customer-modal-footer {
        padding: 2px 16px;
        background-color: #fefefe;
        color: white;
        border: 0 solid rgba(0, 0, 0, .2);
        border-radius: .4375rem;
    }

    /* Modal Content */
    .customer-modal-content {
        position: relative;
        background-color: #fefefe;
        margin: 5% auto;
        padding: 0;
        width: 80%;
        box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
        animation-name: animatetop;
        animation-duration: 0.4s;
        border: 0 solid rgba(0, 0, 0, .2);
        border-radius: .4375rem;
    }

    /* Add Animation */
    @keyframes animatetop {
    from {top: -300px; opacity: 0}
    to {top: 0; opacity: 1}
    }

    /* Active */
    .active-change-name {
        background-image: url('/img/map/active/change_name.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .active-direct {
        background-image: url('/img/map/active/direct.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .active-end-user-distributor {
        background-image: url('/img/map/active/end_user_distributor.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .active-end-user-subdealer {
        background-image: url('/img/map/active/end_user_subdealer.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .active-hauler {
        background-image: url('/img/map/active/hauler.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .active-office {
        background-image: url('/img/map/active/office.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .active-shipping {
        background-image: url('/img/map/active/shipping.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .active-subdealer {
        background-image: url('/img/map/active/subdealer.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .active-trucking {
        background-image: url('/img/map/active/trucking.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }

    /* Prospect */
    .prospect-change-name {
        background-image: url('/img/map/prospect/change_name.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .prospect-direct {
        background-image: url('/img/map/prospect/direct.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .prospect-end-user-distributor {
        background-image: url('/img/map/prospect/end_user_distributor.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .prospect-end-user-subdealer {
        background-image: url('/img/map/prospect/end_user_subdealer.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .prospect-hauler {
        background-image: url('/img/map/prospect/hauler.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .prospect-office {
        background-image: url('/img/map/prospect/office.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .prospect-shipping {
        background-image: url('/img/map/prospect/shipping.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .prospect-subdealer {
        background-image: url('/img/map/prospect/subdealer.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .prospect-trucking {
        background-image: url('/img/map/prospect/trucking.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }

    /* Inactive */
    .inactive-change-name {
        background-image: url('/img/map/inactive/change_name.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .inactive-direct {
        background-image: url('/img/map/inactive/direct.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .inactive-end-user-distributor {
        background-image: url('/img/map/inactive/end_user_distributor.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .inactive-end-user-subdealer {
        background-image: url('/img/map/inactive/end_user_subdealer.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .inactive-hauler {
        background-image: url('/img/map/inactive/hauler.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .inactive-office {
        background-image: url('/img/map/inactive/office.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .inactive-shipping {
        background-image: url('/img/map/inactive/shipping.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .inactive-subdealer {
        background-image: url('/img/map/inactive/subdealer.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .inactive-trucking {
        background-image: url('/img/map/inactive/trucking.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }

    /* Blocklist */
    .blocklist-change-name {
        background-image: url('/img/map/blocklist/change_name.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .blocklist-direct {
        background-image: url('/img/map/blocklist/direct.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .blocklist-end-user-distributor {
        background-image: url('/img/map/blocklist/end_user_distributor.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .blocklist-end-user-subdealer {
        background-image: url('/img/map/blocklist/end_user_subdealer.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .blocklist-hauler {
        background-image: url('/img/map/blocklist/hauler.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .blocklist-office {
        background-image: url('/img/map/blocklist/office.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .blocklist-shipping {
        background-image: url('/img/map/blocklist/shipping.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .blocklist-subdealer {
        background-image: url('/img/map/blocklist/subdealer.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .blocklist-trucking {
        background-image: url('/img/map/blocklist/trucking.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }


    /* Default */
    .default-change-name {
        background-image: url('/img/map/default/change_name.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .default-direct {
        background-image: url('/img/map/default/direct.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .default-end-user-distributor {
        background-image: url('/img/map/default/end_user_distributor.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .default-end-user-subdealer {
        background-image: url('/img/map/default/end_user_subdealer.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .default-hauler {
        background-image: url('/img/map/default/hauler.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .default-office {
        background-image: url('/img/map/default/office.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .default-shipping {
        background-image: url('/img/map/default/shipping.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .default-subdealer {
        background-image: url('/img/map/default/subdealer.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
    .default-trucking {
        background-image: url('/img/map/default/trucking.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }

    .default-pin {
        background-image: url('/img/map/default/default.png');background-size: cover;width: 35px;height: 35px;cursor: pointer;
    }
</style>