<template>
    <div>
        <!-- Header -->
        <div class="header bg-green pb-8 pt-5 pt-md-8" style="min-height: 300px; background-image: url(/img/bg.jpg); background-size: cover; background-position: center bottom;">
            <span class="mask bg-gradient-primary opacity-7"></span>
            <div class="container-fluid">
                <div class="header-body">
                    <div class="row">
                        <div class="col-xl-3 col-lg-6" style="cursor:pointer" @click="showscheduleForVisit">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">Schedule for Customer Visit</h5>
                                            <span class="h2 font-weight-bold mb-0">{{ scheduleForVisit.length }}</span>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon icon-shape bg-gradient-red text-white rounded-circle shadow">
                                                <i class="fas fa-calendar"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mt-3 mb-0 text-muted text-sm">
                                        <span class="text-nowrap">Today</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6" style="cursor:pointer" @click="showactualVisit">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">Actual Visited Customer</h5>
                                            <span class="h2 font-weight-bold mb-0">{{ actualVisit.length }}</span>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon icon-shape bg-gradient-green text-white rounded-circle shadow">
                                                <i class="fas fa-map-marker"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mt-3 mb-0 text-muted text-sm">
                                        <span class="text-nowrap">Today</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6" style="cursor:pointer">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">Total Not Visited Customer</h5>
                                            <span class="h2 font-weight-bold mb-0">{{notVisit.length}}</span>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon icon-shape bg-gradient-orange text-white rounded-circle shadow">
                                                <i class="fas fa-times"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mt-3 mb-0 text-muted text-sm">
                                        <span class="text-nowrap">Today</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6" style="cursor:pointer">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">Total Client Interaction</h5>
                                            <span class="h2 font-weight-bold mb-0"> {{ totalClientInteraction }}</span>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon icon-shape bg-gradient-blue text-white rounded-circle shadow">
                                                <i class="fas fa-users"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mt-3 mb-0 text-muted text-sm">
                                        <span class="text-nowrap">Today</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid mt--8">
            <div class="row mt-5">

                <div class="col-xl-12 mb-5 mb-xl-0">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="mb-0">Actual Visited Customer</h3>
                                </div>
                                <div class="col-12">
                                    <div class="card-body">
                                        <line-chart :chart-data="actualVisitedCustomer" :height="70" :download="true"  ></line-chart>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 mt-3 mb-5 mb-xl-0">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="mb-0">Customer Visits</h3>
                                </div>
                                <div class="col text-right">
                                    <a href="#!" class="btn btn-sm btn-primary">Most</a>
                                    <a href="#!" class="btn btn-sm btn-primary">Less</a>
                                    <a href="#!" class="btn btn-sm btn-primary">Average</a>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">Customer Name</th>
                                    <th scope="col">Total Visits</th>
                                </tr>
                                </thead>
                                <tbody>
                                   
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 mt-3 mb-5 mb-xl-0">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="mb-0">TSR Customer Visits</h3>
                                </div>
                                <div class="col text-right">
                                    <a href="#!" class="btn btn-sm btn-primary">Most</a>
                                    <a href="#!" class="btn btn-sm btn-primary">Less</a>
                                    <a href="#!" class="btn btn-sm btn-primary">Average</a>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">TSR Name</th>
                                    <th scope="col">Total Visits</th>
                                </tr>
                                </thead>
                                <tbody>
                                   
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


            </div>
        </div>

        <!-- Schedule for Visit -->
        <div class="modal fade" id="showscheduleForVisit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">SCHEDULE FOR CUSTOMER VISIT</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4>Total: {{ scheduleForVisit.length }}</h4>
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                            <tr>
                                <th scope="col">CUSTOMER</th>
                                <th scope="col">TSR</th>
                                <th scope="col">COMPANY</th>
                                <th scope="col">VISITED</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(schedule, s) in scheduleForVisitfilteredQueues" v-bind:key="s">
                                    <td>
                                        <h5>{{schedule.name}}</h5>
                                        <span>ADDRESS: {{schedule.address}}</span><br>
                                        <span>REGION: {{ schedule.customer.provinces ? schedule.customer.provinces.regions.name : ""}}</span>
                                    </td>
                                    <td>
                                        {{ schedule.user ? schedule.user.name : ""}}
                                    </td>
                                    <td>
                                        {{ schedule.user.companies ? schedule.user.companies[0].name : ""}}
                                    </td>
                                    <td>
                                        <div v-if="schedule.attendances"><i class="fas fa-check text-success"></i></div>
                                        <div v-else><i class="fas fa-times text-danger"></i></div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <nav aria-label="...">
                            <ul class="pagination justify-content-end mb-0">
                                <li class="page-item">
                                    <button :disabled="!scheduleForVisitshowPreviousLink()" class="page-link" v-on:click="scheduleForVisitsetPage(scheduleForVisitcurrentPage - 1)"> <i class="fas fa-angle-left"></i> </button>
                                </li>
                                <li class="page-item">
                                    Page {{ scheduleForVisitcurrentPage + 1 }} of {{ scheduleForVisittotalPages }}
                                </li>
                                <li class="page-item">
                                    <button :disabled="!scheduleForVisitshowNextLink()" class="page-link" v-on:click="scheduleForVisitsetPage(scheduleForVisitcurrentPage + 1)"><i class="fas fa-angle-right"></i> </button>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div>

        <!-- Actual Visited Customer -->
        <div class="modal fade" id="showactualVisit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">ACTUAL VISITED CUSTOMER</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4>Total Schedule Visit: {{ actualVisit.length }}</h4>
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                            <tr>
                                <th scope="col">CUSTOMER</th>
                                <th scope="col">ATTENDANCES</th>
                                <th scope="col">TSR</th>
                                <th scope="col">COMPANY</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(schedule, s) in actualVisitfilteredQueues" v-bind:key="s">
                                    <td>
                                        <h4>{{schedule.name}}</h4>
                                        <span>ADDRESS: {{schedule.address}}</span><br>
                                        <span>REGION: {{ schedule.customer.provinces ? schedule.customer.provinces.regions.name : ""}}</span>
                                    </td>
                                    <td>
                                        {{ schedule.attendances ? schedule.attendances.sign_in : "No sign in"}} - {{ schedule.attendances ? schedule.attendances.sign_out : "No sign out"}} <br>
                                        <span v-if="schedule.attendances">DWELL TIME: {{ rendered(schedule.attendances.sign_out , schedule.attendances.sign_in) }}</span>
                                    </td>
                                    <td>
                                        {{ schedule.user ? schedule.user.name : ""}}
                                    </td>
                                    <td>
                                        {{ schedule.user.companies ? schedule.user.companies[0].name : ""}}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <nav aria-label="...">
                            <ul class="pagination justify-content-end mb-0">
                                <li class="page-item">
                                    <button :disabled="!actualVisitshowPreviousLink()" class="page-link" v-on:click="actualVisitsetPage(actualVisitcurrentPage - 1)"> <i class="fas fa-angle-left"></i> </button>
                                </li>
                                <li class="page-item">
                                    Page {{ actualVisitcurrentPage + 1 }} of {{ actualVisittotalPages }}
                                </li>
                                <li class="page-item">
                                    <button :disabled="!actualVisitshowNextLink()" class="page-link" v-on:click="actualVisitsetPage(actualVisitcurrentPage + 1)"><i class="fas fa-angle-right"></i> </button>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import moment from 'moment';
