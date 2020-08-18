<template>
    <div>
        <!-- Header -->
        <div class="header bg-green pb-8 pt-5 pt-md-8" style="min-height: 300px; background-image: url(/img/bg.jpg); background-size: cover; background-position: center bottom;">
            <span class="mask bg-gradient-success opacity-7"></span>
            <div class="container-fluid">
                <div class="header-body mt--4">

                    <div class="row align-items-center py-4">
                        <div class="col-lg-12 col-5 text-right">
                            <button class="btn btn-sm btn-neutral" @click="showdashboardFilter" ><i class="fas fa-filter"></i> Filters</button>
                            <a class="btn btn-sm btn-warning" href="/home" ><i class="fas fa-sync"></i> Switch Dashboard</a>
                        </div>
                    </div>
                    
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
                        <div class="col-xl-3 col-lg-6">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">Total Time Travel</h5>
                                            <span v-if="totalTravelTime" class="h2 font-weight-bold mb-0">{{totalTravelTime}}</span>
                                            <span v-else class="h2 font-weight-bold mb-0">-- -- -- -- --</span>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon icon-shape bg-gradient-orange text-white rounded-circle shadow">
                                                <i class="fas fa-clock"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mt-3 mb-0 text-muted text-sm">
                                        <span class="text-nowrap">Today</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6">
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

                <!-- <div class="col-xl-12 mb-5 mb-xl-0">
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
                </div> -->

                <!-- Most Customer Visit -->
                <div class="col-xl-6 mt-3 mb-5 mb-xl-0">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="mb-0">TSR Most Customer Visits (For the Month of {{currentMonth}})</h3>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 ml-2">
                                <div class="form-group">
                                    <label for="name" class="form-control-label">Search TSR</label> 
                                    <input type="text" class="form-control" placeholder="Search TSR" v-model="mostCustomerVisitedkeyword" id="mostCustomerVisitedkeyword">
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <h5 class="ml-3">TOTAL TSR: {{mostCustomerVisited.length}}</h5>
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">TSR Name</th>
                                    <th scope="col" @click="mostCustomerVisitedScheduleSort" style="cursor:pointer">
                                       Total Schedule
                                        <span v-if="mostCustomerVisitedScheduleSortby=='ASC'"><i class="fas fa-sort"></i></span>
                                        <span v-else>(Lowest to Highest) <i class="fas fa-sort"></i></span>
                                    </th>
                                    <th scope="col" @click="mostCustomerVisitedSort" style="cursor:pointer">
                                        Total Visits
                                        <span v-if="mostCustomerVisitedSortby=='ASC'"><i class="fas fa-sort"></i></span>
                                        <span v-else><i class="fas fa-sort"></i></span>
                                    </th>
                                    <th scope="col" @click="mostCustomerVisitedDwellTimeSort" style="cursor:pointer">
                                        Total Customer Dwell Time
                                        <span v-if="mostCustomerVisitedDwellTimeSortby=='ASC'"><i class="fas fa-sort"></i></span>
                                        <span v-else><i class="fas fa-sort"></i></span>
                                    </th>
                                    <th scope="col" @click="mostCustomerAverageTimeSort" style="cursor:pointer">
                                        Average Time per Visit
                                        <span v-if="mostCustomerAverageTimeSortSortby=='ASC'"><i class="fas fa-sort"></i></span>
                                        <span v-else><i class="fas fa-sort"></i></span>
                                    </th>
                                    <th scope="col" @click="mostCustomerAverageVisitSort" style="cursor:pointer">
                                        Average Visit per Day
                                        <span v-if="mostCustomerAverageVisitSortby=='ASC'"><i class="fas fa-sort"></i></span>
                                        <span v-else><i class="fas fa-sort"></i></span>
                                    </th>
                                    <th scope="col" @click="mostCustomerAverageExpenseSort" style="cursor:pointer">
                                        Average Expense per Day
                                        <span v-if="mostCustomerAverageExpenseSortby=='ASC'"><i class="fas fa-sort"></i></span>
                                        <span v-else><i class="fas fa-sort"></i></span>
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                   <tr v-for="(customer, s) in mostCustomerVisitedfilteredQueues" v-bind:key="s">
                                        <td>
                                            <h5>{{customer.name}}</h5>
                                            <small>{{customer.company}}</small>
                                        </td>
                                        <td>
                                            <h5>{{customer.total_count_schedule}}</h5>
                                        </td>
                                        <td>
                                            <h5>{{customer.total_count_visit}}</h5>
                                        </td>
                                        <td>
                                            <h5>{{customer.total_customer_dwell_time}}</h5>
                                        </td>
                                        <td>
                                            <h5>{{customer.average_time_per_visit}}</h5>
                                        </td>
                                        <td>
                                            <h5>{{customer.average_visit_per_day}}</h5>
                                        </td>
                                        <td>
                                            <h5>{{customer.average_expense_per_day}}</h5>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer">
                            <nav aria-label="...">
                                <ul class="pagination justify-content-end mb-0">
                                    <li class="page-item">
                                        <button :disabled="!mostCustomerVisitedshowPreviousLink()" class="page-link" v-on:click="mostCustomerVisitedsetPage(mostCustomerVisitedcurrentPage - 1)"> <i class="fas fa-angle-left"></i> </button>
                                    </li>
                                    <li class="page-item">
                                        Page {{ mostCustomerVisitedcurrentPage + 1 }} of {{ mostCustomerVisitedtotalPages }}
                                    </li>
                                    <li class="page-item">
                                        <button :disabled="!mostCustomerVisitedshowNextLink()" class="page-link" v-on:click="mostCustomerVisitedsetPage(mostCustomerVisitedcurrentPage + 1)"><i class="fas fa-angle-right"></i> </button>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                        
                    </div>
                </div>

                <!-- Current Month Total -->
                <div class="col-xl-6 mb-5 mb-xl-0 mb-5 ml--1">

                    <div class="col-xl-12 mt-3 mb-5 mb-xl-0 mb-5">
                        <div class="card shadow mb-3">
                            <div class="card-header border-0">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h4>Customer Visited per Area</h4>
                                        <small>Current Month</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <bar-chart :chart-data="datacustomerVisitedPerArea" :height="100"></bar-chart>

                                <div class="mt-2">
                                    <small>Luzon : <strong class="text-primary" v-if="dataPercentagePerArea.length > 0"> {{ dataPercentagePerArea[0].luzon != 'NaN' ? dataPercentagePerArea[0].luzon + '%' : "--" }} </strong> | </small>
                                    <small>Visayas : <strong class="text-primary" v-if="dataPercentagePerArea.length > 0"> {{ dataPercentagePerArea[1].visayas != 'NaN' ? dataPercentagePerArea[1].visayas + '%' : "--" }} </strong>  | </small>
                                    <small>Mindanao : <strong class="text-primary" v-if="dataPercentagePerArea.length > 0"> {{ dataPercentagePerArea[2].mindanao != 'NaN' ? dataPercentagePerArea[2].mindanao + '%' : "--" }} </strong>  </small>
                                </div>
                            </div>
                        
                        </div>
                    </div>

                    <!-- Work Start End Time -->
                    <div class="col-xl-12 mt-3 mb-5 mb-xl-0 mb-5">
                        <div class="card shadow">
                            <div class="card-header border-0">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="mb-0">Work Start and End Time</h3>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table align-items-center table-flush">
                                    <thead class="thead-light">
                                    <tr>
                                    
                                        <th scope="col" colspan="2">YESTERDAY</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>START TIME</td>
                                            <td>
                                                <h4 v-if="wokStartEndTime.yesterday_attendance_start" class="text-success">{{ wokStartEndTime.yesterday_attendance_start }}</h4>
                                                <h3 v-else class="text-success">- --  -- --:-- --</h3>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>END TIME</td>
                                            <td>
                                                <h4 v-if="wokStartEndTime.yesterday_attendance_end" class="text-danger">{{ wokStartEndTime.yesterday_attendance_end }}</h4>
                                                <h3 v-else class="text-danger">- --  -- --:-- --</h3>
                                            </td>
                                        </tr>
                                        
                                    </tbody>
                                    <thead class="thead-light">
                                    <tr>
                                    
                                        <th scope="col" colspan="2">Today</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <td>START TIME</td>
                                        <td>
                                            <h4  v-if="wokStartEndTime.today_attendance_start" >{{ wokStartEndTime.today_attendance_start }}</h4>
                                            <h3 v-else>- --  -- --:-- --</h3>
                                        </td>
                                    </tbody>
                                </table>
                            </div>
                        </div>  
                    </div>

                    <!-- Total Count for this month -->
                    <div class="col-xl-12 mt-3 mb-5 mb-xl-0 mb-5">
                        <div class="card shadow">
                            <div class="card-header border-0">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <h3 class="mb-0">Current Month : {{currentMonth}}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table align-items-center table-flush">
                                    <thead class="thead-light">
                                    <tr>
                                    
                                        <th scope="col" colspan="2">TOTAL</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td> <h5>VISITED CUSTOMER</h5> </td>
                                            <td> <h5>{{ currentMonthTotalVisited }}</h5> </td>
                                        </tr>
                                        <tr>
                                            <td> <h5>NOT VISITED CUSTOMER</h5> </td>
                                            <td> <h5>{{ currentMonthTotalNotVisited }}</h5> </td>
                                        </tr>
                                        <tr>
                                            <td> <h5>TRAVEL TIME</h5> </td>
                                            <td> <h5>{{ currentMonthTotalTravelTime }} </h5></td>
                                        </tr>
                                        <tr>
                                            <td> <h5>CLIENT INTERACTION</h5> </td>
                                            <td> <h5>{{ currentMonthTotalClientInteraction }} </h5> </td>
                                        </tr>
                                        <tr>
                                            <td> <h5>TOTAL SCHEDULES</h5> </td>
                                            <td> <h5>{{ currentMonthTotalSchedule }} </h5> </td>
                                        </tr>
                                        <tr>
                                            <td> <h5>MAPPED CUSTOMER</h5> </td>
                                            <td> <h5>{{ currentMonthTotalMapped }} </h5> </td>
                                        </tr>
                                        <tr>
                                            <td> <h5>NOT VISITED MAPPED CUSTOMER</h5> </td>
                                            <td> <h5>{{ currentMonthTotalScheduleMapped }} </h5> </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
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
                   
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-control-label">Search TSR</label> 
                                <input type="text" class="form-control" placeholder="Search TSR" v-model="scheduleForVisitkeyword" id="name">
                            </div>
                        </div>
                    </div>
                 
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

        <!-- Dashboard Filter -->
        <div class="modal fade" id="showdashboardFilter" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Dashboard Filter Settings</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 float-left">
                            <h4 v-if="dashboardFilter">Current Filter: {{dashboardFilter.company ? dashboardFilter.company.name : ""}}</h4>
                            <div class="form-group">
                                <label for="customerSelect" class="form-control-label">Select Company</label> 
                                <multiselect
                                        v-model="companyIds"
                                        :options="companyOptions"
                                        :multiple="false"
                                        track-by="id"
                                        :custom-label="customLabelCompany"
                                        placeholder="Select Company"
                                        id="selected_company"
                                >
                                </multiselect>
                                <span class="text-danger small" v-if="errors.selectedCompanies">{{ errors.selectedCompanies[0] }}</span>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" @click="saveDashboardFilter">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div>


    </div>
