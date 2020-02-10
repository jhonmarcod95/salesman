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
                                    <h3 class="mb-0">SALES ACTIVITY CUSTOMER REPORT</h3>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="row ml-2">
                                <div class="col-md-5 float-left">
                                    <div class="form-group">
                                        <label for="name" class="form-control-label">Search</label> 
                                        <input type="text" class="form-control" placeholder="Search by (Customer Code, Customer Name)" v-model="keywords" id="name">
                                    </div>
                                </div>

                                <div class="col-md-3 float-left">
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
                                        <span class="text-danger small" v-if="errors.selectedCompany">{{ errors.selectedCompany[0] }}</span>
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
                                                placeholder="Status"
                                                id="selected_status"
                                        >
                                        </multiselect>
                                        <span class="text-danger small" v-if="errors.selectedStatuses">{{ errors.selectedStatuses[0] }}</span>

                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <button class="btn btn-sm btn-primary" @click="applyFilterCustomers"> Apply Filter</button>
                                </div>

                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">Customer Code</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Status</th>
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
                                        <td>
                                            {{ customer.customer_code }}
                                            <br>
                                            <small>Prospect Code: {{ customer.prospect_id ? customer.prospect_id : 'N/A'  }}</small>
                                        </td>
                                        <td>
                                            <h4>{{ customer.name }}<br>
                                                <small>{{customer.google_address}}</small>
                                            </h4>
                                            <div v-if="customer.customer_activity.length > 0">
                                                <div v-for="(activity, c) in customer.customer_activity" v-bind:key="c">
                                                    <span v-if="activity.activity_description =='Prospect'" style="color:#5E72E4;"><strong><i class="fas fa-circle"></i> {{ activity.activity_description }}</strong> <small style="color:#848DA4">Date: {{ activity.activity_date }}</small></span>
                                                    <span v-else-if="activity.activity_description =='Active'" style="color:#2DCE89;"><strong><i class="fas fa-circle"></i> {{ activity.activity_description }}</strong> <small style="color:#848DA4">Date: {{ activity.activity_date }}</small></span>
                                                    <span v-else-if="activity.activity_description =='Inactive'" style="color:#FB6340;"><strong><i class="fas fa-circle"></i> {{ activity.activity_description }}</strong> <small style="color:#848DA4">As of: {{ activity.activity_date }}</small></span>
                                                    <span v-else-if="activity.activity_description =='Closed'" style="color:#F5365C;"><strong><i class="fas fa-circle"></i> {{ activity.activity_description }}</strong> <small style="color:#848DA4">As of: {{ activity.activity_date }}</small></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            {{ customer.statuses ? customer.statuses.description : "" }}
                                        </td>
                                    </tr>
                                    
                                </tbody>
                                
                            </table>
                        </div>
                        <div class="row mb-3 mt-3 ml-1" v-if="filteredQueues.length ">
                            <div class="col-6 text-left">
                                <span>{{ filteredQueues.length }} Total Filtered Customers</span><br>
                                    <span>{{ Object.keys(customers).length }} Total Customers</span>
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
                customer:[],
                customers:[],
                customerActivities:[],
                activities:[],
                customersData:[],
                companyIds:[],
                companyOptions:[],
                statusIds:[],
                statusOptions:[],
                errors: [],
                error: [],
                keywords: '',
                table_loading:false,
                activities_loading:true,
                records_not_found:false,
                currentPage: 0,
                itemsPerPage: 50,
            }     
        },
        created(){
            this.fetchCompany();
            this.fetchStatus();
        },
        methods:{
            customLabelStatus (status) {
                return `${status.description}`
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
            applyFilterCustomers(){
                this.table_loading = true;
                this.errors = [];
                this.customers = [];
                axios.post('/sales-activity-customer-all', {
                    selectedCompany: this.companyIds,
                    selectedStatuses: this.statusIds,
                })
                .then(response =>{
                    this.customers = response.data;
                    this.table_loading = false;
                })
                .catch(error => {
                    this.table_loading = false;
                    this.errors = error.response.data.errors;
                });

            },
            customerActivityModal(customer_activities){
                this.customervisitList = [];
                var modal = document.getElementById('customer-activities-modal');
                modal.style.display = "block";
                this.customer = customer_activities;
                this.fetchCustomerActivities();
            },
            closecustomerActivityModal(){
                var modal = document.getElementById('customer-activities-modal');
                modal.style.display = "none";
            },
            rendered(endTime, startTime){ 
                if(endTime && startTime){
                    var ms = moment(endTime,"YYYY/MM/DD HH:mm a").diff(moment(startTime,"YYYY/MM/DD HH:mm a"));
                    var d = moment.duration(ms);
                    return d.years() + 'year(s) ' + d.months() +  'month(s) ' + d.days() + 'day(s)';
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
            fetchCompany(){
                axios.get('/companies-filter-all')
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

        },
        computed:{
            filteredCustomers(){
                let self = this;
                return self.customers.filter(customer => {
                    return customer.name.toLowerCase().includes(self.keywords.toLowerCase()) || customer.customer_code.toLowerCase().includes(self.keywords.toLowerCase())
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
    .customer-activity-modal {
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