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
                                    <h3 class="mb-0">Expenses Report</h3>
                                </div>
                                <div><a class="btn btn-sm btn-info mr-2" href="/expenses-top-spender-report"> Expense Top Spender</a></div>
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
                                    <button class="btn btn-sm btn-primary mt-4" @click="fetchExpenses">
                                        Filter
                                        <span v-if="fetchingExpense">...</span>
                                    </button> 
                                </div>

                                <div class="col-md-12">
                                    <span>Attachment: {{ filteredExpensesStats.expensesCount }}</span> |
                                    <span>Verified: {{ filteredExpensesStats.verifiedCount }}</span> |
                                    <span class="text-warning">Unverified: {{ filteredExpensesStats.unverifiedCount }}</span>
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
                                    <th scope="col" v-if="expenseVerifierRole || salesHeadRole">Verified Count</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Total Expenses</th>
                                </tr>
                                </thead>
                                <tbody v-if="expenses.length">
                                    <tr v-for="(expense, e) in filteredQueues" v-bind:key="e">
                                        <td class="text-right" v-if="userLevel != 5">
                                            <button class="btn btn-sm text-black-50" @click="fetchExpenseByTsr(expense.id, expense.user.name, expense.created_at)">View</button>
                                            <!-- <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a class="dropdown-item" href="javascript:void(0)"  @click="fetchExpenseByTsr(expense.id, expense.user.name, expense.created_at)">View</a>
                                                </div>
                                            </div> -->
                                        </td>
                                        <td v-else></td>
                                        <td>{{ !isEmpty(expense.user) ? expense.user.name : '' }}</td>
                                        <td>{{ expense.expenses_model_count  }}</td>
                                        <td v-if="expenseVerifierRole || salesHeadRole">{{ expense.verified_expense_count  }}</td>
                                        <td>{{ moment(expense.created_at).format('ll') }}</td>
                                        <td>PHP {{ countTotalExpenses(expense) }}</td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                       <tr>
                                           <td>No data available in the table</td>
                                       </tr>
                                </tbody>
                            </table>
                        </div>
                       <div class="card-footer py-4" v-if="expenses.length">
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
                        <div class="col"><h3>TSR: {{ this.tsrName }}</h3></div>
                        <div class="col"><h3>Date: {{ moment(this.date).format('ll') }} </h3></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                            <tr>
                                <th scope="col" v-if="expenseVerifierRole || salesHeadRole">Verify</th>
                                <th scope="col">Attachment</th>
                                <th scope="col">Type of Expense</th>
                                <th scope="col">Date</th>
                                <th scope="col">Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(expenseBy, e) in expenseByTsr" v-bind:key="e">
                                    <td v-if="expenseVerifierRole || salesHeadRole">
                                        <div v-if="expenseBy.is_verified">
                                            <div>Verified</div>
                                            <div v-if="salesHeadRole" class="btn btn-warning btn-sm mt-2" @click="verifyExpense(expenseBy,'unset')">Unverify</div>
                                        </div>
                                        <button type="button" class="btn btn-primary btn-sm" v-else @click="verifyExpense(expenseBy,'verify')" :disabled="verifiyingId">
                                            Verify
                                            <span v-if="verifiyingId == expenseBy.id">...</span>
                                        </button>
                                    </td>
                                    <td> <a :href="imageLink+expenseBy.attachment" target="__blank"><img class="rounded-circle" :src="imageLink+expenseBy.attachment" style="height: 70px; width: 70px" @error="noImage"></a></td>
                                    <td>{{ expenseBy.expenses_type.name }}</td>
                                    <td>{{ moment(expenseBy.created_at).format('ll') }}</td>
                                    <td>PHP {{ expenseBy.amount.toFixed(2) }} </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-round btn-fill" data-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
