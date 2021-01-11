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
                                    <h3 class="mb-0">Individual Performance</h3>
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
                                <div class="col-md-3 float-left">
                                    <div class="form-group">
                                        <label class="form-control-label" for="role">Select Month</label>
                                        <select class="form-control" v-model="month">
                                            <option v-for="(select_month,c) in months" v-bind:key="c" :value="select_month.id"> {{ select_month.name }}</option>
                                        </select>
                                        <span class="text-danger" v-if="errors.month  ">{{ errors.month[0] }}</span>
                                    </div>
                                 </div>
                                <div class="col-md-3 float-left">
                                    <div class="form-group">
                                        <label class="form-control-label" for="role">Select Year</label>
                                        <select class="form-control" v-model="year">
                                            <option v-for="(select_year,c) in years" v-bind:key="c" :value="select_year.id"> {{ select_year.name }}</option>
                                        </select>
                                        <span class="text-danger" v-if="errors.year  ">{{ errors.year[0] }}</span>
                                    </div>
                                 </div>

                                 <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-control-label" for="role">Company</label>
                                        <select class="form-control" v-model="company">
                                            <option v-for="(company,c) in companies" v-bind:key="c" :value="company.id"> {{ company.name }}</option>
                                        </select>
                                        <span class="text-danger" v-if="errors.company  ">{{ errors.company[0] }}</span>
                                    </div>
                                </div>

                                 <div class="col-md-12 text-right">
                                    <button class="btn btn-sm btn-primary" @click="fetchFilterIndividualPerformance"> Filter</button>

                                    <json-excel class = "btn btn-sm btn-success" :data= "individualPerformances" :fields = "json_fields" name= "Individual Performance.xls">Export to Excel</json-excel> 

                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <h4 class="ml-3" v-if="loading"><i>Please wait. Loading...</i></h4>
                            <h4 class="ml-3" v-else>Total Filtered TSR : {{ totalFilterIndividualPerformances }}</h4>
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                   
                                    <th scope="col">TSR</th>
                                    <th scope="col">Company</th>
                                    <th scope="col">Period</th>
                                    <!-- <th scope="col">Total Customer Schedule</th> -->
                                    <th scope="col" @click="totalVisitedSort" style="cursor:pointer">
                                        Total Customer Visited <br>
                                        <span v-if="totalVisitedSortby=='ASC'"> (Highest to Lowest) <i class="fas fa-sort"></i></span>
                                        <span v-else>(Lowest to Highest) <i class="fas fa-sort"></i></span>
                                    </th>
                                    <th scope="col" @click="totalCustomerOrderSort" style="cursor:pointer">
                                        Total Customer Order <br>
                                        <span v-if="totalCustomerOrderSortby=='ASC'"> (Highest to Lowest) <i class="fas fa-sort"></i></span>
                                        <span v-else>(Lowest to Highest) <i class="fas fa-sort"></i></span>
                                    </th>
                                    <th scope="col" @click="totalVolumeOrderSort" style="cursor:pointer">
                                        Total Volume Order <br>
                                        <span v-if="totalVolumeOrderSortby=='ASC'"> (Highest to Lowest) <i class="fas fa-sort"></i></span>
                                        <span v-else>(Lowest to Highest) <i class="fas fa-sort"></i></span>
                                    </th>
                                    <th scope="col" @click="totalCustomerSpendSort" style="cursor:pointer">
                                        Total Customer Spend <br>
                                        <span v-if="totalCustomerSpendSortby=='ASC'"> (Highest to Lowest) <i class="fas fa-sort"></i></span>
                                        <span v-else>(Lowest to Highest) <i class="fas fa-sort"></i></span>
                                    </th>
                                    <th>Monthly Quota</th>
                                </tr>
                                </thead>
                                <tbody>
                                   <tr v-for="(user,c) in filteredQueues" v-bind:key="c">
                                        <td>{{ user.name }}</td>
                                        <td>{{ user.company }}</td>
                                        <td>{{ user.month_year }}</td>
                                        <!-- <td>{{ user.total_schedule }}</td> -->
                                        <td>{{ user.total_visited }}</td>
                                        <td>{{ user.total_customer_order }}</td>
                                        <td>{{ user.total_customer_volume_order }}</td>
                                        <td>{{ user.total_customer_expenses }}</td>
                                        <td>{{ user.monthly_qouta }}</td>
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
    </div>
</template>

<script>

import JsonExcel from 'vue-json-excel'


