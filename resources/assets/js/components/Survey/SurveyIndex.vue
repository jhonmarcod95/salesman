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
                                    <h3 class="mb-0">Survey Report</h3>
                                </div>
                                 <div class="col-4 text-right">
                                    <button type="submit" @click="switchView = !switchView" class="btn btn-outline-primary mb-2">{{ switchView === false ? 'Switch to Graph' : 'Switch to Table' }}</button>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="row pl-2 pr-2">
                             
                                <div class="col-md-3" v-if="userRole == 1 || userRole == 2 || userRole == 10 || userRole == 13">
                                    <div class="form-group">
                                        <label class="form-control-label" for="role">Company</label>
                                        <select class="form-control" v-model="company">
                                            <option v-for="(company,c) in companies" v-bind:key="c" :value="company.id"> {{ company.name }}</option>
                                        </select>
                                        <span class="text-danger" v-if="errors.company  ">{{ errors.company[0] }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="start_date" class="form-control-label">Start Date</label> 
                                        <input type="date" id="start_date" class="form-control form-control-alternative" v-model="startDate">
                                        <span class="text-danger" v-if="errors.startDate"> {{ errors.startDate[0] }} </span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="end_date" class="form-control-label">End Date</label> 
                                        <input type="date" id="end_date" class="form-control form-control-alternative" v-model="endDate">
                                        <span class="text-danger" v-if="errors.endDate"> {{ errors.endDate[0] }} </span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                         <label for="end_date" class="form-control-label">&nbsp;</label> 
                                         <button type="button" class="btn btn-primary btn-lg btn-block" :class="{ ' disabled' : loading === true }" :disabled="loading === true"  @click="fetchSchedules">Filter</button>                                    
                                    </div>
                                </div>
                            </div>
                        </div>


                        <graph-survey :surveys="filteredSurveys" 
                                      :startDate="startDate"
                                      :endDate="endDate"
                                      :company="company"
                                      v-show="switchView === true">
                       </graph-survey>

                        <div v-show="switchView === false" class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">TSR</th>
                                    <th scope="col">Customer</th>
                                    <!-- <th scope="col">Date</th>
                                    <th scope="col">Schedule</th> -->
                                    <th scope="col">Brands</th>
                                    <th scope="col">Question</th>
                                    <th scope="col">Rating</th>
                                    <th scope="col">Remarks</th>
                                    <th scope="col">Survey Date</th>
                                </tr>
                                </thead>

                                <tbody v-if="loading" class="list">
                                    <tr>
                                        <td colspan="8">

                                            <div class="center-align py-3" style="display: flex; align-items: center; justify-content: center;">
                                                <svg class="spinner" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
                                                    <circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
                                                </svg>	
                                            </div>

                                        </td>
                                    </tr>   
                                </tbody>

                                <tbody v-else v-for="(schedule, s) in filteredSurveys" :key="s"  class="list">

                                    <tr v-for="(item, m) in schedule" v-bind:key="m">
                                    <td class="text-right">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a v-if="item.customer_photo !== null" class="dropdown-item" href="javascript:void(0)"  data-toggle="modal" data-target="#singInphotoModal" @click="getCustomerImage(item.customer_photo, item.user.name)">Customer Photo</a>
                                                    <!-- <a v-if="schedule.attendances && schedule.attendances.sign_out !== null" class="dropdown-item" href="javascript:void(0)"  data-toggle="modal" data-target="#photoModal" @click="getImage(schedule)">Sign out Photo</a> -->
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ item.user.name }}</td>
                                        <td>
                                            Customer: {{ item.customer.name }} <br>
                                            Address: {{ item.customer.area }} <br>
                                        </td>
                                        <td>
                                            <span v-for="(brand, b) in item.brands" :key="b">
                                                <span>{{ brand.name }}</span> <br/>
                                            </span>
                                        </td>
                                        <td>
                                            <span v-for="(rank, r) in item.ranks" :key="r">
                                                <span v-for="(questionnaire, q) in rank.questions" :key="q">
                                                    <span>{{ questionnaire.question }}</span> <span class="text-danger">({{ questionnaire.rating }})</span>
                                                    <br/>
                                                </span>
                                            </span>
                                        </td>
                                        <td>
                                            {{ questionnaireTotal(item.ranks[0].questions.map(item => item.rating)) }} Points
                                        </td>
                                         <td scope="row">
                                            <div class="media align-items-center">
                                                <div style="width: 300px; white-space: normal;" class="media-body">
                                                   {{ item.remarks }}
                                                </div>
                                            </div>
                                        </td>
                                        <td> {{ item.created_at }}</td>
                                    </tr>

                                     <tr class="table-success">
                                        <td colSpan="5"></td>
                                        <td>
                                        <span class="font-weight-bold text-danger">
                                            {{ aveScheduleRanks(schedule) }} (Average)
                                        </span>
                                        <td colSpan="3"></td>
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
        
    </div>
