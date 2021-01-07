<template>
    <div>
        <div class="header bg-green pb-6 pt-5 pt-md-6"></div>
        <div class="container-fluid mt--7">
            <!-- Table -->
            <div class="row mt-5">
                <div class="col">
                    <div class="card shadow">

                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="mb-0">Attendance Report</h3>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="row ml-2 mr-2">
                                <div class="col-md-3 float-left">
                                    <div class="form-group">
                                        <label for="name" class="form-control-label">Search TSR</label>
                                        <input type="text" class="form-control" placeholder="Search TSR" v-model="keywords" id="name">
                                    </div>
                                </div>
                                <div class="col-md-2" v-if="userRole == 1 || userRole == 2 || userRole == 10 || userRole == 13">
                                    <div class="form-group">
                                        <label class="form-control-label" for="role">Company</label>
                                        <select class="form-control" v-model="company">
                                            <option v-for="(company,c) in companies" v-bind:key="c" :value="company.id"> {{ company.name }}</option>
                                        </select>
                                        <span class="text-danger" v-if="errors.company  ">{{ errors.company[0] }}</span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="start_date" class="form-control-label">Start Date</label>
                                        <input type="date" id="start_date" class="form-control form-control-alternative" v-model="startDate">
                                        <span class="text-danger" v-if="errors.startDate"> {{ errors.startDate[0] }} </span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="end_date" class="form-control-label">End Date</label>
                                        <input type="date" id="end_date" class="form-control form-control-alternative" v-model="endDate">
                                        <span class="text-danger" v-if="errors.endDate"> {{ errors.endDate[0] }} </span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">

                                        <label for="customerSelect" class="form-control-label">Select Region</label>
                                        <multiselect
                                                v-model="regionIds"
                                                :options="regionOptions"
                                                :multiple="true"
                                                track-by="id"
                                                :custom-label="customLabelRegion"
                                                placeholder="Select Region"
                                                id="selected_region"
                                        >
                                        </multiselect>
                                        <span class="text-danger small" v-if="errors.selectedRegion">{{ errors.selectedRegion[0] }}</span>
                                    </div>
                                </div>

                                <div class="col-md-12 text-right">
                                    <button class="btn btn-sm btn-primary" @click="fetchSchedules"> Filter</button>
                                    <download-excel
                                        :data   = "schedules"
                                        :fields = "json_fields"
                                        class   = "btn btn-sm btn-default"
                                        name    = "Salesforce Attendance report.xls">
                                            Export to excel
                                    </download-excel>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <h4 class="ml-3" v-if="loading"><i>Please wait. Loading...</i></h4>
                            <h4 class="ml-3" v-else>Total Filtered Attendance : {{ totalFilterSchedule }}</h4>
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">TSR</th>
                                    <th scope="col">Customer</th>
                                    <!-- <th scope="col">Date</th>
                                    <th scope="col">Schedule</th> -->
                                    <th scope="col">In / Out</th>
                                    <th scope="col">Rendered</th>
                                    <th scope="col">Short Time Status</th>
                                    <th scope="col">Schedule Type</th>
                                    <th scope="col">Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(schedule, s) in filteredQueues" v-bind:key="s">
                                    <td class="text-right">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a v-if="schedule.attendances && schedule.attendances.sign_in !== null" class="dropdown-item" href="javascript:void(0)"  data-toggle="modal" data-target="#singInphotoModal" @click="getSingInImage(schedule)">Sign In Photo</a>
                                                    <a v-if="schedule.attendances && schedule.attendances.sign_out !== null" class="dropdown-item" href="javascript:void(0)"  data-toggle="modal" data-target="#photoModal" @click="getImage(schedule)">Sign out Photo</a>
                                                    <a v-if="schedule.type == 7" class="dropdown-item" href="javascript:void(0)"  data-toggle="modal" data-target="#salesCallModal" @click="getSalesCallAttachment(schedule)">Online Visit</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ schedule.user.name }}</td>
                                        <td>
                                            Customer: {{ schedule.name }} <br>
                                            Date: {{ moment(schedule.date).format('ll') }} <br>
                                            Schedule: {{  moment(schedule.start_time, "HH:mm:ss").format("hh:mm A")  }} - {{ moment(schedule.end_time, "HH:mm:ss").format("hh:mm A") }}<br>
                                            Location: {{  schedule.address  }} <br>
                                            <div v-if="schedule.customer">
                                                <p v-if="schedule.customer.provinces">
                                                     Region: {{  schedule.customer.provinces.regions ? schedule.customer.provinces.regions.name : ""  }}
                                                </p>
                                            </div>
                                        </td>
                                        <td>
                                            <div v-if="schedule.attendances">
                                                <span v-if="schedule.attendances"> IN: {{ moment(schedule.attendances.sign_in ).format('lll') }}</span> <br>
                                                <span v-if="schedule.attendances && schedule.attendances.sign_out !== null"> OUT: {{ moment(schedule.attendances.sign_out).format('lll') }} </span>
                                            </div>
                                            <div v-else>
                                                <span v-if="schedule.signinwithoutout"> IN: {{ moment(schedule.signinwithoutout.sign_in ).format('lll') }}</span> <br>
                                                <span v-if="schedule.signinwithoutout"> OUT: </span>
                                            </div>

                                        </td>
                                        <td>
                                            <span v-if="schedule.attendances && schedule.attendances.sign_out !== null">
                                                {{ rendered(schedule.attendances.sign_out, schedule.attendances.sign_in) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span v-if="schedule.attendances && schedule.attendances.sign_out !== null">
                                                {{ checkRendereShorTime(schedule.attendances.sign_out, schedule.attendances.sign_in) }}
                                            </span>
                                        </td>
                                        <!-- <td></td> -->
                                        <td>
                                            {{ schedule.schedule_type.description }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                       <div class="card-footer py-4">
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
            </div>
        </div>

        <!-- Photo Modal -->
        <div class="modal fade" id="photoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <span class="closed" data-dismiss="modal">&times;</span>
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-body text-center">
                    <img class="w-100 h-75" style="max-height: 700px" :src="image">
                    <h1 class="mt-3"> {{ tsrName }} </h1>
                    <span>{{ remarks }} </span><br>
                    <a :href="signOutLink" target="__blank">Sign Out link</a>
                </div>
                </div>
            </div>
        </div>

        <!-- Sign in Photo Modal -->
        <div class="modal fade" id="singInphotoModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <span class="closed" data-dismiss="modal">&times;</span>
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-body text-center">
                    <img class="w-100 h-75" style="max-height: 700px" :src="signImage">
                    <h1 class="mt-3"> {{ tsrName }} </h1>
                    <a :href="signInLink" target="__blank">Sign In link</a>
                </div>
                </div>
            </div>
        </div>

        <!-- Sign in Photo Modal -->
        <div class="modal fade" id="salesCallModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <span class="closed" data-dismiss="modal">&times;</span>
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-body text-center">
                    <img class="w-100 h-75" style="max-height: 700px" :src="salesCallAttachment">
                    <h1 class="mt-3"> {{ tsrName }} </h1>
                    <!-- <a :href="signInLink" target="__blank">Sign In link</a> -->
                </div>
                </div>
            </div>
        </div>

    </div>
</template>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<script>
import moment from 'moment';
import JsonExcel from 'vue-json-excel'
import Multiselect from 'vue-multiselect';

export default {
    components: { 'downloadExcel': JsonExcel, Multiselect },
    props: ['userRole'],
    data(){
        return{
            tsrs: [],
            schedules: [],
            startDate: '',
            endDate: '',
            tsrName: '',
            remarks: '',
            image: '',
            signImage: '',
            signInLink: '',
            salesCallAttachment: '',
            signOutLink: '',
            errors: [],
            companies: [],
            company: '',
            keywords: '',
            currentPage: 0,
            itemsPerPage: 10,
            totalFilterSchedule : 0,
            json_fields: {
                'TYPE': {
                    callback: (value) => {
                        if(value.type == 1){
                            return 'Customer';
                        }else if(value.type == 2){
                           return 'Mapping';
                        }else{
                            return 'Event';
                        }
                    }
                },
                'NAME': 'name',
                'ADDRESS': 'address',
                'REGION': {
                    callback: (value) => {
                        if(value.customer){
                            if(value.customer.provinces){
                                var region = value.customer.provinces.regions ? value.customer.provinces.regions.name : "";
                                return region;
                            }else{
                                return "";
                            }
                        }else{
                            return "";
                        }
                    }
                },
                'SCHEDULE TYPE' : {
                    callback: (value) => {
                        if(value.schedule_type){
                            return value.schedule_type.description;
                        }else{
                            return "";
                        }
                    }
                },
                'DATE': 'date',
                'START TIME': 'start_time',
                'END TIME': 'end_time',
                'STATUS': {
                    callback: (value) => {
                        if(value.status == 1){
                            return 'Visited';
                        }else if(value.status == 2){
                           return 'Pending';
                        }else{
                            return 'Absent';
                        }
                    }
                },
                'REMARKS': {
                    callback: (value) => {
                        if(value.attendances){
                            return value.attendances.remarks;
                        }else{
                            return '';
                        }
                    }
                },
                'SALES PERSONNEL': {
                    callback: (value) => {
                        return value.user ? value.user.name : "";
                    }
                },
                'IN': {
                    callback: (value) => {
                        if(value.attendances){
                            return moment(value.attendances.sign_in).format('lll');
                        }else{
                            return '';
                        }
                    }
                },
                'OUT':{
                      callback: (value) => {
                        if(value.attendances){
                            if(value.attendances.sign_out){
                                return moment(value.attendances.sign_out).format('lll');
                            }else{
                                return '';
                            }
                        }else{
                            return '';
                        }
                    }
                },
                'RENDERED':{
                     callback: (value) => {
                        if(value.attendances){
                            if(value.attendances.sign_in && value.attendances.sign_out){
                                // return moment(value.attendances.sign_out).format('lll');
                                return this.rendered(value.attendances.sign_out, value.attendances.sign_in)
                            }else{
                                return '';
                            }
                        }else{
                            return '';
                        }
                    }
                },
                'SHORT TIME STATUS':{
                     callback: (value) => {
                        if(value.attendances){
                            if(value.attendances.sign_in && value.attendances.sign_out){
                                // return moment(value.attendances.sign_out).format('lll');
                                return this.checkRendereShorTime(value.attendances.sign_out, value.attendances.sign_in)
                            }else{
                                return '';
                            }
                        }else{
                            return '';
                        }
                    }
                }
            },
            regionIds:[],
            regionOptions : [],
            loading : false,
        }
    },
    created(){
        this.fetchCompanies();
        this.fetchTodaySchedules();
        this.fetchRegion();
    },
    methods:{
        moment,
        fetchRegion(){
            axios.get('/regions')
            .then(response => {
                this.regionOptions = response.data;
            })
            .catch(error =>{
                this.errors = error.response.data.errors;
            })
        },
        customLabelRegion(region) {
                if(region){
                    return `${region.name}`
                }else{
                    return '';
                }

        },
        fetchCompanies(){
            axios.get('/companies-all')
            .then(response => {
                this.companies = response.data;
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            })
        },
        fetchTodaySchedules(){
            this.loading = true;
            axios.get('/attendance-report-today')
            .then(response => {
                this.schedules = response.data;
                this.errors = [];
                this.loading = false;
            })
            .catch(error => {
                this.errors = error.response.data.errors;
                this.loading = false;
            })
        },
        fetchSchedules(){
            this.loading = true;
            this.schedules = [];
            axios.post('/attendance-report-bydate', {
                startDate: this.startDate,
                endDate: this.endDate,
                company: this.company,
                selectedRegion: this.regionIds,
            })
            .then(response => {
                this.schedules = response.data;
                this.errors = [];
                 this.loading = false;
            })
            .catch(error => {
                this.errors = error.response.data.errors;
                this.loading = false;
            })
        },
        // fetchTsrs(){
        //     axios.get('/attendance-report-all')
        //     .then(response=>{
        //         this.tsrs = response.data;
        //     })
        //     .catch(error =>{
        //         this.errors = error.response.data.errors;
        //     })
        // },
        rendered(endTime, startTime){
            if(endTime && startTime){
                var ms = moment(endTime,"YYYY/MM/DD HH:mm a").diff(moment(startTime,"YYYY/MM/DD HH:mm a"));
                var d = moment.duration(ms);
                var hours = Math.floor(d.asHours());
                var minutes = moment.utc(ms).format("mm");
                return hours + 'h '+ minutes+' min.';
             }else{
                return "";
            }
        },
        checkRendereShorTime(endTime, startTime){
            if(endTime && startTime){
                var ms = moment(endTime,"YYYY/MM/DD HH:mm a").diff(moment(startTime,"YYYY/MM/DD HH:mm a"));
                var short_time_status="";
                if(ms <= 300000){
                    short_time_status = "Short time @ fieldwork"
                }

                return short_time_status;
             }else{
                return "";
            }
        },
        getImage(schedule){
            this.image = window.location.origin+'/storage/'+schedule.attendances.sign_out_image;
            this.tsrName = schedule.user ? schedule.user.name : "";
            this.remarks = schedule.attendances.remarks;
            this.signOutLink = 'https://www.google.com/maps/place/'+schedule.attendances.sign_out_latitude+','+schedule.attendances.sign_out_longitude;
        },
        getSingInImage(schedule){
            this.signImage = window.location.origin+'/storage/'+schedule.attendances.sign_in_image;
            this.tsrName = schedule.user ? schedule.user.name : "";
            this.signInLink = 'https://www.google.com/maps/place/'+schedule.attendances.sign_in_latitude+','+schedule.attendances.sign_in_longitude;
        },
        getSalesCallAttachment(schedule) {
            this.salesCallAttachment = window.location.origin+'/storage/'+schedule.salesmanAttachement.attachment;
            this.tsrName = schedule.user.name;
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
        filteredSchedules(){
            let self = this;
            return self.schedules.filter(schedule => {
                if(schedule.user){
                    return schedule.user.name.toLowerCase().includes(this.keywords.toLowerCase())
                }
            });
        },
        totalPages() {
            return Math.ceil(this.filteredSchedules.length / this.itemsPerPage)
        },
        filteredQueues() {
            this.totalFilterSchedule = this.filteredSchedules.length;
            var index = this.currentPage * this.itemsPerPage;
            var queues_array = this.filteredSchedules.slice(index, index + this.itemsPerPage);

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
    .modal{
        background-color: rgba(0,0,0,0.9);
    }
    /* The Close Button */
    .closed {
        position: absolute;
        top: 15px;
        right: 35px;
        color: #f1f1f1;
        font-size: 40px;
        font-weight: bold;
        transition: 0.3s;
    }
    .closed:hover,
    .closed:focus {
        color: #bbb;
        text-decoration: none;
        cursor: pointer;
    }
</style>
