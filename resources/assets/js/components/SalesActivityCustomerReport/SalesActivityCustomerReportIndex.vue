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
                                        <input type="text" class="form-control" placeholder="Search by (Customer Code, Customer Name)" v-model="search_keywords" id="name">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-sm btn-primary" @click="fetchCustomers()"> Search</button>
                                </div>

                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">Customer Code</th>
                                    <th scope="col">Customer Name</th>
                                    <th scope="col">Address</th>
                                    <th scope="col">Activity</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Duration</th>
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
                                    <tr v-for="(customer, p) in customers" v-bind:key="p">
                                        <td>{{ customer.customer_code }}</td>
                                        <td>{{ customer.name }}</td>
                                        <td>{{ customer.address }}</td>
                                        <td>{{ customer.activity }}</td>
                                        <td>{{ customer.date }}</td>
                                        <td>{{ customer.duration }}</td>
                                    </tr>
                                </tbody>
                                
                            </table>
                        </div>
                        <!-- <div class="row mb-3 mt-3 ml-1" v-if="filteredQueues.length ">
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
                        </div> -->
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
                customers:[],
                customersData:[],
                errors: [],
                error: [],
                search_keywords: '',
                table_loading:false,

            }     
        },
        created(){
    
        },
        methods:{
            fetchCustomers(){
                let v = this;
                this.customers = [];
                this.customersData = [];
                this.table_loading = true;
               
                axios.post('/sales-activity-customer-report-data',{
                    searchData: this.search_keywords,
                })
                .then(response => {
                    v.customersData = response.data;

                    let last_customer_code = '';
                    let last_activity_date = '';
                    let is_first = true;
                    let duration = 0;
                    v.customersData.forEach((customer,key) => {
                        if(is_first){
                            last_customer_code = customer.customer_code;
                            last_activity_date = customer.activity_date;
                            duration = '';
                            is_first = false;
                        }else{
                            if(last_customer_code == customer.customer_code){
                                duration = v.rendered(customer.activity_date,last_activity_date);
                                last_customer_code = customer.customer_code;
                                last_activity_date = customer.activity_date;

                            }else{
                                duration = 0;
                                last_customer_code = customer.customer_code;
                                last_activity_date = customer.activity_date;
                            }
                        }

                        v.customers.push({
                            'customer_code' : customer.customer_code,
                            'name' : customer.name,
                            'address' : customer.google_address,
                            'activity' : customer.activity_description,
                            'date' : customer.activity_date,
                            'duration' : duration
                        });
                    });
                    this.table_loading = false;
                })
                .catch(error => { 
                    this.errors = error.response.data.errors;
                })
            },
          

            rendered(endTime, startTime){ 
                if(endTime && startTime){
                    var ms = moment(endTime,"YYYY/MM/DD HH:mm a").diff(moment(startTime,"YYYY/MM/DD HH:mm a"));
                    var d = moment.duration(ms);
                    return d.years() + 'year(s) ' + d.months() +  'month(s) ' + d.days() + 'day(s)';
                }else{
                    return '-';
                }                                  
            }
        },
        computed:{

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