import BarChart from '../Charts/BarChart.js'
import LineChart from '../Charts/LineChart.js'

export default {
    components: {
        BarChart,
        LineChart
    },
    data(){
        return{
            actualVisitedCustomer : null,
            scheduleForVisit:[],
            scheduleForVisitcurrentPage: 0,
            scheduleForVisititemsPerPage: 10,
            actualVisit:[],
            actualVisitcurrentPage: 0,
            actualVisititemsPerPage: 10,
            totalClientInteraction: '',
            notVisit:[]
        }
    },
    created(){
      this.actualVisitedCustomerData();
      this.fetchScheduleForVisit();
    },
    methods:{
        actualVisitedCustomerData ()
        {
            var count = [];

            this.actualVisitedCustomer = {
                labels: ['Jan','Feb','Mar','Apr','May', 'Jun' , 'Jul', 'Aug', 'Sept', 'Oct','Nov','Dec'],
                datasets: [
                    {
                        label: "Actual Visited Customer",
                        backgroundColor: 'rgba(45, 206 , 137, 0.5)',
                        pointBackgroundColor: 'white',
                        borderWidth: 1,
                        pointBorderColor: '#249EBF',
                        data: [1,2,3,4,5,6,7,8,10,11,12,3],
                    },
                    {
                        label: "Non Visited Customer",
                        backgroundColor: 'rgba(94,114,228, 0.5)',
                        pointBackgroundColor: 'white',
                        borderWidth: 1,
                        pointBorderColor: '#249EBF',
                        data: [11,2,23,4,15,6,7,18,10,2,3,12],
                    },
                ]
            }
        },
        getTotalMinutes(endTime, startTime){
            if(endTime && startTime){
                var ms = moment(endTime,"YYYY/MM/DD HH:mm a").diff(moment(startTime,"YYYY/MM/DD HH:mm a"));
                var d = moment.duration(ms);
                var minutes = Math.floor(d.asMinutes());
                return minutes; 
            }else{
                return 0;
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
                return "";
            }
            
                                            
        },
        showscheduleForVisit(){
            $('#showscheduleForVisit').modal('show');
        },
        showactualVisit(){
            $('#showactualVisit').modal('show');
        },
        fetchScheduleForVisit(){
            axios.get('/schedule-for-visit')
            .then(response => { 
                this.scheduleForVisit = response.data;
                this.fetchActualVisit();
                this.fetchTotalClientInteraction();
            })
            .catch(error => { 
                this.errors = error.response.data.errors;
            })
        },
        scheduleForVisitsetPage(pageNumber) {
            this.scheduleForVisitcurrentPage = pageNumber;
        },

        scheduleForVisitresetStartRow() {
            this.scheduleForVisitcurrentPage = 0;
        },

        scheduleForVisitshowPreviousLink() {
            return this.scheduleForVisitcurrentPage == 0 ? false : true;
        },

        scheduleForVisitshowNextLink() {
            return this.scheduleForVisitcurrentPage == (this.scheduleForVisittotalPages - 1) ? false : true;
        },
        fetchActualVisit(){
            let v = this;
            v.actualVisit = [];
            this.scheduleForVisit.forEach(function(schedule){
                if(schedule.attendances){
                    v.actualVisit.push(schedule);
                }else{
                    v.notVisit.push(schedule);
                }
            });     
        },
        actualVisitsetPage(pageNumber) {
            this.actualVisitcurrentPage = pageNumber;
        },
        actualVisitresetStartRow() {
            this.actualVisitcurrentPage = 0;
        },
        actualVisitshowPreviousLink() {
            return this.actualVisitcurrentPage == 0 ? false : true;
        },
        actualVisitshowNextLink() {
            return this.actualVisitcurrentPage == (this.actualVisittotalPages - 1) ? false : true;
        },
        fetchTotalClientInteraction(){
            let v = this;
            let total_minutes = 0;
            v.totalClientInteraction = '';
            this.scheduleForVisit.forEach(function(schedule){
                if(schedule.attendances){
                    total_minutes += v.getTotalMinutes(schedule.attendances.sign_out, schedule.attendances.sign_in);
                }
            });  
            if(total_minutes > 0){
                var hours = (total_minutes / 60);
                var rhours = Math.floor(hours);
                var minutes = (hours - rhours) * 60;
                var rminutes = Math.round(minutes);
                v.totalClientInteraction = rhours + " hour(s) and " + rminutes + " minute(s)";
            }
        },
    },
    computed:{
        scheduleForVisittotalPages() {
            return Math.ceil(this.scheduleForVisit.length / this.scheduleForVisititemsPerPage)
        },
        scheduleForVisitfilteredQueues() {
            var index = this.scheduleForVisitcurrentPage * this.scheduleForVisititemsPerPage;
            var queues_array = this.scheduleForVisit.slice(index, index + this.scheduleForVisititemsPerPage);

            if(this.scheduleForVisitcurrentPage >= this.scheduleForVisittotalPages) {
                this.scheduleForVisitcurrentPage = this.scheduleForVisittotalPages - 1
            }
            if(this.scheduleForVisitcurrentPage == -1) {
                this.scheduleForVisitcurrentPage = 0;
            }
            return queues_array;
        },
        actualVisittotalPages() {
            return Math.ceil(this.actualVisit.length / this.actualVisititemsPerPage)
        },
        actualVisitfilteredQueues() {
            var index = this.actualVisitcurrentPage * this.actualVisititemsPerPage;
            var queues_array = this.actualVisit.slice(index, index + this.actualVisititemsPerPage);

            if(this.actualVisitcurrentPage >= this.actualVisittotalPages) {
                this.actualVisitcurrentPage = this.actualVisittotalPages - 1
            }
            if(this.actualVisitcurrentPage == -1) {
                this.actualVisitcurrentPage = 0;
            }
            return queues_array;
        }
    }
}
</script>

<style>
    @media (min-width: 768px) {
        .modal-xl {
            width: 90%;
            max-width:1200px;
        }
    }
</style>
