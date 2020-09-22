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
                                    <h3 class="mb-0">Expense/Budget per TSR</h3>
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
                                    <button class="btn btn-sm btn-primary" @click="fetchFilter"> Filter</button>

                                    <json-excel class = "btn btn-sm btn-success" :data= "allTsr" :fields = "json_fields" name= "expense_budget.xls">Export to Excel</json-excel> 


                                    <a class="btn btn-sm btn-default mr-2" href="/expenses-report"> Back to Expense Report</a>  
                                </div>
                            </div>
                        </div>


                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">TSR NAME</th> 
                                    <th scope="col">TOTAL PLANNED BUDGET</th>
                                    <th scope="col">TOTAL BUDGET BALANCE</th> 
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(tsr, s) in filteredExpensePerIo" v-bind:key="s">
                                        <td>
                                            {{ tsr.name }} <br>
                                            <small>{{ tsr.company }}</small>
                                        </td>
                                        <td>{{ tsr.total_planned_budget }}</td>
                                        <td>{{ tsr.total_budget_balance }}</td>
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
                allTsr: [],
                errors: [],
                currentPage: 0,
                itemsPerPage: 10,
                json_fields : {
                    'TSR NAME': 'name',
                    'COMPANY': 'company',
                    'TOTAL PLANNED BUDGET': {
                        callback: (value) => {
                           return value.total_planned_budget;
                        }
                    },
                    'TOTAL BUDGET BALANCE': {
                        callback: (value) => {
                           return value.total_budget_balance;
                        }
                    },
                }
              
            }
        },
        created () {
           
            this.fetchYears();
            this.fetchCompanies();
            // this.getInitial();
        },
        methods: {
            getInitial() {
                let v = this;
                v.allTsr = [];
                axios.get('/expense-io-checker')
                .then(response => {
                    v.allTsr = response.data.data;
                    v.current_page = response.data.current_page;
                    v.current_page = response.data.current_page;
                    v.total = response.data.total;
                    v.errors = []; 
                })
                .catch(error => {
                    v.errors = error.response.data.errors;
                })
            },
            fetchFilter(){
                let v = this;
                v.allTsr = [];
                axios.post('/expense-io-report-data', {
                    month: v.month ? v.month : "",
                    year: v.year ? v.year : "",
                    company: v.company ? v.company : ""
                })
                .then(response => {
                    v.allTsr = response.data;
                    v.errors = []; 
                })
                .catch(error => {
                    v.errors = error.response.data.errors;
                })
            },
            fetchYears(){
                axios.get('/year-options')
                .then(response => {
                    this.years = response.data;
                })
                .catch(error => { 
                    this.errors = error.response.data.errors;
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
            },fetchYears(){
                axios.get('/year-options')
                .then(response => {
                    this.years = response.data;
                })
                .catch(error => { 
                    this.errors = error.response.data.errors;
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
            }
        },
        computed:{
            filteredExpensePerIo(){
                let self = this;
                return self.allTsr.filter(expense => {
                    return expense.name.toLowerCase().includes(this.keywords.toLowerCase())
                });
            },
            totalPages() {
                return Math.ceil(this.filteredExpensePerIo.length / this.itemsPerPage)
            },
            filteredQueues() {
                var index = this.currentPage * this.itemsPerPage;
                var queues_array = this.filteredExpensePerIo.slice(index, index + this.itemsPerPage);

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