</template>

<script>
import moment from 'moment';
import JsonExcel from 'vue-json-excel'
import GraphSurvey from './GraphSurvey';

export default {
    components: 
    { 
        'downloadExcel': JsonExcel,
        GraphSurvey
    },
    props: ['userRole'],
    data(){
        return{
            loading: false,
            switchView: false,
            tsrs: [],
            schedules: [],
            startDate: '',
            endDate: '',
            tsrName: '',
            remarks: '',
            image: '',
            signImage: '',
            signInLink: '',
            signOutLink: '',
            errors: [],
            companies: [],
            company: '',
            keywords: '',
            currentPage: 0,
            itemsPerPage: 10,
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
                        return value.user.name;
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
                }
            }
        }
    },
    created(){
        this.fetchCompanies();
    },
    methods:{
        moment,
        fetchCompanies(){
            axios.get('/companies-all')
            .then(response => {
                this.companies = response.data;
            })
            .catch(error => { 
                this.errors = error.response.data.errors;
            })
        },
        fetchSchedules(){
            this.loading = true
            axios.post('/api/surveys/company', {
                startDate: this.startDate,
                endDate: this.endDate,
                company: this.company
            })
            .then(response => {
                console.log('check result: ', response.status)
                if(response.status === 200) {
                    this.schedules = response.data;
                    this.errors = []; 
                    this.loading = false
                }
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            })
        },
        questionnaireTotal(array){
            return array.reduce((prev, curr) => prev + curr, 0 );
        },
        aveScheduleRanks(array) {
            let ranks = array.map(item => item.ranks.map(rank => rank.questions.map(item => item.rating )));
            let getTotalRating = this.questionnaireTotal(ranks.flat(2)); 
            let getTotallength = ranks.flat(2).length / 2; 
            let ave = getTotalRating / getTotallength;

            return ave.toFixed(2);
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
            var ms = moment(endTime,"YYYY/MM/DD HH:mm a").diff(moment(startTime,"YYYY/MM/DD HH:mm a"));
            var d = moment.duration(ms);
            var hours = Math.floor(d.asHours());
            var minutes = moment.utc(ms).format("mm");

            return hours + 'h '+ minutes+' min.';
                                            
        },
        getImage(schedule){
            this.image = window.location.origin+'/storage/'+schedule.attendances.sign_out_image;
            this.tsrName = schedule.user.name;
            this.remarks = schedule.attendances.remarks;
            this.signOutLink = 'https://www.google.com/maps/place/'+schedule.attendances.sign_out_latitude+','+schedule.attendances.sign_out_longitude;
        },
        getSingInImage(schedule){
            this.signImage = window.location.origin+'/storage/'+schedule.attendances.sign_in_image;
            this.tsrName = schedule.user.name;
            this.signInLink = 'https://www.google.com/maps/place/'+schedule.attendances.sign_in_latitude+','+schedule.attendances.sign_in_longitude;
        },
        getCustomerImage(photo, tsr){
            this.signImage = window.location.origin+'/storage/'+photo;
            this.tsrName = tsr;
            // this.signInLink = 'https://www.google.com/maps/place/'+schedule.attendances.sign_in_latitude+','+schedule.attendances.sign_in_longitude;
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
        // filteredSchedules(){
        //     let self = this;
        //     return self.schedules.filter(schedule => {
        //         return schedule.user.name.toLowerCase().includes(this.keywords.toLowerCase())
        //     });
        // },
        totalPages() {
            return Math.ceil(this.schedules.length / this.itemsPerPage)
        },
        filteredSurveys() {
            var index = this.currentPage * this.itemsPerPage;
            var queues_array = this.schedules.slice(index, index + this.itemsPerPage);

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
    .disabled {
        cursor: not-allowed;
    }
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