</template>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<script>
import moment from 'moment';
import BarChart from '../Charts/BarChart.js'
import LineChart from '../Charts/LineChart.js'

import Multiselect from 'vue-multiselect';

export default {
    components: {
        BarChart,
        LineChart,
        Multiselect,
    },
    data(){
        return{
            currentMonth: '',
            actualVisitedCustomer : null,

            scheduleForVisitkeyword : '',
            scheduleForVisit:[],
            scheduleForVisitcurrentPage: 0,
            scheduleForVisititemsPerPage: 10,


            actualVisitkeyword: '',
            actualVisit:[],
            actualVisitcurrentPage: 0,
            actualVisititemsPerPage: 10,

            totalClientInteraction: '',
            notVisit:[],

            totalTravelTime:'',
            totalTravelTimeData:[],

            currentMonthData : [],
            currentMonthTotalSchedule:'',
            currentMonthTotalVisited:'',
            currentMonthTotalNotVisited:'',
            currentMonthTotalClientInteraction:'',
            currentMonthTotalTravelTime:'',
            currentMonthTotalMapped:'',
            currentMonthTotalScheduleMapped:'',

            monthlyTotalTravelTimeData:[],

            mostCustomerVisitedkeyword:'',
            mostCustomerVisited:[],
            mostCustomerVisitedcurrentPage: 0,
            mostCustomerVisiteditemsPerPage: 5,

            mostCustomerVisitedSortby:'ASC',
            mostCustomerVisitedScheduleSortby:'ASC',

            mostCustomerVisitedDwellTimeSortby:'ASC',

            mostCustomerAverageTimeSortSortby:'ASC',

            mostCustomerAverageVisitSortby:'ASC',

            mostCustomerAverageExpenseSortby:'ASC',

            wokStartEndTime: [],
            
            dashboardFilter: [],
            companyIds:[],
            companyOptions:[],

            errors : [],


            //Demographic
           datacustomerVisitedPerArea : [],

           dataVisitedPerArea : [],
           dataSchedulePerArea : [],
           dataPercentagePerArea : [],
            
        }
    },
    created(){
        this.fetchDashboardFilter();
        this.fetchworkStartEndTime();
        var d = new Date();
        this.currentMonth = d.toLocaleString('default', { month: 'long' });
        this.actualVisitedCustomerData();
        this.fetchScheduleForVisit();
        this.getCurrentMonthTotal();
        this.fetchmostCustomerVisited();

        this.fetchCompany();
        
        this.fetchTotalTravelTime();

        this.fetchCurrentMonthTotalTravelTime();

        this.fetchCustomerVisitedPerArea();
        
        
        
    },
    methods:{
        //Demographic Customer Visited Per Area
        fetchCustomerVisitedPerArea(){
            axios.get('/customer-visited-per-area')
            .then(response => { 
                this.dataVisitedPerArea = response.data;
                this.fetchCustomerSchedulePerArea();
            })
            .catch(error => { 
                this.errors = error.response.data.error;
            })
        },
        fetchCustomerSchedulePerArea(){
            axios.get('/customer-schedule-per-area')
            .then(response => { 
                this.dataSchedulePerArea = response.data;
                //Fetch Percentage
                this.fetchPercentagePerArea();
                this.employeeCustomerVisitedPerAreaData();
            })
            .catch(error => { 
                this.errors = error.response.data.error;
            })
        },
        fetchPercentagePerArea(){

            let v = this;
            
            let luzon_percentage = 0;
            let visayas_percentage = 0;
            let mindanao_percentage = 0;

            let dataPercentage = [];
            if(v.dataVisitedPerArea && v.dataSchedulePerArea){
                //Luzon
                var luzon_schedules = v.dataSchedulePerArea[0] ? v.dataSchedulePerArea[0] : 0;
                var luzon_visited = v.dataVisitedPerArea[0] ? v.dataVisitedPerArea[0] : 0;
                luzon_percentage = luzon_visited/luzon_schedules * 100;

                dataPercentage.push({
                    'luzon' : luzon_percentage.toFixed(0)
                });
                //Visayas
                var visayas_schedules = v.dataSchedulePerArea[1] ? v.dataSchedulePerArea[1] : 0;
                var visayas_visited = v.dataVisitedPerArea[1] ? v.dataVisitedPerArea[1] : 0;
                visayas_percentage = visayas_visited/visayas_schedules * 100;

                dataPercentage.push({
                    'visayas' : visayas_percentage.toFixed(0)
                });

                //Mindanao
                var mindanao_schedules = v.dataSchedulePerArea[2] ? v.dataSchedulePerArea[2] : 0;
                var mindanao_visited = v.dataVisitedPerArea[2] ? v.dataVisitedPerArea[2] : 0;
                mindanao_percentage = mindanao_visited/mindanao_schedules * 100;

                dataPercentage.push({
                    'mindanao' : mindanao_percentage.toFixed(0)
                });
            }

            v.dataPercentagePerArea = dataPercentage;

            
        },
        employeeCustomerVisitedPerAreaData()
        {
            let v = this;
            var count = [];

            // regioncount.forEach(function(entry) {
            //     count.push(entry);
            // });
            
            this.datacustomerVisitedPerArea = {
                labels: ['Luzon' ,'Visayas', 'Mindanao'],
                datasets: [
                    {
                        label: 'Actual Visited',
                        backgroundColor: 'rgba(45,206,172, 0.5)',
                        pointBackgroundColor: 'white',
                        borderWidth: 1,
                        pointBorderColor: '#249EBF',
                        data: v.dataVisitedPerArea
                    },
                    {
                        label: 'Customer Schedules',
                        backgroundColor: 'rgba(245,54,92, 0.5)',
                        pointBackgroundColor: 'white',
                        borderWidth: 1,
                        pointBorderColor: '#249EBF',
                        data: v.dataSchedulePerArea
                    },
                ]
            }
        },
        // Dashboard Filter
        fetchDashboardFilter(){
            this.dashboardFilter = [];
            axios.get('/get-dashboard-filter')
            .then(response => { 
                this.dashboardFilter = response.data;
            })
            .catch(error =>{
                this.errors = error.response.data.errors;
            })
        },
        saveDashboardFilter(){
            axios.post('/save-dashboard-filter', {
                selectedCompanies: this.companyIds.id
            })
            .then(response =>{
                if(response.data){
                    alert('Filter Saved. Successfully Applied.');
                    location.reload();
                }
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            });
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
        customLabelCompany (company) {
            return `${company.name}`
        },
        showdashboardFilter(){
            $('#showdashboardFilter').modal('show');
        },

        //Work Start and End Time-------------------------------
        fetchworkStartEndTime(){
            let v = this;
            v.wokStartEndTime = [];
            axios.get('/work-start-time')
            .then(response => { 
                v.wokStartEndTime = response.data;
            })
            .catch(error => { 
                this.errors = error.response.data.errors;
            })
        },
        //Monthly Total -------------------------------
        getCurrentMonthTotal(){
            let v = this;
            axios.get('/monthly-actual-visited')
            .then(response => { 
                v.currentMonthData = response.data;
                let totalSchedule = v.currentMonthData.length;
                let totalVisited = 0;
                let totalNotVisited = 0;
                let totalMinutes = 0;
                let totalMapped = 0;
                let totalScheduleMapped = 0;

                v.currentMonthData.forEach(function(schedule){
                    if(schedule.type == '1'){
                        //Customer
                        if(schedule.attendances){
                            totalVisited += 1;
                            totalMinutes += v.getTotalMinutes(schedule.attendances.sign_out, schedule.attendances.sign_in);
                        }else{
                            totalNotVisited +=1;
                        }
                    }else if(schedule.type == '2'){
                        //Customer Mapping
                        if(schedule.attendances){
                            totalMapped +=1;
                        }else{
                            totalScheduleMapped +=1;
                        }
                    }
                    
                });

                if(totalMinutes > 0){
                    var hours = (totalMinutes / 60);
                    var rhours = Math.floor(hours);
                    var minutes = (hours - rhours) * 60;
                    var rminutes = Math.round(minutes);
                    v.currentMonthTotalClientInteraction = rhours + " hr(s) and " + rminutes + " min(s)";
                }

                v.currentMonthTotalSchedule = totalSchedule;
                v.currentMonthTotalVisited = totalVisited;
                v.currentMonthTotalNotVisited = totalNotVisited;
                v.currentMonthTotalMapped = totalMapped;
                v.currentMonthTotalScheduleMapped = totalScheduleMapped;
                
            })
            .catch(error => { 
                this.errors = error.response.data.errors;
            })
        },
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

        //Schedule For Visit
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

        //Total Travel Time
        fetchTotalTravelTime(){
            let v = this;
            v.totalTravelTime = '';
            v.totalTravelTimeData = [];

            axios.get('/total-travel-time')
            .then(response => { 
                let totalTravelTimeAll = response.data;

                totalTravelTimeAll.forEach((user,key) => {

                        let last_date_time = '';
                        let first = true;

                        let total_duration = 0;
                        let total_travel_time = 0;

                        let last_total_duration = '';
                        let last_total_travel_time = '';
    
                        let last_schedule_date = '';

                        let schedule_data = [];

                        let user_count_visited = 0;
                        let user_count_non_visited = 0;
                        let user_count_incomplete_attendance = 0;

                        let last_schedule_id = '';

                        if(user.schedules){

                            user.schedules.forEach((appointment,key) => {

                                var datetime = '';
                                var duration = '';
                                var travel_time = '';
                                var get_duration = 0;
                                var isCompleteAttendance = false;
                            
                                if(first){
                                    
                                    travel_time = '';
                                    first = false;

                                    if(appointment.attendances){
                                        user_count_visited += 1;
                                        last_date_time = appointment.attendances ? appointment.attendances.sign_out : '';
                                        
                                        if(appointment.attendances.sign_out){
                                            datetime = appointment.attendances.sign_in  + ' - ' + appointment.attendances.sign_out;
                                            isCompleteAttendance = true;
                                        }else{
                                            datetime = appointment.attendances.sign_in  + ' - No Sign Out';
                                            isCompleteAttendance = false;
                                            user_count_incomplete_attendance += 1;
                                        }
                                        
                                        duration = v.rendered(appointment.attendances.sign_out ? appointment.attendances.sign_out : "", appointment.attendances.sign_in ? appointment.attendances.sign_in : "");
                                        total_duration += v.getTotalMinutes(appointment.attendances.sign_out ? appointment.attendances.sign_out : "", appointment.attendances.sign_in ? appointment.attendances.sign_in : "");
                                            
                                    }else{
                                        datetime = '';
                                        duration = '';
                                        total_duration += 0;
                                        user_count_non_visited += 1;
                                    }

                                    last_schedule_date = appointment.date;
                                    last_schedule_id = appointment.id;

                                }else{

                                    if(appointment.attendances){
                                        if(last_schedule_id != appointment.id){
                                            user_count_visited += 1;
                                            if(last_schedule_date == appointment.date){
                                                travel_time = v.rendered(appointment.attendances.sign_in ? appointment.attendances.sign_in : '',last_date_time ? last_date_time : '');  
                                                total_travel_time += v.getTotalMinutes(appointment.attendances.sign_in ? appointment.attendances.sign_in : "",last_date_time ? last_date_time : '');  

                                            }else{
                                                //If Date is last
                                                travel_time = '';
                                                total_travel_time += 0;
                                                
                                                if(total_duration){
                                                    last_total_duration = parseFloat(total_duration / 60).toFixed(2);
                                                }else{
                                                    last_total_duration = '';
                                                }
                                                if(total_travel_time > 0){
                                                    last_total_travel_time = parseFloat(total_travel_time / 60).toFixed(2);
                                                }else{
                                                    last_total_travel_time = '';
                                                }

                                                total_duration = 0;
                                                total_travel_time = 0;

                                            }
                                            
                                            last_date_time = appointment.attendances.sign_out ? appointment.attendances.sign_out : appointment.attendances.sign_in;  

                                            if(appointment.attendances.sign_out){
                                                datetime = appointment.attendances.sign_in  + ' - ' + appointment.attendances.sign_out;
                                                isCompleteAttendance = true;
                                            }else{
                                                datetime = appointment.attendances.sign_in  + ' - No Sign Out';
                                                isCompleteAttendance = false;
                                                user_count_incomplete_attendance += 1;
                                                
                                            }

                                            duration = v.rendered(appointment.attendances.sign_out ? appointment.attendances.sign_out : "", appointment.attendances.sign_in ? appointment.attendances.sign_in : "");
                                            total_duration += v.getTotalMinutes(appointment.attendances.sign_out ? appointment.attendances.sign_out : "", appointment.attendances.sign_in ? appointment.attendances.sign_in : "");

                                        }
                                        
                                    }else{
                                        last_date_time = '';
                                        travel_time = '';
                                        datetime = '';
                                        duration = '';
                                        total_duration += 0;
                                        user_count_non_visited += 1;
                                    }

                                    last_schedule_date = appointment.date;
                                    
                                }

                                if(last_schedule_id != appointment.id){
                                    last_schedule_id = appointment.id;
                                }

                            });
                        }

                        if(total_duration){
                            last_total_duration = parseFloat(total_duration / 60).toFixed(2);
                        }else{
                            last_total_duration = '';
                        }
                        if(total_travel_time > 0){
                            last_total_travel_time = parseFloat(total_travel_time / 60).toFixed(2);
                        }else{
                            last_total_travel_time = '';
                        }

                        v.totalTravelTimeData.push({
                            'name' : user.name,
                            'last_total_travel_time' : last_total_travel_time,
                        });
                });

                let totalTravelTimeData = '';
                let totalTravelTime = 0;

                v.totalTravelTimeData.forEach((user) => {
                    var total_time = user.last_total_travel_time ? parseFloat(user.last_total_travel_time) : 0;
                    totalTravelTime += total_time;
                });

                if(totalTravelTime > 0){
                    var hours = totalTravelTime;
                    var rhours = Math.floor(hours);
                    var minutes = (hours - rhours) * 60;
                    var rminutes = Math.round(minutes);
                    v.totalTravelTime = rhours + " hr(s) and " + rminutes + " min(s)";
                }
            })
            .catch(error => { 
                this.errors = error.response.data.errors;
            })
        },

        fetchCurrentMonthTotalTravelTime(){
            let v = this;
            v.currentMonthTotalTravelTime = '';
            v.monthlyTotalTravelTimeData = [];

            axios.get('monthly-total-travel-time')
            .then(response => { 
                let totalTravelTimeAll = response.data;

                totalTravelTimeAll.forEach((user,key) => {

                        let last_date_time = '';
                        let first = true;

                        let total_duration = 0;
                        let total_travel_time = 0;

                        let last_total_duration = '';
                        let last_total_travel_time = '';
    
                        let last_schedule_date = '';

                        let schedule_data = [];

                        let user_count_visited = 0;
                        let user_count_non_visited = 0;
                        let user_count_incomplete_attendance = 0;

                        let last_schedule_id = '';

                        if(user.schedules){

                            user.schedules.forEach((appointment,key) => {

                                var datetime = '';
                                var duration = '';
                                var travel_time = '';
                                var get_duration = 0;
                                var isCompleteAttendance = false;
                            
                                if(first){
                                    
                                    travel_time = '';
                                    first = false;

                                    if(appointment.attendances){
                                        user_count_visited += 1;
                                        last_date_time = appointment.attendances ? appointment.attendances.sign_out : '';
                                        
                                        if(appointment.attendances.sign_out){
                                            datetime = appointment.attendances.sign_in  + ' - ' + appointment.attendances.sign_out;
                                            isCompleteAttendance = true;
                                        }else{
                                            datetime = appointment.attendances.sign_in  + ' - No Sign Out';
                                            isCompleteAttendance = false;
                                            user_count_incomplete_attendance += 1;
                                        }
                                        
                                        duration = v.rendered(appointment.attendances.sign_out ? appointment.attendances.sign_out : "", appointment.attendances.sign_in ? appointment.attendances.sign_in : "");
                                        total_duration += v.getTotalMinutes(appointment.attendances.sign_out ? appointment.attendances.sign_out : "", appointment.attendances.sign_in ? appointment.attendances.sign_in : "");
                                            
                                    }else{
                                        datetime = '';
                                        duration = '';
                                        total_duration += 0;
                                        user_count_non_visited += 1;
                                    }

                                    last_schedule_date = appointment.date;
                                    last_schedule_id = appointment.id;

                                }else{

                                    if(appointment.attendances){
                                        if(last_schedule_id != appointment.id){
                                            user_count_visited += 1;
                                            if(last_schedule_date == appointment.date){
                                                travel_time = v.rendered(appointment.attendances.sign_in ? appointment.attendances.sign_in : '',last_date_time ? last_date_time : '');  
                                                total_travel_time += v.getTotalMinutes(appointment.attendances.sign_in ? appointment.attendances.sign_in : "",last_date_time ? last_date_time : '');  

                                            }else{
                                                //If Date is last
                                                travel_time = '';
                                                total_travel_time += 0;
                                                
                                                if(total_duration){
                                                    last_total_duration = parseFloat(total_duration / 60).toFixed(2);
                                                }else{
                                                    last_total_duration = '';
                                                }
                                                if(total_travel_time > 0){
                                                    last_total_travel_time = parseFloat(total_travel_time / 60).toFixed(2);
                                                }else{
                                                    last_total_travel_time = '';
                                                }

                                                v.monthlyTotalTravelTimeData.push({
                                                    'name' : user.name,
                                                    'last_total_travel_time' : last_total_travel_time,
                                                });

                                                total_duration = 0;
                                                total_travel_time = 0;

                                            }
                                            
                                            last_date_time = appointment.attendances.sign_out ? appointment.attendances.sign_out : appointment.attendances.sign_in;  

                                            if(appointment.attendances.sign_out){
                                                datetime = appointment.attendances.sign_in  + ' - ' + appointment.attendances.sign_out;
                                                isCompleteAttendance = true;
                                            }else{
                                                datetime = appointment.attendances.sign_in  + ' - No Sign Out';
                                                isCompleteAttendance = false;
                                                user_count_incomplete_attendance += 1;
                                                
                                            }

                                            duration = v.rendered(appointment.attendances.sign_out ? appointment.attendances.sign_out : "", appointment.attendances.sign_in ? appointment.attendances.sign_in : "");
                                            total_duration += v.getTotalMinutes(appointment.attendances.sign_out ? appointment.attendances.sign_out : "", appointment.attendances.sign_in ? appointment.attendances.sign_in : "");

                                        }
                                        
                                    }else{
                                        last_date_time = '';
                                        travel_time = '';
                                        datetime = '';
                                        duration = '';
                                        total_duration += 0;
                                        user_count_non_visited += 1;
                                    }

                                    last_schedule_date = appointment.date;
                                    
                                }

                                if(last_schedule_id != appointment.id){
                                    last_schedule_id = appointment.id;
                                }

                            });
                        }

                        if(total_duration){
                            last_total_duration = parseFloat(total_duration / 60).toFixed(2);
                        }else{
                            last_total_duration = '';
                        }
                });

                let totalTravelTimeData = '';
                let totalTravelTime = 0;

                v.monthlyTotalTravelTimeData.forEach((user) => {
                    var total_time = user.last_total_travel_time ? parseFloat(user.last_total_travel_time) : 0;
                    totalTravelTime += total_time;
                });

                if(totalTravelTime > 0){
                    var hours = totalTravelTime;
                    var rhours = Math.floor(hours);
                    var minutes = (hours - rhours) * 60;
                    var rminutes = Math.round(minutes);
                    v.currentMonthTotalTravelTime = rhours + " hr(s) and " + rminutes + " min(s)";
                }
            })
            .catch(error => { 
                this.errors = error.response.data.errors;
            })
        },

        //Actual Visit
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

        //Total Client Interaction
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
                v.totalClientInteraction = rhours + " hr(s) and " + rminutes + " min(s)";
            }
        },

        //Most Customer Visit 
        fetchmostCustomerVisited(){
            let v = this;
             axios.get('/most-customer-visit')
            .then(response => { 
                var mostCustomerVisit = response.data;


                mostCustomerVisit.forEach(function(user){

                    var total_customer_dwell_time = '';
                    var average_time_per_visit = '';

                    if(user.total_customer_dwell_time > 0){
                        var hours = (user.total_customer_dwell_time / 60);
                        var rhours = Math.floor(hours);
                        var minutes = (hours - rhours) * 60;
                        var rminutes = Math.round(minutes);
                        total_customer_dwell_time = rhours + " hr(s) and " + rminutes + " min(s)";
                    }

                    if(user.average_time_per_visit > 0){
                        var hours = (user.average_time_per_visit / 60);
                        var rhours = Math.floor(hours);
                        var minutes = (hours - rhours) * 60;
                        var rminutes = Math.round(minutes);
                        average_time_per_visit = rhours + " hr(s) and " + rminutes + " min(s)";
                    }

                    v.mostCustomerVisited.push({
                        'company' : user.company,
                        'name' : user.name,
                        'total_count_schedule' : user.total_count_schedule,
                        'total_count_visit' : user.total_count_visit,
                        'total_customer_dwell_time' : total_customer_dwell_time,
                        'total_customer_dwell_time_min' : user.total_customer_dwell_time,
                        'average_time_per_visit_min' : user.average_time_per_visit,
                        'average_time_per_visit' : average_time_per_visit,
                        'average_visit_per_day' : user.average_visit_per_day,
                        'average_expense_per_day' : user.average_expense_per_day,
                    });

                });
            })
            .catch(error => { 
                this.errors = error.response.data.errors;
            })    
        },
        mostCustomerVisitedsetPage(pageNumber) {
            this.mostCustomerVisitedcurrentPage = pageNumber;
        },
        mostCustomerVisitedresetStartRow() {
            this.mostCustomerVisitedcurrentPage = 0;
        },
        mostCustomerVisitedshowPreviousLink() {
            return this.mostCustomerVisitedcurrentPage == 0 ? false : true;
        },
        mostCustomerVisitedshowNextLink() {
            return this.mostCustomerVisitedcurrentPage == (this.mostCustomerVisitedtotalPages - 1) ? false : true;
        },

        mostCustomerVisitedSort(){
            let v = this;
            if(v.mostCustomerVisitedSortby == 'ASC'){
                v.mostCustomerVisited.sort(function(a,b){
                        if (a.total_count_visit < b.total_count_visit)
                            return -1;
                        else if (a.total_count_visit == b.total_count_visit)
                            return 0;
                        else
                            return 1;
                    });
                v.mostCustomerVisitedSortby = 'DESC';
            }else{
                v.mostCustomerVisited.sort(function(a,b){
                        if (a.total_count_visit > b.total_count_visit)
                            return -1;
                        else if (a.total_count_visit == b.total_count_visit)
                            return 0;
                        else
                            return 1;
                    });
                v.mostCustomerVisitedSortby = 'ASC';
            }
        },

        mostCustomerVisitedScheduleSort(){
            let v = this;
            if(v.mostCustomerVisitedScheduleSortby == 'ASC'){
                v.mostCustomerVisited.sort(function(a,b){
                        if (a.total_count_schedule < b.total_count_schedule)
                            return -1;
                        else if (a.total_count_schedule == b.total_count_schedule)
                            return 0;
                        else
                            return 1;
                    });
                v.mostCustomerVisitedScheduleSortby = 'DESC';
            }else{
                v.mostCustomerVisited.sort(function(a,b){
                        if (a.total_count_schedule > b.total_count_schedule)
                            return -1;
                        else if (a.total_count_schedule == b.total_count_schedule)
                            return 0;
                        else
                            return 1;
                    });
                v.mostCustomerVisitedScheduleSortby = 'ASC';
            }
        },

        mostCustomerVisitedDwellTimeSort(){
            let v = this;
            if(v.mostCustomerVisitedDwellTimeSortby == 'ASC'){
                v.mostCustomerVisited.sort(function(a,b){
                        if (a.total_customer_dwell_time_min < b.total_customer_dwell_time_min)
                            return -1;
                        else if (a.total_customer_dwell_time_min == b.total_customer_dwell_time_min)
                            return 0;
                        else
                            return 1;
                    });
                v.mostCustomerVisitedDwellTimeSortby = 'DESC';
            }else{
                v.mostCustomerVisited.sort(function(a,b){
                        if (a.total_customer_dwell_time_min > b.total_customer_dwell_time_min)
                            return -1;
                        else if (a.total_customer_dwell_time_min == b.total_customer_dwell_time_min)
                            return 0;
                        else
                            return 1;
                    });
                v.mostCustomerVisitedDwellTimeSortby = 'ASC';
            }
        },

        mostCustomerAverageTimeSort(){
            let v = this;
            if(v.mostCustomerAverageTimeSortSortby == 'ASC'){
                v.mostCustomerVisited.sort(function(a,b){
                        if (a.average_time_per_visit_min < b.average_time_per_visit_min)
                            return -1;
                        else if (a.average_time_per_visit_min == b.average_time_per_visit_min)
                            return 0;
                        else
                            return 1;
                    });
                v.mostCustomerAverageTimeSortSortby = 'DESC';
            }else{
                v.mostCustomerVisited.sort(function(a,b){
                        if (a.average_time_per_visit_min > b.average_time_per_visit_min)
                            return -1;
                        else if (a.average_time_per_visit_min == b.average_time_per_visit_min)
                            return 0;
                        else
                            return 1;
                    });
                v.mostCustomerAverageTimeSortSortby = 'ASC';
            }
        },

        mostCustomerAverageVisitSort(){
            let v = this;
            if(v.mostCustomerAverageVisitSortby == 'ASC'){
                v.mostCustomerVisited.sort(function(a,b){
                        if (a.average_visit_per_day < b.average_visit_per_day)
                            return -1;
                        else if (a.average_visit_per_day == b.average_visit_per_day)
                            return 0;
                        else
                            return 1;
                    });
                v.mostCustomerAverageVisitSortby = 'DESC';
            }else{
                v.mostCustomerVisited.sort(function(a,b){
                        if (a.average_visit_per_day > b.average_visit_per_day)
                            return -1;
                        else if (a.average_visit_per_day == b.average_visit_per_day)
                            return 0;
                        else
                            return 1;
                    });
                v.mostCustomerAverageVisitSortby = 'ASC';
            }
        },

        mostCustomerAverageExpenseSort(){
            let v = this;
            if(v.mostCustomerAverageExpenseSortby == 'ASC'){
                v.mostCustomerVisited.sort(function(a,b){
                        if (a.average_expense_per_day < b.average_expense_per_day)
                            return -1;
                        else if (a.average_expense_per_day == b.average_expense_per_day)
                            return 0;
                        else
                            return 1;
                    });
                v.mostCustomerAverageExpenseSortby = 'DESC';
            }else{
                v.mostCustomerVisited.sort(function(a,b){
                        if (a.average_expense_per_day > b.average_expense_per_day)
                            return -1;
                        else if (a.average_expense_per_day == b.average_expense_per_day)
                            return 0;
                        else
                            return 1;
                    });
                v.mostCustomerAverageExpenseSortby = 'ASC';
            }
        },

    },
    computed:{
        //Schedule for Visit
        filteredScheduleForVisit(){
            let self = this;
            return self.scheduleForVisit.filter(schedule => {
                return schedule.user.name.toLowerCase().includes(this.scheduleForVisitkeyword.toLowerCase())
            });
        },
        scheduleForVisittotalPages() {
            return Math.ceil(this.filteredScheduleForVisit.length / this.scheduleForVisititemsPerPage)
        },
        scheduleForVisitfilteredQueues() {
            var index = this.scheduleForVisitcurrentPage * this.scheduleForVisititemsPerPage;
            var queues_array = this.filteredScheduleForVisit.slice(index, index + this.scheduleForVisititemsPerPage);

            if(this.scheduleForVisitcurrentPage >= this.scheduleForVisittotalPages) {
                this.scheduleForVisitcurrentPage = this.scheduleForVisittotalPages - 1
            }
            if(this.scheduleForVisitcurrentPage == -1) {
                this.scheduleForVisitcurrentPage = 0;
            }
            return queues_array;
        },
        //Actual Visit Total
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
        },
        //Most Customer Visit
        filteredMostCustomerVisited(){
            let self = this;
            return self.mostCustomerVisited.filter(tsr => {
                return tsr.name.toLowerCase().includes(this.mostCustomerVisitedkeyword.toLowerCase())
            });
        },
        mostCustomerVisitedtotalPages() {
            return Math.ceil(this.filteredMostCustomerVisited.length / this.mostCustomerVisiteditemsPerPage)
        },
        mostCustomerVisitedfilteredQueues() {
            var index = this.mostCustomerVisitedcurrentPage * this.mostCustomerVisiteditemsPerPage;
            var queues_array = this.filteredMostCustomerVisited.slice(index, index + this.mostCustomerVisiteditemsPerPage);

            if(this.mostCustomerVisitedcurrentPage >= this.mostCustomerVisitedtotalPages) {
                this.mostCustomerVisitedcurrentPage = this.mostCustomerVisitedtotalPages - 1
            }
            if(this.mostCustomerVisitedcurrentPage == -1) {
                this.mostCustomerVisitedcurrentPage = 0;
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
