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
                                    <h3 class="mb-0">SALES REPORT</h3>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="row ml-2">
                                <div class="col-md-3 float-left">
                                    <div class="form-group">
                                        <label for="name" class="form-control-label">Search</label> 
                                        <input type="text" class="form-control" placeholder="Search" v-model="keywords" id="name">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="start_date" class="form-control-label">Visit Status</label> 
                                        <multiselect
                                            v-model="filterVisit"
                                            :options="visitStatuses"
                                            :multiple="false"
                                            placeholder="Visit Status"
                                            id="selected_filter_visit"
                                        >
                                        </multiselect>
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
                                <div class="col-md-2">
                                    <button class="btn btn-sm btn-primary" @click="fetchCustomers()"> Apply Filter</button>
                                </div>

                            </div>
                        </div>
                        
                        <div class="row ml-3 mt-3 mb-3 pl-3">
                            <h4 class="mt-3">Legend:</h4>
                            <div class="col-sm-2">
                                <div class="icon icon-shape bg-success text-white rounded-circle shadow" style="font-size: .8rem!important;">{{countVisited}}</div>
                                <span class="text-sm"> Visited</span>
                            </div>
                            <div class="col-sm-2">
                                <div class="icon icon-shape bg-danger text-white rounded-circle shadow" style="font-size: .8rem!important;">{{countNonVisited}}</div>
                                <span class="text-sm"> Non Visited</span>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">Customer Code</th>
                                    <th scope="col">Customer Name</th>
                                    <th scope="col">Visit Count</th>
                                    <th scope="col">Last Visited Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="table_loading">
                                        <td colspan="15">
                                            <content-placeholders>
                                                <content-placeholders-heading :img="false" />
                                                <content-placeholders-text :lines="3" />
                                            </content-placeholders>
                                        </td>
                                    </tr>
                                    <tr v-for="(customer, p) in filteredQueues" v-bind:key="p">
                                        <td>{{ customer.customer_code }}</td>
                                        <td>{{ customer.name }}</td>
                                        <td v-if="customer.schedules.length  > 0">
                                            <button class="btn btn-sm btn-outline-success" @click="customerDetailsModal(customer)">{{ customer.schedules.length + ' visit(s)' }}</button>
                                        </td>
                                        <td v-else style="color:#f5365c">Non Visited</td>
                                        <td>{{ customer.last_visited.length > 0 ? customer.last_visited[0].date : '' }}</td>
                                    </tr>
                                </tbody>
                                
                            </table>
                        </div>
                        <div class="row mb-3 mt-3 ml-1" v-if="filteredQueues.length ">
                            <div class="col-6 text-left">
                                    <span>{{ filteredQueues.length }} Filtered Customer(s)</span><br>
                                    <span>{{ Object.keys(customers).length }} Total Customer(s)</span>
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
                </div>
            </div>

        </div>

        <div id="customer-details-modal" tabindex="1" class="customer-modal">
        <div class="customer-modal-content">
            <div class="customer-modal-header">
                <span class="customer-close" @click="closecustomerDetailsModal">&times;</span>
                <h3 class="mt-3">{{ customer.name }}</h3>
                <span class="text-default">{{ customer.google_address }}</span>
            </div>
            <div class="customer-modal-body mt-3">
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
    import Multiselect from 'vue-multiselect';
    import moment from 'moment';
    import VueContentPlaceholders from 'vue-content-placeholders'

    export default {
        components: {
            Multiselect
        },
        data(){
            return{
                customers:[],
                customer:[],
                startDate: '',
                endDate: '',
                copiedObject: [],
                errors: [],
                error: [],
                keywords: '',
                currentPage: 0,
                itemsPerPage: 50,
                visitStatuses: ['Visited','Non Visited'],
                filterVisit : '',
                countVisited : 0,
                countNonVisited : 0,
                customervisitList : [],
                customer_visits : [],
                visit_keywords : '',
                visitcurrentPage: 0,
                visititemsPerPage: 10,
                table_loading:false,

            }     
        },
        created(){
    
        },
        methods:{
            customerDetailsModal(customer_visit_details){
                this.customervisitList = [];
                var modal = document.getElementById('customer-details-modal');
                modal.style.display = "block";
                this.customer = customer_visit_details;
                this.customervisitList = customer_visit_details.schedules;
            },
            closecustomerDetailsModal(){
                var modal = document.getElementById('customer-details-modal');
                modal.style.display = "none";
            },
            fetchCustomers(){
                let v = this;
                this.customers = [];
                this.table_loading = true;
               
                axios.post('/sales-report-customer-data',{
                    startDate: this.startDate,
                    endDate: this.endDate
                })
                .then(response => {

                    this.customers = response.data;
                    v.countVisited = 0;
                    v.countNonVisited = 0;
                    this.customers.forEach((customer) => {
                       if(customer.schedules.length > 0){
                           v.countVisited += 1;
                       }else{
                           v.countNonVisited += 1;
                       }
                    });
                    this.table_loading = false;

                })
                .catch(error => { 
                    this.errors = error.response.data.errors;
                })
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
                return self.customers.filter(customer => {
                    
                    if(self.filterVisit){
                        if(self.filterVisit == "Visited"){
                            if(customer.schedules.length > 0){
                                return customer.name.toLowerCase().includes(this.keywords.toLowerCase()) || customer.customer_code.toLowerCase().includes(this.keywords.toLowerCase())
                            }
                        }
                        else if(self.filterVisit == "Non Visited"){
                            if(customer.schedules.length == 0){
                                return customer.name.toLowerCase().includes(this.keywords.toLowerCase()) || customer.customer_code.toLowerCase().includes(this.keywords.toLowerCase())
                            }
                        }  
                        else{
                            return customer.name.toLowerCase().includes(this.keywords.toLowerCase()) || customer.customer_code.toLowerCase().includes(this.keywords.toLowerCase())
                        }  
                    }else{
                        return customer.name.toLowerCase().includes(this.keywords.toLowerCase()) || customer.customer_code.toLowerCase().includes(this.keywords.toLowerCase())    
                    }

                });
            },
            totalPages() {
                return Math.ceil(this.filteredCustomers.length / this.itemsPerPage)
            },
            filteredQueues() {
                var index = this.currentPage * this.itemsPerPage;
                var queues_array = this.filteredCustomers.slice(index, index + this.itemsPerPage);

                if(this.currentPage >= this.totalPages) {
                    this.currentPage = this.totalPages - 1
                }

                if(this.currentPage == -1) {
                    this.currentPage = 0;
                }

                return queues_array;
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
            visittotalPages() {
                return Math.ceil(Object.values(this.filteredVisits).length / this.visititemsPerPage)
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
    .multiselect__tags {
        min-height: 47px!important;
        border: 1px solid #cad1d7;
    }

    .multiselect__single{
        padding-top: 6px!important;
    }

    .multiselect__select {
        padding-top: 10px!important;
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

</style>