export default {
    props:['userLevel','userRole','expenseVerifier'],
    data(){
        return{
            fetchingExpense: false,
            expenses: [],
            expenseByTsr: [],
            startDate: '',
            endDate: '',
            tsrName: '',
            date: '',
            errors: [],
            keywords: '',
            currentPage: 0,
            itemsPerPage: 10,
            verifiyingId: null
        }
    },
    created(){

    },
    methods:{
        moment,
        noImage(event){
            event.target.src = window.location.origin+'/img/brand/no-image.png';
        },
        countTotalExpenses(expense){
            var totalExpenses = 0;
            expense.expenses_model.forEach(element => {
                totalExpenses = totalExpenses + element.amount;
            });
            return totalExpenses.toFixed(2);
        },
        fetchExpenses(){
            this.errors = [];
            this.fetchingExpense = true;
            axios.post('/expense-report-bydate', {
                startDate: this.startDate,
                endDate: this.endDate
            })
            .then(response => {
                this.expenses = response.data;
                this.errors = []; 
                this.fetchingExpense = false;
            })
            .catch(error => {
                this.errors = error.response.data.errors;
                this.fetchingExpense = false;
            })
        },
        fetchExpenseByTsr(id,name,created){
            axios.get(`/expense-report/${id}`)
            .then(response => { 
                this.expenseByTsr = response.data;
                this.tsrName = name;
                this.date = created;
                $('#viewModal').modal('show');
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            })
        },
        doFetchExpenseByTsr(id) {
            axios.get(`/expense-report/${id}`)
            .then(response => { 
                this.verifiyingId = null;
                this.expenseByTsr = response.data;
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

        verifyExpense(expense, mode) {
            let vm = this;
            let alertStatus = mode == 'verify' ? "mark as verified" : "reset to unverified"
            if(confirm(`Are you sure you want to ${alertStatus} this attachment?`)) {
                vm.verifiyingId = expense.id;
                axios.post(`/verify-expense-attachment/${expense.id}`,{mode})
                .then(res => {
                    vm.doFetchExpenseByTsr(expense.expenses_entry_id)
                })
            }
        },
        isEmpty(data) {
            return _.isEmpty(data);
        }
    },
    computed:{
        filteredExpenses(){
            let self = this;
            return self.expenses.filter(expense => {
                return expense.user.name.toLowerCase().includes(this.keywords.toLowerCase())
            });
        },
        filteredExpensesStats(){
            let self = this;
            let expensesCount = 0;
            let verifiedCount = 0;
            let unverifiedCount = 0;

            if(!_.isEmpty(self.filteredExpenses)) {
                _.each(self.filteredExpenses, (item) => {
                    verifiedCount = verifiedCount + item.verified_expense_count;
                    unverifiedCount = unverifiedCount + (item.expenses_model_count - item.verified_expense_count);
                    expensesCount = expensesCount + item.expenses_model_count;
                })
            }

            return {expensesCount, verifiedCount, unverifiedCount}
        },
        totalPages() {
            return Math.ceil(this.filteredExpenses.length / this.itemsPerPage)
        },
        filteredQueues() {
            var index = this.currentPage * this.itemsPerPage;
            var queues_array = this.filteredExpenses.slice(index, index + this.itemsPerPage);

            if(this.currentPage >= this.totalPages) {
                this.currentPage = this.totalPages - 1
            }

            if(this.currentPage == -1) {
                this.currentPage = 0;
            }

            return queues_array;
        },
        imageLink(){
            // return 'http://salesforce.lafilgroup.net:8666/storage/';
            return window.location.origin+'/storage/';
        },
        expenseVerifierRole() {
            let userLevel = [
                4, // Coordinator
                9  // IT
            ];

            return _.includes(userLevel, this.userLevel) || this.expenseVerifier;
        },
        salesHeadRole() {
            let userRole = [
                4, // VP/Sales Head
                1  // IT
            ];
            return _.includes(userRole, this.userRole);
        }
    },
}
</script>

<style>

</style>