export default {
    components: {
        JsonExcel
    },
    data() {
        return {
            months: [
                    {
                        "id": '01',
                        "name": "January",
                    },
                    {
                        "id": '02',
                        "name": "February",
                    },
                    {
                        "id": '03',
                        "name": "March",
                    },
                    {
                        "id": '04',
                        "name": "April",
                    },
                    {
                        "id": '05',
                        "name": "May",
                    },
                    {
                        "id": '06',
                        "name": "June",
                    },
                    {
                        "id": '07',
                        "name": "July",
                    },
                    {
                        "id": '08',
                        "name": "August",
                    },
                    {
                        "id": '09',
                        "name": "September",
                    },
                    {
                        "id": '10',
                        "name": "October",
                    },
                    {
                        "id": '11',
                        "name": "November",
                    },
                    {
                        "id": '12',
                        "name": "December",
                    },
                ],
            years : [],
            keywords : '',
            month: '',
            year: '',
            company: '',
            companies: [],
            errors: [],
            individualPerformances: [],
            currentPage: 0,
            itemsPerPage: 10,
            json_fields: {
                'TSR NAME' : 'name',
                'COMPANY': 'company',
                'PERIOD': 'month_year',
                'TOTAL CUSTOMER VISITED' : 'total_visited',
                'TOTAL CUSTOMER ORDER' : 'total_customer_order',
                'TOTAL CUSTOMER VOLUME ORDER' : 'total_customer_volume_order',
                'TOTAL CUSTOMER SPEND' : 'total_customer_expenses',
            },
            totalVisitedSortby : 'DESC',
            totalCustomerOrderSortby : 'DESC',
            totalVolumeOrderSortby : 'DESC',
            totalCustomerSpendSortby : 'DESC',
            loading: false,
            totalFilterIndividualPerformances: '',
        }
    },
    created(){
        this.fetchIndividualPerformances();
        this.fetchCompanies();
        this.fetchYears();
    },
    methods: {
        fetchYears(){
            axios.get('/year-options')
            .then(response => {
                this.years = response.data;
            })
            .catch(error => { 
                this.errors = error.response.data.errors;
            })
        },
        fetchFilterIndividualPerformance(){
            let v = this;
            this.individualPerformances = [];
            this.loading = true;
            axios.post('/individual-performance-filter-data',{
                month: v.month,
                year: v.year,
                company: v.company
            })
            .then(response => {
                this.individualPerformances = response.data;
                this.loading = false;
            })
            .catch(error => { 
                this.errors = error.response.data.errors;
                this.loading = false;
            })
        },
        fetchIndividualPerformances(){
            this.loading = true;
            axios.get('/individual-performance-data')
            .then(response => {
                this.individualPerformances = response.data;
                this.totalVisitedSort();
                this.loading = false;
            })
            .catch(error => {
                this.errors = error.response.data.errors;
                this.loading = false;
            })
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
        totalVisitedSort(){
            let v = this;
            if(v.totalVisitedSortby == 'ASC'){
                v.individualPerformances.sort(function(a,b){
                        if (a.total_visited < b.total_visited)
                            return -1;
                        else if (a.total_visited == b.total_visited)
                            return 0;
                        else
                            return 1;
                    });
                v.totalVisitedSortby = 'DESC';
            }else{
                v.individualPerformances.sort(function(a,b){
                        if (a.total_visited > b.total_visited)
                            return -1;
                        else if (a.total_visited == b.total_visited)
                            return 0;
                        else
                            return 1;
                    });
                v.totalVisitedSortby = 'ASC';
            }
        },
        totalCustomerOrderSort(){
            let v = this;
            if(v.totalCustomerOrderSortby == 'ASC'){
                v.individualPerformances.sort(function(a,b){
                        if (a.total_customer_order < b.total_customer_order)
                            return -1;
                        else if (a.total_customer_order == b.total_customer_order)
                            return 0;
                        else
                            return 1;
                    });
                v.totalCustomerOrderSortby = 'DESC';
            }else{
                v.individualPerformances.sort(function(a,b){
                        if (a.total_customer_order > b.total_customer_order)
                            return -1;
                        else if (a.total_customer_order == b.total_customer_order)
                            return 0;
                        else
                            return 1;
                    });
                v.totalCustomerOrderSortby = 'ASC';
            }
        },
        totalVolumeOrderSort(){
            let v = this;
            if(v.totalVolumeOrderSortby == 'ASC'){
                v.individualPerformances.sort(function(a,b){
                        if (a.total_customer_volume_order < b.total_customer_volume_order)
                            return -1;
                        else if (a.total_customer_volume_order == b.total_customer_volume_order)
                            return 0;
                        else
                            return 1;
                    });
                v.totalVolumeOrderSortby = 'DESC';
            }else{
                v.individualPerformances.sort(function(a,b){
                        if (a.total_customer_volume_order > b.total_customer_volume_order)
                            return -1;
                        else if (a.total_customer_volume_order == b.total_customer_volume_order)
                            return 0;
                        else
                            return 1;
                    });
                v.totalVolumeOrderSortby = 'ASC';
            }
        },
        totalCustomerSpendSort(){
            let v = this;
            if(v.totalCustomerSpendSortby == 'ASC'){
                v.individualPerformances.sort(function(a,b){
                        if (a.total_customer_expenses < b.total_customer_expenses)
                            return -1;
                        else if (a.total_customer_expenses == b.total_customer_expenses)
                            return 0;
                        else
                            return 1;
                    });
                v.totalCustomerSpendSortby = 'DESC';
            }else{
                v.individualPerformances.sort(function(a,b){
                        if (a.total_customer_expenses > b.total_customer_expenses)
                            return -1;
                        else if (a.total_customer_expenses == b.total_customer_expenses)
                            return 0;
                        else
                            return 1;
                    });
                v.totalCustomerSpendSortby = 'ASC';
            }
        },
    },
    computed:{
        filteredIndividualPerformances(){
            let self = this;

            return self.individualPerformances.filter(user => {
                return user.name.toLowerCase().includes(this.keywords.toLowerCase())
            });
        },
        totalPages() {
            return Math.ceil(this.filteredIndividualPerformances.length / this.itemsPerPage)
        },
        filteredQueues() {
            this.totalFilterIndividualPerformances  = this.filteredIndividualPerformances.length;
            var index = this.currentPage * this.itemsPerPage;
            var queues_array = this.filteredIndividualPerformances.slice(index, index + this.itemsPerPage);

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