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
                            <div class="row ml-2">
                                <div class="col-md-6 float-left">
                                    <div class="form-group">
                                        <label for="customerSelect" class="form-control-label">Customer</label> 
                                        <multiselect
                                                v-model="customerSelect"
                                                :options="customerOptions"
                                                :multiple="true"
                                                track-by="id"
                                                :custom-label="customLabelCustomer"
                                                :max="5"
                                                placeholder="Select Customer"
                                                id="selected_customer"
                                        >
                                        </multiselect>
                                    </div>
                                </div>

                                <div class="col-md-2 float-left">
                                    <div class="form-group">
                                        <label for="selectMonth" class="form-control-label">Select Month</label> 
                                        <multiselect
                                                v-model="selectMonth"
                                                :options="monthOptions"
                                                :multiple="false"
                                                track-by="id"
                                                :custom-label="customLabelMonth"
                                                placeholder="Select Month"
                                                id="selected_month"
                                            >
                                        </multiselect>
                                    </div>
                                </div>

                                <div class="col-md-2 float-left">
                                    <div class="form-group">
                                        <label for="selectYear" class="form-control-label">Select Year</label> 
                                        <multiselect
                                                v-model="selectYear"
                                                :options="yearOptions"
                                                :multiple="false"
                                                track-by="id"
                                                :custom-label="customLabelYear"
                                                placeholder="Select Year"
                                                id="selected_year"
                                            >
                                        </multiselect>
                                    </div>
                                </div>


                                <div class="col-md-2">
                                    <button class="btn btn-sm btn-primary" @click="getUsers"> Filter</button>
                                </div>
                            </div>
                        </div>
                 
                        <div class="col-12 mb-3">
                            <Mapbox 
                                :accessToken="accessToken" 
                                :map-options="{
                                    style: mapStyle,
                                    center: [121.035249, 14.675647], // starting position
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
        </div>

        

        <!-- Customer Users List Modal -->
        <div class="modal fade" id="showUserList" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title-default">User's List</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h1 class="text-center" style="color:#5e72e4">{{ customer.name }}</h1>
                    <p class="description text-center">{{ customer.google_address }}</p>

                    <div class="col-md-4 float-left">
                        <div class="form-group">
                            <label for="name" class="form-control-label">Search</label> 
                            <input type="text" class="form-control" placeholder="Search" v-model="keywords" id="name">
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                            <tr>
                                <th scope="col">Image In - Out</th>
                                <th scope="col">Date</th>
                                <th scope="col">Name</th>
                                <th scope="col">Schedule Sign In - Out</th>
                                <th scope="col">Sign In - Out</th>
                                <th scope="col">Remarks</th>
                                <th scope="col">Status</th>
                            </tr>
                            </thead>
                                <tbody v-if="usersList">
                                    <tr v-for="(user, e) in filteredQueues" v-bind:key="e"> 
                                        <td>
                                            <div class="row" style="width:105px!important;">
                                                <div class="col-sm-6">
                                                    <img id="sign-in-image" class="image-modal-list-thumb img-center" :src="'/storage/attendances/' + user.sign_in_image" @error="imageLoadError" alt="Sign In Image"  data-toggle="modal" data-target="#showUserImage" @click="imageModal('/storage/attendances/' + user.sign_in_image, 'Sign in image')">
                                                </div>
                                                <div class="col-sm-6">
                                                    <img id="sign-out-image" class="image-modal-list-thumb img-center" :src="'/storage/attendances/' + user.sign_out_image" @error="imageLoadError" alt="Sign Out Image"  data-toggle="modal" data-target="#showUserImage" @click="imageModal('/storage/attendances/' + user.sign_out_image, 'Sign out image')">
                                                </div>
                                             </div>
                                        </td> 
                                        <td>{{ user.schedule_date }}</td>
                                        <td>{{ user.name }}</td>
                                        <td>{{ user.schedule_time }}</td>
                                        <td>{{ user.sign_in_out }}</td>
                                        <td>{{ user.remarks }}</td>
                                        <td>
                                            <span class="badge badge-pill badge-success text-uppercase mb-3" v-if="user.status == 'inside'">{{ user.status }}</span>
                                            <span class="badge badge-pill badge-danger text-uppercase mb-3" v-else-if="user.status == 'outside'">{{ user.status }}</span>
                                        </td>
                                        
                                    </tr>
                                </tbody>
                                 <tbody v-else>
                                       <tr>
                                           <td>No data available in the table</td>
                                       </tr>
                                </tbody>
                        </table>
                         
                    </div>

                    <div class="row mb-3 mt-3 ml-1" v-if="filteredQueues.length ">
                        <div class="col-6 text-left">
                                <span>{{ filteredQueues.length }} Filtered User(s)</span><br>
                                <span>{{ Object.keys(usersList).length }} Total User(s)</span>
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


        <!-- User Modal -->
        <div class="modal fade" id="showUserInfo" tabindex="2" role="dialog" aria-labelledby="exampleModalLabel1" aria-hidden="true" data-backdrop="true">
            <span class="closed" data-dismiss="modal">&times;</span>
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header border-bottom">
                        <h4 class="modal-title" id="modal-title-default">User Details</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="px-4">
                            <div class="text-center mt-2">
                                <h1 style="color:#5e72e4">{{ user.name }}</h1>
                                <span class="badge badge-pill badge-success text-uppercase mb-3" v-if="userCheckStatus == 'inside'">{{ userCheckStatus }}</span>
                                <span class="badge badge-pill badge-danger text-uppercase mb-3" v-else-if="userCheckStatus == 'outside'">{{ userCheckStatus }}</span>
                                <div class="h4 font-weight-500"><i class="fas fa-clock" style="color:green" title="Sign in"></i> {{ user.sign_in }} - <i class="fas fa-clock" style="color:orange" title="Sign out"></i> {{ user.sign_out }}</div>
                            </div>

                            <div class="mt-3 py-3 border pl-1 pr-1" v-if="user.remarks">
                                <h4 class="text-left">SCHEDULE</h4>
                                <div class="row justify-content-center text-center">
                                    <div class="col-lg-12">
                                        <h4 style="color:#2DCE89">{{ user.schedule_name }}</h4>
                                        <p class="description">{{ user.schedule_address }}</p>
                                        <div class="h4 font-weight-500"><i class="fas fa-calendar" style="color:blue" title="Date"></i> {{ user.schedule_date }}</div>
                                        <div class="h4 font-weight-500"><i class="fas fa-clock" style="color:green" title="Start Time"></i> {{ user.schedule_start_time }} - <i class="fas fa-clock" style="color:orange" title="End Time"></i> {{ user.schedule_end_time }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="py-3" v-if="user.remarks">
                                <h4 class="text-left">REMARKS</h4>
                                <div class="row justify-content-center text-center">
                                    <div class="col-lg-12">
                                        <p>{{ user.remarks }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-4">
                                    <h4 class="text-center">Sign in image</h4>  
                                    <img id="sign-in-image" class="image-modal-thumb img-center" :src="'/storage/attendances/' + user.sign_in_image" @error="imageLoadError" alt="Sign In Image"  @click="imageModal('/storage/attendances/' + user.sign_in_image, 'Sign in image')">   
                                </div>
                                <div class="col-4">
                                    <h4 class="text-center">Sign out image</h4>
                                    <img id="sign-out-image" class="image-modal-thumb img-center"  :src="'/storage/attendances/' + user.sign_out_image" @error="imageLoadError" alt="Sign Out Image"  @click="imageModal('/storage/attendances/' + user.sign_out_image, 'Sign out image')">     
                                </div>
                            </div>

                            
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-link  ml-auto" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- The Modal -->
        <div id="showImageModal" tabindex="1" class="imageModal">
            <span class="closeImage" @click="closeImageModal">&times;</span>
            <img class="modal-content2" :src="imageModalSrc" @error="imageLoadError" alt="Image" id="imgModal">
            <div id="caption">{{ imageModalTitle }}</div>
        </div>
        
        </div>
 

       
</template>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<script>
    import loader from '../Loader';
    import Multiselect from 'vue-multiselect';
    import Mapbox from 'mapbox-gl-vue';
    import mapboxgl from 'mapbox-gl';
    import vSelect from 'vue-select'
    export default {
        components: {
            Mapbox,Multiselect,vSelect
        },
        data() {
            return {
                // accessToken: 'pk.eyJ1IjoiY2hhcmxpZWRvdGF1IiwiYSI6ImNpazlpdzh0ZTA5d3Z2Y200emhqbml1OGEifQ.uoA6t5rO18m0BgNGPXsm5A',
                accessToken: 'pk.eyJ1IjoiamF5LWx1bWFnZG9uZzEyMyIsImEiOiJjazFxNm5wZGwxNG02M2dtaXF2dHE1YzluIn0.SHUJTfNTrhGoyacA8H7Tbw',
                mapStyle: 'mapbox://styles/mapbox/outdoors-v10',
                mapDefaultLayer: [],
                selectedProvinces: [],
                customerCoordinates:[],
                customerOptions:[],
                customerSelect:[],
                customer:[],
                users:[],
                user:[],
                userCoordinates:[],
                userCheckStatus:'',
                startDate:'',
                endDate:'',
                errors: [],
                error: '',
                loading: false,
                monthOptions:[],
                yearOptions:[],
                selectMonth:'',
                selectYear:'',
                imageModalSrc : '',
                imageModalTitle : '',
                usersList : [],
                keywords : '',
                currentPage: 0,
                itemsPerPage: 10,
            };
        },
        created() {
            this.mapbox = Mapbox;
            this.fetchCustomers();
            this.getMonths();
            this.getYears();
        },
        methods: {
            imageLoadError(event){
                event.target.src = "/storage/default.png"
            },
            imageModal(src,title){
                this.imageModalSrc = src;
                this.imageModalTitle = title;
                var modal = document.getElementById('showImageModal');
                modal.style.display = "block";
            },
            closeImageModal(){
                 var modal = document.getElementById('showImageModal');
                // Get the <span> element that closes the modal
                var span = document.getElementsByClassName("closeImage")[0];
                // When the user clicks on <span> (x), close the modal
                span.onclick = function() { 
                    modal.style.display = "none";
                }
            },
            fetchCustomers(){
                axios.get('/customers-all')
                .then(response => { 
                    this.customerOptions = response.data;
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                })
            },
            customLabelMonth (month) {
                return `${month.name}`
            },
            customLabelYear (year) {
                return `${year.name}`
            },
            customLabelCustomer (customer) {
                return `${customer.name  }`
            },
            clearMap(){
                document.getElementById('map').innerHTML = "";
                mapboxgl.accessToken = this.accessToken;
                // init the map
                var map = new mapboxgl.Map({
                    container: 'map',
                    style: this.mapStyle,
                    center: [121.035249, 14.675647], // starting position
                    minzoom:23,
                    maxZoom:18,
                    zoom: 3,
                    maxBounds: [[110.446227,2.949317], [131.509814,21.637444 ]]
                });
                map.addControl(new mapboxgl.NavigationControl());
            },  
            createCustomerMap: function (map) {
                let v=this;
                var customerData = this.customerSelect;

                map.on('load', function (e) {

                    var createGeoJSONCircle = function(center, radiusInKm, customerCode, points) {
                        if(!points) points = 64;

                        var coords = {
                            latitude: center[1],
                            longitude: center[0]
                        };

                        var km = radiusInKm;

                        var ret = [];
                        var distanceX = km/(111.320*Math.cos(coords.latitude*Math.PI/180));
                        var distanceY = km/110.574;

                        var theta, x, y;
                        for(var i=0; i<points; i++) {
                            theta = (i/points)*(2*Math.PI);
                            x = distanceX*Math.cos(theta);
                            y = distanceY*Math.sin(theta);

                            ret.push([coords.longitude+x, coords.latitude+y]);
                        }
                        ret.push(ret[0]);

                        v.customerCoordinates.push({'customer_code':customerCode,'coords':ret});

                        return {
                            "type": "geojson",
                            "data": {
                                "type": "FeatureCollection",
                                "features": [{
                                    "type": "Feature",
                                    "geometry": {
                                        "type": "Polygon",
                                        "coordinates": [ret]
                                    }
                                }]
                            }
                        };
                    };
                    
                    customerData.forEach(function(marker) {

                        // var coordinates = [marker.lng,marker.lat];  
                        var lng = parseFloat(marker.lng);
                        var lat = parseFloat(marker.lat);
                    
                        //Circle
                        map.addSource("circle"+marker.id, createGeoJSONCircle([lng,lat], 1,marker.customer_code));
                        map.addLayer({
                            "id": "circle"+marker.id,
                            "type": "fill",
                            "source": "circle"+marker.id,
                            "layout": {},
                            "paint": {
                                "fill-color": "#008000",
                                "fill-opacity": 0.2,
                                'fill-outline-color': 'red'
                            }
                        });

                        //Marker
                        // create a HTML element for each feature
                        const el = document.createElement('div')
                        el.className = 'building'
                        // make a marker for each feature and add to the map
                        new mapboxgl.Marker(el)
                        .setLngLat([lng,lat])
                        .addTo(map)
                        
                        //Marker Click
                        el.addEventListener('click', e => {
                            e.stopPropagation();
                            new mapboxgl.Popup()
                            .setLngLat([lng,lat])
                            .setHTML('<div class="text-center"><h4 class="map-pop-up-text">'+ marker.name + '</h4><button id="customer'+marker.id+'" class="btn btn-sm btn-success">View Details</button></p></div>')
                            .addTo(map);

                            const buttonCustomer = document.getElementById('customer'+marker.id)   
                            buttonCustomer.addEventListener('click', e => {
                                e.stopPropagation();
                                v.customer = marker;
                                v.userList();
                                $('#showUserList').modal('show');
                            }, true);

                        }, true);

                        

                        //Label
                        var labels = {
                            "type": "FeatureCollection",
                            "features": [{
                                "type": "Feature",
                                "properties": {
                                    "description": marker.name,
                                },
                                "geometry": {
                                    "type": "Point",
                                    "coordinates": [lng,lat]
                                }
                            }]
                        };

                        map.addSource("places"+marker.id, {
                            "type": "geojson",
                            "data": labels
                        });

                        map.addLayer({
                            "id": "poi-labels"+marker.id,
                            "type": "symbol",
                            "source": "places"+marker.id,
                            "layout": {
                            "text-field": ["get", "description"],
                            "text-variable-anchor": ["top", "bottom", "left", "right"],
                            "text-font": ["Open Sans Bold", "Arial Unicode MS Bold"],
                            "text-radial-offset": 0.5,
                            "text-justify": "auto",
                            "text-size" : 16
                            },
                            paint: {
                                "text-color": "#000",
                                "text-halo-color": "#2DCE89",
                                "text-halo-width": 2,
                                "text-opacity":1
                            }
                            
                        });

                    });


                });
            },
            createUsersMap() {
                let v = this;
                v.customerCoordinates = [];
                mapboxgl.accessToken = this.accessToken;
                // init the map
                var map = new mapboxgl.Map({
                    container: 'map',
                    style: this.mapStyle,
                    center: [121.035249, 14.675647], // starting position
                    minzoom:23,
                    maxZoom:18,
                    zoom: 3,
                    maxBounds: [[110.446227,2.949317], [131.509814,21.637444 ]]
                });

                map.addControl(new mapboxgl.NavigationControl());

                

                const geojson = JSON.parse(JSON.stringify(this.users));
                
                if(geojson){
                    geojson.features.forEach((marker) => {
                        //Marker
                        // create a HTML element for each feature
                        const el = document.createElement('div')
                        el.id = 'user-marker'+marker.id
                        el.className = 'employee'
                        // make a marker for each feature and add to the map
                        new mapboxgl.Marker(el)
                        .setLngLat(marker.geometry.coordinates)
                        .addTo(map)
                        
                        //Marker Click
                        el.addEventListener('click', e => {
                            e.stopPropagation();

                            // console.log(marker);
                            new mapboxgl.Popup()
                            .setLngLat(marker.geometry.coordinates)
                            .setHTML('<div class="text-center"><h3 class="map-pop-up-text">' + marker.properties.name + '</h3><button id="user'+marker.properties.id+'" class="btn btn-sm btn-danger">View Details</button></div>')
                            .addTo(map);

                            const buttonUser = document.getElementById('user'+marker.properties.id)   
                            buttonUser.addEventListener('click', e => {
                                e.stopPropagation();
                                v.user = JSON.parse(JSON.stringify(marker.properties));
                                v.userCoordinates = marker.geometry.coordinates;
                                v.getUserCircleStatus();
                                $('#showUserInfo').modal('show');
                            }, true);
                        }, true);
                    });
                }
                this.createCustomerMap(map);
            },
            getUserCircleStatus(){
                let v = this;
                if(v.user){

                    var customer_circle_coords=[];
                    var user_coords = [];
                    if(v.customerCoordinates){
                       
                        var customer_obj = v.customerCoordinates.find(item => item.customer_code === v.user.schedule_code);

                        var pt = turf.point(v.userCoordinates);
                        var poly = turf.polygon([
                           customer_obj.coords
                        ]);

                        var pts = turf.booleanPointInPolygon(pt, poly);
                        if(pts){
                            v.userCheckStatus = 'inside'; 
                        }else{
                            v.userCheckStatus = 'outside';
                        }

                       
                    }
                }
            },
         
            getUsers(){
                this.loading = true;
                this.errors = [];
                axios.post('/users-data', {
                    request_status: this.request_status,
                    customerSelect: this.customerSelect,
                    selectMonth: this.selectMonth.id,
                    selectYear: this.selectYear.id,
                })
                .then(response =>{
                    this.users = response.data ? response.data : [];
                    this.clearMap();
                    this.createUsersMap();
                    this.loading = false;
                })
                .catch(error => {
                    this.errors = error.response;
                    this.clearMap();
                    this.loading = false;
                })
            },
            getMonths(){
                this.monthOptions = [
                    {id:'01', name: 'Jan'},
                    {id:'02', name: 'Feb'},
                    {id:'03', name: 'Mar'},
                    {id:'04', name: 'Apr'},
                    {id:'05', name: 'May'},
                    {id:'06', name: 'Jun'},
                    {id:'07', name: 'Jul'},
                    {id:'08', name: 'Aug'},
                    {id:'09', name: 'Sep'},
                    {id:'10', name: 'Oct'},
                    {id:'11', name: 'Nov'},
                    {id:'12', name: 'Dec'}
                ];
            },
            getYears(){
                var yearArr = [];
                axios.get('/map-year')
                .then(response => { 
                    response.data.forEach(year => {
                        yearArr.push({'id':year,'name':year});
                    });
                    this.yearOptions = yearArr;
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                })  
            },
            userList(){
                let v = this;
                v.keywords = '';
                v.usersList = [];
                var users_list = [];
                const user_data_arr = JSON.parse(JSON.stringify(v.users));

                user_data_arr.features.forEach(element => {
                    var status = '';
                    if(v.customer.customer_code == element.properties.schedule_code){

                        var customer_obj = v.customerCoordinates.find(item => item.customer_code === element.properties.schedule_code);
                        
                        var pt = turf.point(element.geometry.coordinates);
                        var poly = turf.polygon([
                           customer_obj.coords
                        ]);

                        var pts = turf.booleanPointInPolygon(pt, poly);
                        if(pts){
                            status = 'inside'; 
                        }else{
                            status = 'outside';
                        }

                        users_list.push({
                            'schedule_date' : element.properties.schedule_date,  
                            'name' : element.properties.name,  
                            'schedule_time' : element.properties.schedule_start_time + ' - ' + element.properties.schedule_end_time,  
                            'sign_in_out' : element.properties.sign_in + ' - ' + element.properties.sign_out,  
                            'status' : status,
                            'sign_in_image' : element.properties.sign_in_image,
                            'sign_out_image' : element.properties.sign_out_image,
                            'remarks' : element.properties.remarks,
                        });    
                    }
                });

                v.usersList = Object.assign({}, users_list);

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
            }
        },
        computed:{
            filteredUsers(){
                let self = this;
                return Object.values(self.usersList).filter(user => {
                    return user.name.toLowerCase().includes(this.keywords.toLowerCase()) || user.status.toLowerCase().includes(this.keywords.toLowerCase()) || user.schedule_date.toLowerCase().includes(this.keywords.toLowerCase())
                });
            },
            totalPages() {
                return Math.ceil(Object.values(this.filteredUsers).length / this.itemsPerPage)
            },
            filteredQueues() {
                var index = this.currentPage * this.itemsPerPage;
                var queues_array = Object.values(this.filteredUsers).slice(index, index + this.itemsPerPage);
                if(this.currentPage >= this.totalPages) {
                    this.currentPage = this.totalPages - 1
                }

                if(this.currentPage == -1) {
                    this.currentPage = 0;
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
        background-color:#006994!important;
        border-radius:10px;
    }

    .employee {
        background-image: url('/img/map/user.png');
        background-size: cover;
        width: 30px;
        height: 30px;
        /* border-radius: 50%; */
        cursor: pointer;
    }
    .building {
        background-image: url('/img/map/customer.png');
        background-size: cover;
        width: 35px;
        height: 35px;
        /* border-radius: 50%; */
        cursor: pointer;
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

    .bg-image-modal{
        background-color:rgb(56, 56, 56);
    }

     .bg-image-modal img{
        width:300px;
        height:auto;
        border-radius:5px;
     }

     .image-modal-thumb{
        width: 100%;
        height:200px;
        border-radius:5px;
     }
     .image-modal-list-thumb{
        height:50px;
        width:50px;
        border-radius:25px!important;
     }

    .image-modal-thumb:hover {
        -webkit-filter: brightness(50%);
        -webkit-transition: all 1s ease;
        -moz-transition: all 1s ease;
        -o-transition: all 1s ease;
        -ms-transition: all 1s ease;
        transition: all 1s ease;
    }

    .img-center {
        display: block;
        margin-left: auto;
        margin-right: auto;
        border-radius:5px;
        
    }

    .modal-xl{
        max-width: 1140px!important;
    }

    #myImg {
        border-radius: 5px;
        cursor: pointer;
        transition: 0.3s;
    }

    #myImg:hover {opacity: 0.7;}

    /* The Modal (background) */
    .imageModal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 10000; /* Sit on top */
        padding-top: 100px; /* Location of the box */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0,0,0); /* Fallback color */
        background-color: rgba(0,0,0,0.9); /* Black w/ opacity */
    }

    /* Modal Content (image) */
    .modal-content2 {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 600px;
    }

    /* Caption of Modal Image */
    #caption {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 700px;
        text-align: center;
        color: #ccc;
        padding: 10px 0;
        height: 150px;
    }

    /* Add Animation */
    .modal-content2, #caption {    
        -webkit-animation-name: zoom;
        -webkit-animation-duration: 0.6s;
        animation-name: zoom;
        animation-duration: 0.6s;
    }

    @-webkit-keyframes zoom {
        from {-webkit-transform:scale(0)} 
        to {-webkit-transform:scale(1)}
    }

    @keyframes zoom {
        from {transform:scale(0)} 
        to {transform:scale(1)}
    }

    /* The Close Button */
    .closeImage {
        position: absolute;
        top: 15px;
        right: 35px;
        color: #f1f1f1;
        font-size: 40px;
        font-weight: bold;
        transition: 0.3s;
    }

    .closeImage:hover,
    .closeImage:focus {
        color: #bbb;
        text-decoration: none;
        cursor: pointer;
    }

    /* 100% Image Width on Smaller Screens */
    @media only screen and (max-width: 700px){
        .modal-content2 {
            width: 100%;
        }
    }
    
</style>
