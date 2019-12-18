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
                                    <h3 class="mb-0">Map Analytics Report - Users</h3>
                                </div>
                            </div>
                        </div>
                       
                        <div class="mb-3">
                            <div class="row col-sm-12">
                                <div class="col-md-3 float-left">
                                    <div class="form-group">
                                        <label for="customerSelect" class="form-control-label">Select User</label> 
                                        <multiselect
                                                v-model="userId"
                                                :options="userOptions"
                                                :multiple="false"
                                                track-by="id"
                                                :custom-label="customLabelUser"
                                                placeholder="Select User"
                                                id="selected_user"
                                        >
                                        </multiselect>
                                    </div>
                                </div>
                                <div class="col-md-3 float-left">
                                    <div class="form-group">
                                        <label for="customerSelect" class="form-control-label">Schedule Type</label> 
                                        <multiselect
                                                v-model="scheduleType"
                                                :options="scheduleTypeOptions"
                                                :multiple="false"
                                                track-by="id"
                                                :custom-label="customLabelScheduleType"
                                                placeholder="Schedule Type"
                                                id="selected_schedule_type"
                                        >
                                        </multiselect>
                                    </div>
                                </div>
                                <div class="col-md-2 float-left">
                                    <div class="form-group">
                                        <label for="customerSelect" class="form-control-label">Address</label> 
                                        <input type="text" id="searchAddress" placeholder="Address" class="form-control form-control-alternative" v-model="searchAddress">
                                        <span class="text-danger" v-if="errors.searchAddress"> {{ errors.searchAddress[0] }} </span>
                                    </div>
                                </div>
                                <div class="col-md-2 float-left">
                                    <div class="form-group">
                                        <label for="startDate" class="form-control-label">Start Date</label> 
                                        <input type="date" id="startDate" class="form-control form-control-alternative" v-model="startDate">
                                        <span class="text-danger" v-if="errors.startDate"> {{ errors.startDate[0] }} </span>
                                    </div>
                                </div>

                                <div class="col-md-2 float-left">
                                    <div class="form-group">
                                        <label for="endDate" class="form-control-label">End Date</label> 
                                        <input type="date" id="endDate" class="form-control form-control-alternative" v-model="endDate">
                                        <span class="text-danger" v-if="errors.endDate"> {{ errors.endDate[0] }} </span>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <button class="btn btn-sm btn-primary" @click="getUserLocations"> Filter</button>
                                    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#showUserList"> List ({{ Object.keys(usersList).length }})</button>
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

        <!-- User Details Modal -->
        <div class="modal fade" id="showUserInfo" tabindex="2" role="dialog" aria-labelledby="exampleModalLabel1" aria-hidden="true" data-backdrop="true">
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
                                <h1 style="color:#5e72e4">{{ name }}</h1>
                                <div class="h4 font-weight-500"><i class="fas fa-clock" style="color:green" title="Sign in"></i> {{ sign_in }} - <i class="fas fa-clock" style="color:orange" title="Sign out"></i> {{ sign_out }}</div>
                                <div class="h4 font-weight-500">Rendered: {{ hrs_rendered }}</div>
                            </div>

                            <div class="mt-3 py-3 border pl-1 pr-1">
                                <h4 class="text-left">SCHEDULE: {{ schedule_type }}</h4>
                                <div class="row justify-content-center text-center">
                                    <div class="col-lg-12">
                                        <h4 style="color:#2DCE89">{{ schedule_name }}</h4>
                                        <p class="description">{{ schedule_address }}</p>
                                        <div class="h4 fontfont-weight-500"><i class="fas fa-calendar" style="color:blue" title="Date"></i> {{ schedule_date }}</div>
                                        <div class="h4 font-weight-500"><i class="fas fa-clock" style="color:green" title="Start Time"></i> {{ schedule_start_time }} - <i class="fas fa-clock" style="color:orange" title="End Time"></i> {{ schedule_end_time }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="py-3" v-if="remarks">
                                <h4 class="text-left">REMARKS</h4>
                                <div class="row justify-content-center text-center">
                                    <div class="col-lg-12">
                                        <p>{{ remarks }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-3">
                                    <h4 class="text-center">In</h4>  
                                    <img id="sign-in-image" class="image-modal-thumb img-center" :src="'/storage/' + sign_in_image" @error="imageLoadError" alt="Sign In Image"  @click="imageModal('/storage/' + sign_in_image, 'Sign in image')">   
                                </div>
                                <div class="col-3">
                                    <h4 class="text-center">Out</h4>
                                    <img id="sign-out-image" class="image-modal-thumb img-center"  :src="'/storage/' + sign_out_image" @error="imageLoadError" alt="Sign Out Image"  @click="imageModal('/storage/' + sign_out_image, 'Sign out image')">     
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

        <!-- Users List Modal -->

        <div class="modal fade" id="showUserList" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title-default">Map User(s ) List</h4>
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
                    </div>
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                            <tr>
                                <th scope="col">Image In / Out</th>
                                <th scope="col">Name</th>
                                <th scope="col">Date</th>
                                <th scope="col">Schedule</th>
                                <th scope="col">Address</th>
                                <th scope="col">Type</th>
                                <th scope="col">Schedule In / Out</th>
                                <th scope="col">In / Out</th>
                                <th scope="col">Rendered</th>
                                <th scope="col">Remarks</th>
                            </tr>
                            </thead>
                                <tbody v-if="usersList">
                                    <tr v-for="(user, e) in filteredQueues" v-bind:key="e"> 
                                        <td>
                                            <div class="row" style="width:105px!important;">
                                                <div class="col-sm-6">
                                                    <img id="sign-in-image" class="image-modal-list-thumb img-center" :src="'/storage/' + user.attendances.sign_in_image" @error="imageLoadError" alt="Sign In Image"  data-toggle="modal" data-target="#showUserImage" @click="imageModal('/storage/' + user.attendances.sign_in_image, 'Sign in image')">
                                                </div>
                                                <div class="col-sm-6">
                                                    <img id="sign-out-image" class="image-modal-list-thumb img-center" :src="'/storage/' + user.attendances.sign_out_image" @error="imageLoadError" alt="Sign Out Image"  data-toggle="modal" data-target="#showUserImage" @click="imageModal('/storage/' + user.attendances.sign_out_image, 'Sign out image')">
                                                </div>
                                             </div>
                                        </td> 
                                        <td>{{ user.user.name }}</td>
                                        <td>{{ user.date }}</td>    
                                        <td>{{ user.name }}</td>
                                        <td>{{ user.address }}</td>
                                        <td>{{ user.schedule_type.description }}</td>
                                        <td>{{ user.start_time }} - {{ user.end_time }}</td>
                                        <td>{{ user.attendances.sign_in }} - {{ user.attendances.sign_out }}</td>
                                        <td>{{ rendered(user.attendances.sign_out, user.attendances.sign_in) }}</td>
                                        <td>{{ user.attendances.remarks }}</td>
                                        
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

        <!-- Image Modal -->

        <div id="showImageModal" tabindex="1" class="imageModal">
            <span class="closeImage" @click="closeImageModal">×</span>
            <img class="modal-content-img" :src="imageModalSrc" @error="imageLoadError" alt="Image" id="imgModal">
            <div id="caption">{{ imageModalTitle }}</div>
        </div>
        
    </div>

</template>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<script>
    import moment from 'moment';
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
                mapStyle: 'mapbox://styles/mapbox/streets-v11',
                mapCenter: [121.035249, 14.675647],
                mapDefaultLayer: [],
                userOptions:[],
                scheduleTypeOptions:[],
                userId:'',
                scheduleType:'',
                users:[],
                user:[],
                name:'',
                hrs_rendered:'',
                sign_in:'',
                sign_out:'',
                remarks:'',
                sign_in_image:'',
                sign_out_image:'',
                schedule_name: '',
                schedule_address:'',
                schedule_date:'',
                schedule_start_time:'',
                schedule_end_time:'',
                schedule_type:'',
                searchAddress:'',
                startDate:'',
                endDate:'',
                errors: [],
                error: '',
                imageModalSrc : '',
                imageModalTitle : '',
                usersList : [],
                keywords : '',
                currentPage: 0,
                itemsPerPage: 10,
                loading: false,
            };
        },
        created() {
            this.mapbox = Mapbox;
            this.fetchUsers();
            this.fetchScheduleTypes();
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
                modal.style.display = "none";
            },
            fetchUsers(){
                axios.get('/map-users-all')
                .then(response => { 
                    this.userOptions = response.data;
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                })
            },
            fetchScheduleTypes(){
                axios.get('/schedule-types-all')
                .then(response => { 
                    this.scheduleTypeOptions = response.data;
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
            customLabelUser (user) {
                return `${ user.name + ' (' + user.company.name  + ')' }`
            },
            customLabelScheduleType (schedule_type) {
                return `${schedule_type.description  }`
            },
            clearMap(){
                this.loading = false;
                document.getElementById('map').innerHTML = "";
                mapboxgl.accessToken = this.accessToken;
                var map = new mapboxgl.Map({
                    container: 'map',
                    style: this.mapStyle,
                    center: this.mapCenter,
                    minzoom:23,
                    maxZoom:18,
                    zoom: 3,
                    maxBounds: [[110.446227,2.949317], [131.509814,21.637444 ]]
                });
                map.addControl(new mapboxgl.NavigationControl());
            },  
            createUsersMap() {
                let v = this;
                v.clearMap();
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

                const geojson = JSON.parse(JSON.stringify(this.users));
                
                if(geojson){
                    geojson.forEach((marker) => {
                        
                        var sign_in_longitude = 0;
                        var sign_in_latitude = 0;     
                        
                        if(marker.attendances){
                            var sign_in_longitude = marker.attendances.sign_in_longitude ? marker.attendances.sign_in_longitude : 0;
                            var sign_in_latitude = marker.attendances.sign_in_latitude ? marker.attendances.sign_in_latitude : 0;
                        }
                        
                        const el = document.createElement('div')
                        el.id = 'user-marker' + marker.id 
                        el.title = 'Name: ' + marker.user.name + '\nAddress: ' + marker.address  + '\nType: ' +  marker.schedule_type.description  + '\nDate: ' +  marker.date 
                        el.className = 'employee'
                     
                        new mapboxgl.Marker(el)
                        .setLngLat([sign_in_longitude,sign_in_latitude])
                        .addTo(map)
                        
                        el.addEventListener('click', e => {
                            e.stopPropagation();

                            var current_zoom = map.getZoom();

                            map.flyTo({center: [sign_in_longitude,sign_in_latitude],zoom: current_zoom > 17 ? current_zoom : 17});

                            new mapboxgl.Popup()
                            .setLngLat([sign_in_longitude,sign_in_latitude])
                            .setHTML('<div class="text-center"><h4 class="map-pop-up-text mb-0">' + marker.user.name + '</h4><button id="user-'+marker.attendances.id+marker.id+'" class="btn btn-outline-primary btn-sm">'+marker.date+'</button></div>')
                            .addTo(map);
                            const buttonUser = document.getElementById('user-'+marker.attendances.id+marker.id)   
                            buttonUser.addEventListener('click', e => {
                                e.stopPropagation();
                                v.id = marker.id;
                                v.name = marker.user.name;
                                v.sign_in = marker.attendances.sign_in;
                                v.sign_out = marker.attendances.sign_out;
                                v.hrs_rendered = v.rendered(marker.attendances.sign_out,marker.attendances.sign_in);
                                v.remarks = marker.attendances.remarks;
                                v.sign_in_image = marker.attendances.sign_in_image;
                                v.sign_out_image = marker.attendances.sign_out_image;
                                v.schedule_name = marker.name;
                                v.schedule_address = marker.address;
                                v.schedule_date = marker.date;
                                v.schedule_start_time = marker.start_time;
                                v.schedule_end_time = marker.end_time;
                                v.schedule_type = marker.schedule_type.description; 
                                $('#showUserInfo').modal('show');
                            }, true);
                        }, true);
                    });
                }
                v.loading = false;
            },
            getUserLocations(){
                this.loading = true;
                this.usersList = [];
                this.errors = [];
                axios.post('/user-locations', {
                    defaultUsers: this.userOptions,
                    userId: this.userId ? this.userId['id'] : '',
                    scheduleType: this.scheduleType ? this.scheduleType['id'] : '',
                    searchAddress: this.searchAddress ? this.searchAddress : '',
                    startDate: this.startDate,
                    endDate: this.endDate,
                })
                .then(response =>{
                    this.users = response.data ? response.data : [];
                    this.usersList = Object.assign({},  this.users);
                    this.createUsersMap();
                })
                .catch(error => {
                    this.loading = false;
                    this.errors = error.response.data.errors;
                    this.clearMap();
                    if(this.errors.startDate[0] || this.errors.endDate[0]){
                        return this.errors;
                    }else{
                        alert('Unable to load Map. Please try again.');
                        window.location.reload();
                    }
                });
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
            }
        },
        computed:{
            filteredUsers(){
                let self = this;
                return Object.values(self.usersList).filter(user => {
                    if(user.user){
                       return user.user.name.toLowerCase().includes(this.keywords.toLowerCase()) || user.date.toLowerCase().includes(this.keywords.toLowerCase())
                    }
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
        background-color:#75CFF0!important;
    }

    .employee {
        background-image: url('/img/map/user.png');
        background-size: cover;
        width: 30px;
        height: 30px;
        cursor: pointer;
    }
    .building {
        background-image: url('/img/map/customer.png');
        background-size: cover;
        width: 35px;
        height: 35px;
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
        height:140px;
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

    .imageModal {
        display: none; 
        position: fixed; 
        z-index: 10000;
        padding-top: 100px;
        left: 0;
        top: 0;
        width: 100%; 
        height: 100%;
        overflow: auto; 
        background-color: rgb(0,0,0); 
        background-color: rgba(0,0,0,0.9); 
    }

    .modal-content-img {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 600px;
    }

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

    .modal-content-img, #caption {    
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

    .closeImage {
        position: absolute;
        top: 15px;
        right: 35px;
        color: #f1f1f1;
        font-size: 30px;
        font-weight: bold;
        transition: 0.3s;
        cursor: pointer;
    }

    @media only screen and (max-width: 700px){
        .modal-content-img {
            width: 100%;
        }
    }

    .multiselect__tags {
        min-height: 45px!important;
    }

    .multiselect__single {
        padding-top: 5px!important;
    }
    
</style>
