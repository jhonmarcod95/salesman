<template>
      <div>
        <loader v-if="loading"></loader>
        <div class="header bg-green pb-6 pt-5 pt-md-6"></div>
        <div class="container-fluid mt--7">
            <!-- Table -->
            <div class="row mt-5">
                <div class="col">
                    <div class="card shadow">

                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="mb-0">Unposted Expenses</h3>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="row ml-2">
                                <div class="col-md-4 float-left">
                                    <div class="form-group">
                                        <label for="name" class="form-control-label">Search</label> 
                                        <input type="text" class="form-control" placeholder="Search" v-model="keywords" id="name">
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
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="year" class="form-control-label">Year</label> 
                                        <select class="form-control" v-model="year" @change="getyear">
                                            <option v-for="(year, y) in years" v-bind:key="y">{{ year }}</option>
                                        </select> 
                                        <span class="text-danger" v-if="errors.year  ">{{ errors.year[0] }}</span>

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="year" class="form-control-label">Date</label> 
                                        <select class="form-control" v-model="week">
                                            <option v-for="(week, w) in weeks" v-bind:key="w">{{ week }} </option>
                                        </select> 
                                        <span class="text-danger" v-if="errors.week">{{ errors.week[0] }}</span>
                                    </div>
                                </div>
                                <div class="col-md-2 align-content-center">
                                    <button class="btn btn-primary" @click="fetchExpenses"> Filter</button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">TSR</th>
                                    <th scope="col">Expense Submitted</th>
                                    <th scope="col">Total Expenses</th>
                                </tr>
                                </thead>
                                <tbody v-if="expenses.length">
                                    <tr v-for="(expense, e) in filteredQueues" v-bind:key="e">
                                        <td class="text-right">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-target="#viewModal"
                                                    @click="getExpenseSubmitted(e,expense.user.name,expense.user.expenses,expense.payment_header_detail_error)">View</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ expense.user.name }}</td>
                                        <td>{{ expense.user.expenses.length }}</td>
                                        <td>PHP {{ countTotalExpenses(expense.user.expenses) }}</td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                       <tr>
                                           <td>No data available in the table</td>
                                       </tr>
                                </tbody>
                            </table>
                        </div>
                       <div class="card-footer py-4" v-if="this.expenses.length">
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

        <!-- View Expense Modal -->
        <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <span class="closed" data-dismiss="modal">&times;</span>
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCompanyLabel">Expenses Submitted</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div class="row">
                        <div class="col"><h3>TSR: {{ selectedUser.id }} - {{ selectedUser.name }}</h3></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                            <tr>
                                <th scope="col">Attachment</th>
                                <th scope="col">Type of Expense</th>
                                <th scope="col">Date</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Status</th>
                                <th scope="col">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(expenseBy, e) in expenseByTsr" v-bind:key="e">
                                    <td><a :href="imageLink+expenseBy.attachment" target="__blank"><img class="rounded-circle" :src="imageLink+expenseBy.attachment" style="height: 70px; width: 70px" @error="noImage"></a></td>
                                    <td>{{ expenseBy.expenses_type.name }}</td>
                                    <td>{{ moment(expenseBy.created_at).format('ll') }}</td>
                                    <td>PHP {{ expenseBy.amount.toFixed(2) }} </td>
                                    <td :class="expenseBy.deleted_at? 'text-danger': 'text-primary'">{{ expenseBy.deleted_at? 'Deleted': 'Unposted' }}</td>
                                    <td>
                                        <button v-if="!expenseBy.deleted_at" class="btn btn-sm btn-danger" @click="deleteExpense(expenseBy)">Delete</button>
                                        <button v-else class="btn btn-sm btn-secondary" @click="cancelDeletion(expenseBy)">Restore</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-danger text-center mt-3 mb-3">
                        <span>Unable to post due to errors</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                            <tr>
                                <th scope="col">Type</th>
                                <th scope="col">Id</th>
                                <th scope="col">Number</th>
                                <th scope="col">Description</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(errorDetail, e) in errorDetails" v-bind:key="e">
                                    <td>{{ errorDetail.return_message_type }}</td>
                                    <td>{{ errorDetail.return_message_id }}</td>
                                    <td>{{ errorDetail.return_message_number }}</td>
                                    <td>{{ errorDetail.return_message_description }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                </div>
            </div>
        </div>
        
    </div>
</template>
<script>
import moment from 'moment';
import loader from '../Loader';
import Swal from 'sweetalert2';

export default {
    components: { loader },
    data(){
        return{
            currentIndex: '',
            expenses: [],
            expenses_id: [],
            expenseByTsr: [],
            weeks: [],
            current_week: '',
            year: '',
            week: '',
            startDate: '',
            endDate: '',
            tsrName: '',
            date: '',
            submit: '',
            companies: [],
            company: '',
            errors: [],
            keywords: '',
            currentPage: 0,
            itemsPerPage: 10,
            loading: false,
            errorDetails: []
        }
    },
    created(){
        this.fetchCompanies();
    },
    methods:{
        moment,
        noImage(event){
            event.target.src = window.location.origin+'/img/brand/no-image.png';
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
        getExpenseSubmitted(index,name,expenses,errors){
            this.currentIndex = index;
            this.tsrName = name? name: this.filteredQueues[index].user.name;
            this.expenseByTsr = expenses? expenses: this.filteredQueues[index].user.expenses;
            this.errorDetails = errors? errors: this.filteredQueues[index].payment_header_detail_error;
        },
        deleteExpense(target){
            Swal.fire({
                title: "Delete Expense?",
                text: 'Delete expense type ' + target.expenses_type.name + ' with amount PHP ' + target.amount + '?',
                showCancelButton: true,
                showConfirmButton: true,
                confirmButtonColor: "#e24444",
                confirmButtonText: "Delete",
            })
            .then(response => {
                if (response.isConfirmed) {
                    axios.delete(`/expense-unposted-delete/${target.id}`)
                    .then(response => {
                        this.fetchExpenses();
                    });
                    // .catch(error => { 
                    //     this.errors = error.response.data.errors;
                    // });
                }
            });
        },
        cancelDeletion(target){
            Swal.fire({
                title: "Restore Expense?",
                text: 'Restore expense type ' + target.expenses_type.name + ' with amount PHP ' + target.amount + '?',
                showCancelButton: true,
                showConfirmButton: true,
                confirmButtonText: "Restore",
            })
            .then(response => {
                if (response.isConfirmed) {
                    axios.delete(`/expense-unposted-restore/${target.id}`)
                    .then(response => {
                        this.fetchExpenses();
                    })
                    // .catch(error => { 
                    //     this.errors = error.response.data.errors;
                    // });
                }
            });
        },
        fetchExpenses(){
            this.loading = true;
            var dates = this.week.split('-');
            var date1 = dates[0];
            var date2= dates[1];
            
            var moment1 = moment(date1);
            var moment2 = moment(date2);
            this.startDate = moment1.format('YYYY-MM-DD');
            this.endDate = moment2.format('YYYY-MM-DD');
            axios.post('/expense-unposteds', {
                startDate: this.startDate,
                endDate: this.endDate,
                company: this.company
            })
            .then(response => {
                this.expenses = response.data;
                this.errors = [];
                this.loading = false;
                //updates the view modal if open
                if (this.currentIndex || this.currentIndex == 0) this.getExpenseSubmitted(this.currentIndex);
            })
            .catch(error => {
                this.errors = error.response.data.errors;
                this.loading = false;
            })
        },
        getyear(){
            var start = moment(this.year).day('Monday');
            var end   = moment();
            var day   = 1;

            var result = [];
            var current = start.clone();
            result.push(moment(start).format('ll') +' - ' + moment(start.add(6, 'days')).format('ll'));
            while (current.day(7 + day).isBefore(end)) {
                result.push(moment(current.clone()).format('ll') +' - ' + moment(current.clone().add(6, 'days')).format('ll'));
            }
            this.weeks = result;
            this.current_week = this.weeks[this.weeks.length - 1];
        },
        countExpenseSubmitted(expenses){
            var totalSubmitted = 0;
            expenses.forEach(element => {
              totalSubmitted = totalSubmitted + element.expenses_model_count;
            });
            return totalSubmitted;
        },
        countTotalExpenses(expenses){
            var totalExpenses = 0;
            expenses.forEach(element => {
                totalExpenses = totalExpenses + element.amount;
            });
            return totalExpenses.toFixed(2);
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
        filteredExpenses(){
            let self = this;
            return Object.values(self.expenses).filter(expense => {
                return expense.user.name.toLowerCase().includes(this.keywords.toLowerCase())
            });
        },
        totalPages() {
            return Math.ceil(Object.values(this.filteredExpenses).length / this.itemsPerPage)
        },
        filteredQueues() {
            var index = this.currentPage * this.itemsPerPage;
            var queues_array = Object.values(this.filteredExpenses).slice(index, index + this.itemsPerPage);

            if(this.currentPage >= this.totalPages) {
                this.currentPage = this.totalPages - 1
            }

            if(this.currentPage == -1) {
                this.currentPage = 0;
            }

            return queues_array;
        },
        years () {
            const year = new Date().getFullYear()
            return Array.from({length: year - 2018}, (value, index) => 2019 + index)
        },
        imageLink(){
            return window.location.origin+'/storage/';
        }
    },
}
</script>
