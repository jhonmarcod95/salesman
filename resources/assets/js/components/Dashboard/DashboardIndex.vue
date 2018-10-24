<template>
    <div>
        <!-- Header -->
        <div class="header bg-green pb-8 pt-5 pt-md-8">
            <div class="container-fluid">
                <div class="header-body">
                    <!-- Card stats -->
                    <div class="row">
                        <div class="col-xl-3 col-lg-6" @click="openCurrentVisiting" style="cursor:pointer">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">Current Visiting</h5>
                                            <span class="h2 font-weight-bold mb-0">{{ visiting.length }}</span>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon icon-shape bg-danger text-white rounded-circle shadow">
                                                <i class="fas fa-store"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mt-3 mb-0 text-muted text-sm">
                                        <!-- <span class="text-success mr-2"><i class="fa fa-arrow-up"></i> 3.48%</span> -->
                                        <span class="text-nowrap">Click for more details</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6" @click="openCompletedTask" style="cursor:pointer">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">Completed Task</h5>
                                            <span class="h2 font-weight-bold mb-0">{{ completed.length }}</span>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon icon-shape bg-yellow text-white rounded-circle shadow">
                                                <i class="fas fa-tasks"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mt-3 mb-0 text-muted text-sm">
                                        <!-- <span class="text-warning mr-2"><i class="fas fa-arrow-down"></i> 1.10%</span> -->
                                        <span class="text-nowrap">Click for more details</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6" @click="openTodaysSchedule" style="cursor:pointer">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">Today's Schedule</h5>
                                            <span class="h2 font-weight-bold mb-0">{{ todays.length }}</span>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                                                <i class="fas fa-calendar"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mt-3 mb-0 text-muted text-sm">
                                        <!-- <span class="text-danger mr-2"><i class="fas fa-arrow-down"></i> 3.48%</span> -->
                                        <span class="text-nowrap">Click for more details</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">Request</h5>
                                            <span class="h2 font-weight-bold mb-0">0</span>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon icon-shape bg-info text-white rounded-circle shadow">
                                                <i class="fas fa-bell"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mt-3 mb-0 text-muted text-sm">
                                        <!-- <span class="text-success mr-2"><i class="fas fa-arrow-up"></i> 12%</span> -->
                                        <span class="text-nowrap">Click for more details</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid mt--7">
            <div class="row mt-5">
                <div class="col-xl-8 mb-5 mb-xl-0">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="mb-0">Recent Time In</h3>
                                </div>
                                <div class="col text-right">
                                    <!-- <a href="#!" class="btn btn-sm btn-primary">See all</a> -->
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <!-- Projects table -->
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">TSR</th>
                                    <th scope="col">Customer</th>
                                    <th scope="col">IN</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(recent, r) in recents" v-bind:key="r">
                                        <td>{{ recent.user.name }}</td>
                                        <td>{{ recent.schedule.name }}</td>
                                        <td>{{ moment(recent.sign_in ).format('lll') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="mb-0">Performance Summary</h3>
                                </div>
                                <div class="col text-right">
                                    <!-- <a href="#!" class="btn btn-sm btn-primary">See all</a> -->
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <!-- Projects table -->
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">Type</th>
                                    <th scope="col">Scheduled</th>
                                    <th scope="col">Completed</th>
                                    <th scope="col"> Percentage</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th scope="row"> Customer </th>
                                    <td> {{ customerCount.length }} </td>
                                    <td> {{ customerCompletedCount.length }}</td>
                                    <td v-if="customerPercentage"> {{ customerPercentage }}% </td>
                                    <td v-else> 0% </td>
                                </tr>
                                <tr>
                                    <th scope="row"> Mapping </th>
                                    <td> {{ mappingCount.length }} </td>
                                    <td> {{ mappingCompletedCount.length }}</td>
                                    <td v-if="mappingPercentage"> {{ mappingPercentage }}% </td>
                                    <td v-else> 0% </td>
                                </tr>
                                <tr>
                                    <th scope="row"> Event </th>
                                    <td> {{ eventCount.length }} </td>
                                    <td> {{ eventCompletedCount.length }} </td>
                                    <td v-if="eventPercentage"> {{ eventPercentage }}% </td>
                                    <td v-else> 0% </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-xl-8 mb-5 mb-xl-0">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="mb-0">Schedule Summary</h3>
                                </div>
                                <div class="col text-right">
                                    <!-- <a href="#!" class="btn btn-sm btn-primary">See all</a> -->
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <!-- Projects table -->
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">TSR</th>
                                    <th scope="col">Scheduled</th>
                                    <th scope="col">Completed</th>
                                    <th scope="col"> Percentage</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(tsrUnique, tsrU) in tsrUniques" v-bind:key="tsrU">
                                        <td>{{ tsrUnique[0].user.name }}</td>
                                        <td>{{ tsrUnique.length }}</td>
                                        <td>{{ countCompleted(tsrUnique) }}</td>
                                        <td v-if="completedPercentage !== 0">{{ completedPercentage }}% </td>
                                        <td v-else>0% </td>  
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Current Visiting Modal -->
        <div class="modal fade" id="currentVisitingModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Current Visiting</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                            <tr>
                                <th scope="col">TSR</th>
                                <th scope="col">Customer</th>
                                <th scope="col">In</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(visit, v) in visiting" v-bind:key="v">
                                    <td>{{ visit.user.name }}</td>
                                    <td> Customer: {{ visit.schedule.name }} <br></td>
                                    <td><span>{{ moment(visit.sign_in ).format('lll') }}</span> </td>
                                </tr>
                                <tr v-if="!visiting.length">
                                    <td>No data available in the table</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div>

        <!--Completed task Modal -->
        <div class="modal fade" id="completedTaskModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Completed Task</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">TSR</th>
                                    <th scope="col">Customer</th>
                                    <th scope="col">In / Out</th>
                                    <th scope="col">Rendered</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(complete, c) in completed" v-bind:key="c">
                                        <td>{{ complete.user.name }}</td>
                                        <td>{{ complete.schedule.name }}</td>
                                        <td> 
                                            <span> IN: {{ moment(complete.sign_in ).format('lll') }}</span> <br>
                                            <span> OUT: {{ moment(complete.sign_out ).format('lll') }}</span> 
                                        </td>
                                        <td> {{ rendered(complete.sign_out, complete.sign_in) }}</td>  
                                    </tr>
                                    <tr v-if="!completed.length">
                                        <td>No data available in the table</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div>

        <!--Todays Schedule Modal -->
        <div class="modal fade" id="todaysScheduleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Today's Schedule</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                            <tr>
                                <th scope="col">TSR</th>
                                <th scope="col">Customer</th>
                                <th scope="col">Start Time/ End Time</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(today, t) in todays" v-bind:key="t">
                                    <td>{{ today.user.name }}</td>
                                    <td>
                                        Customer : {{ today.name }} <br>
                                        Address : {{ today.address }}  
                                    </td>
                                    <td> 
                                        <span> Start Time : {{ moment(today.start_time , 'HH:mm').format('hh:mm a') }}</span> <br>
                                        <span> End Time : {{ moment(today.end_time , 'HH:mm').format('hh:mm a') }}</span> 
                                    </td>
                                </tr>
                                <tr v-if="!todays.length">
                                    <td>No data available in the table</td>
                                </tr>
                            </tbody>
                        </table>
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
export default {
    data(){
        return{
            visiting: [],
            completed: [],
            todays: [],
            errors: [],
            recents: [],
            tsrUniques: [],
            customerCount: '',
            customerCompletedCount: '',
            customerPercentage: '',
            mappingCount: '',
            mappingCompletedCount: '',
            mappingPercentage: '',
            eventCount: '',
            eventCompletedCount: '',
            eventPercentage: '',
            completedPercentage: 0,
        }
    },
    created(){
        this.fetchCurrentVisiting();
        this.fetchCompletedTask();
        this.fetchTodaysSchedule();
    },
    methods:{
        moment,
        openCurrentVisiting(){
            $('#currentVisitingModal').modal('show');
        },
        openCompletedTask(){
            $('#completedTaskModal').modal('show');
        },
        openTodaysSchedule(){
            $('#todaysScheduleModal').modal('show');
        },
        fetchCurrentVisiting(){
            axios.get('/attendances-visiting')
            .then(response =>{
                this.visiting = response.data;
                this.recents = this.visiting.slice(0, 5);
            })
            .catch(error => { 
                this.errors = error.response.data.errors;
            })
        },
        fetchCompletedTask(){
             axios.get('/attendances-completed')
            .then(response =>{
                this.completed = response.data;
            })
            .catch(error =>{ 
                this.errors = error.response.data.errors;
            })
        },
        fetchTodaysSchedule(){
             axios.get('/schedules-todays')
            .then(response => {
                this.todays = response.data;
                this.countCustomer();
                this.countMapping();
                this.countEvent();
                this.tsrGetUnique();
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            })
        },
        countCustomer(){
            this.customerCount = this.todays.filter(item => item.type == 1);
            if(this.customerCount.length){
                var attendance = this.customerCount.filter(item => item.attendances  !== null);
                if(attendance.length){
                    this.customerCompletedCount = attendance.filter(item => item.attendances.sign_out !== null);
                    this.customerPercentage = Math.round((this.customerCompletedCount.length/this.customerCount.length) * 100);
                }
            }
        },
        countMapping(){
            this.mappingCount = this.todays.filter(item => item.type == 2);
            if(this.mappingCount.length){
                var attendance = this.mappingCount.filter(item => item.attendances  !== null);
                if(attendance.length){
                    this.mappingCompletedCount = attendance.filter(item => item.attendances.sign_out !== null);
                    this.mappingPercentage = Math.round((this.mappingCompletedCount.length/this.mappingCount.length) * 100);
                }
            }
        },
        countEvent(){
            this.eventCount = this.todays.filter(item => item.type == 3);
            if(this.eventCount.length){
                var attendance = this.eventCount.filter(item => item.attendances  !== null);
                if(attendance.length){
                    this.eventCompletedCount = attendance.filter(item => item.attendances.sign_out !== null);
                    this.eventPercentage = Math.round((this.eventCompletedCount.length/this.eventCount.length) * 100);
                }
            }
        },
        countCompleted(sched){
            this.completedPercentage = 0;
            var attendance = sched.filter(item => item.attendances  !== null);
            if(attendance.length){
                var tsr = attendance.filter(item => item.attendances.sign_out !== null);
                if(tsr.length){
                    this.completedPercentage = Math.round((attendance.length/tsr.length) * 100);
                    return tsr.length;
                }
            }
            return 0;
        },
        rendered(endTime, startTime){
            var ms = moment(endTime,"YYYY/MM/DD HH:mm a").diff(moment(startTime,"YYYY/MM/DD HH:mm a"));
            var d = moment.duration(ms);
            var hours = Math.floor(d.asHours());
            var minutes = moment.utc(ms).format("mm");

            return hours + 'h '+ minutes+' min.';
                                            
        },
        tsrGetUnique(){  
            axios.get('/schedules-user-today')
            .then(response => {
                this.tsrUniques = response.data;
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            })
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
