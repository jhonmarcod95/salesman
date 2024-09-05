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
                                <div class="d-flex">
                                    <div><a class="btn btn-sm btn-default mr-2" href="/expenses-report"> Expenses Report</a></div>
                                    <div v-if="salesHeadRole"><a class="btn btn-sm btn-outline-default mr-2" href="/dms-received-expense"> DMS Submitted Expense</a></div>
                                    <div><a class="btn btn-sm btn-outline-default mr-2" href="/expenses-top-spender-report"> Expense Top Spender</a></div>
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
                                        <label for="start_date" class="form-control-label">Start Date</label> 
                                        <input type="date" id="start_date" class="form-control form-control-alternative" v-model="startDate">
                                        <span class="text-danger" v-if="errors.startDate"> {{ errors.startDate[0] }} </span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="end_date" class="form-control-label">End Date</label> 
                                        <input type="date" id="end_date" class="form-control form-control-alternative" v-model="endDate">
                                        <span class="text-danger" v-if="errors.endDate"> {{ errors.endDate[0] }} </span>
                                    </div>
                                </div>
                                <div class="col-md-2" v-if="userRole == 1 || userRole == 2 || userRole == 10 || userRole == 13">
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
                                        <label for="end_date" class="form-control-label">Status</label> 

                                        <select class="form-control" v-model="expense_verify_status">
                                            <option value=""> Select Status </option>
                                            <option v-for="(item, index) in expenseVerificationStatuses" :key="index" :value="item.id">{{item.name}}</option>
                                            <!-- <option value="0">Pending</option> -->
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <button class="btn btn-sm btn-primary mt-4" @click="fetchExpenses">
                                        Filter
                                        <span v-if="fetchingExpense">...</span>
                                    </button> 
                                </div>

                                <div class="col-md-12">
                                    <span>Attachment: {{ filteredExpensesStats.expensesCount }}</span> |
                                    <span>Verified: {{ filteredExpensesStats.verifiedCount }}</span> |
                                    <span>Unverified: {{ filteredExpensesStats.unverifiedCount }}</span> |
                                    <span class="text-warning">Rejected: {{ filteredExpensesStats.rejectedCount }}</span>
                                    <!-- <span>Pending: {{ filteredExpensesStats.pendingCount }}</span> -->
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">TSR</th>
                                    <th scope="col">Company</th>
                                    <th scope="col">Expense Submitted</th>
                                    <th scope="col">Receipt Status</th>
                                    <!-- <th scope="col">Verified Count</th>
                                    <th scope="col">Unverified Count</th> -->
                                    <th scope="col">Submitted Date</th>
                                    <th scope="col">Total Expenses</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="isEmpty(expenses) && fetchingExpense">
                                        <td colspan="10">Loading Data...</td>
                                    </tr>
                                    <tr v-else v-for="(expense, e) in filteredQueues" v-bind:key="e">
                                        <td class="text-right" v-if="userLevel != 5">
                                            <button v-if="expense.id != null" class="btn btn-sm text-black-50" @click="fetchExpenseByTsr(expense.id, expense.tsr_name, expense.created_at)">View</button>
                                        </td>
                                        <td v-else></td>
                                        <td>{{ expense.tsr_name }}</td>
                                        <td>{{ expense.company }}</td>
                                        <td>
                                            {{ expense.expenses_model_count  }}
                                        </td>
                                        <td>
                                            <!-- <div class="mb-0"><span style="width:90px; display: inline-block;">Pending: </span>{{ (expenseStatus(expense)).pending }}</div> -->
                                            <div class="mb-0"><span style="width:90px; display: inline-block;">Verified: </span>{{ (expenseStatus(expense)).verified }}</div>
                                            <div class="mb-0"><span style="width:90px; display: inline-block;">Unverified: </span>{{ (expenseStatus(expense)).unverified }}</div>
                                            <div class="mb-0"><span style="width:90px; display: inline-block;">Rejected: </span>{{ (expenseStatus(expense)).rejected }}</div>
                                        </td>
                                        <!-- <td>{{ expense.verified_expense_count  }}</td>
                                        <td>{{ expense.expenses_model_count - expense.verified_expense_count  }}</td> -->
                                        <td>
                                            <!-- {{ expense.created_at ? moment(expense.created_at).format('ll') : '' }} -->
                                            {{ expense.created_at }}
                                        </td>
                                        <td>
                                            <span v-if="expense.id != null">
                                                PHP {{ countTotalExpenses(expense) }}
                                            </span>
                                            <span v-else>
                                                0
                                            </span>
                                        </td>
                                    </tr>
                                    <tr v-if="isEmpty(expenses) && !fetchingExpense">
                                        <td>No data available in the table</td>
                                    </tr>
                                </tbody>
                                <!-- <tbody v-else>
                                       
                                </tbody> -->
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
                        <div class="col"><h3>Submitted Date: {{ moment(this.date).format('ll') }} </h3></div>
                    </div>
                    <div class="table-responsive" style="overflow-x:unset">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                            <tr>
                                <th scope="col" v-if="expenseVerifierRole || salesHeadRole || isItRole">Verify</th>
                                <th scope="col">Attachment</th>
                                <th scope="col">Type of Expense</th>
                                <th scope="col">Entry Date</th>
                                <th scope="col">Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(expenseBy, e) in expenseByTsr" v-bind:key="e">
                                    <td v-if="expenseVerifierRole || salesHeadRole || isItRole">
                                        <div v-if="!isEmpty(expenseBy.dms_reference)">
                                            <div v-if="!expenseBy.verified_status_id">
                                                <em>Did Not Verified</em>
                                            </div>
                                            <div v-else>
                                                <div><strong :class="expenseBy.verified_status_id == 1 ? 'text-success': ''">{{ expenseBy.expense_verification_status.name }}</strong></div>
                                                <div style="text-wrap: balance;" v-if="expenseBy.expense_verification_status.id == 3 /**Rejected */">
                                                    {{ expenseBy.expense_rejected_remarks.remark }}
                                                </div>
                                            </div>
                                            <div><small><em>-DMS Received-</em></small></div>
                                        </div>
                                        <div v-else>
                                            <div v-if="!(expenseBy.verified_status_id == 0 || expenseBy.verified_status_id == 2)">
                                                <div><strong :class="expenseBy.verified_status_id == 1 ? 'text-success': ''">{{ expenseBy.expense_verification_status.name }}</strong></div>
                                                <div style="text-wrap: balance;" v-if="expenseBy.expense_verification_status.id == 3 /**Rejected */">
                                                    {{ expenseBy.expense_rejected_remarks.remark }}
                                                    <div v-if="expenseBy.expense_rejected_reason_id == 4">(Deduct PHP{{ expenseBy.rejected_deducted_amount | _amount }}) </div>
                                                </div>
                                                <div class="mt-2" v-if="isItRole">
                                                    <div><small>{{expenseBy.verifier.name}}</small></div>
                                                    <small>{{expenseBy.date_verified | _date}}</small>
                                                </div>

                                                <div v-if="salesHeadRole" class="btn btn-light btn-sm mt-2" @click="verifyExpense(expenseBy,'unset')">Reset Verification</div>
                                            </div>
                                            <div v-else>
                                                <button type="button" class="btn btn-primary btn-sm" @click="verifyExpense(expenseBy,'verify')" :disabled="verifiyingId">
                                                    Verify
                                                    <span v-if="verifiyingId == expenseBy.id">...</span>
                                                </button>

                                                <button type="button" class="btn btn-danger btn-sm" @click="openRejectExpenseModal(expenseBy)" :disabled="verifiyingId">
                                                    Reject
                                                    <span v-if="verifiyingId == expenseBy.id">...</span>
                                                </button>

                                                <!-- <button type="button" class="btn btn-warning btn-sm" @click="verifyExpense(expenseBy,'unverify')" :disabled="verifiyingId">
                                                    Unverify
                                                    <span v-if="verifiyingId == expenseBy.id">...</span>
                                                </button> -->

                                                <!-- <div class="btn-group">
                                                    <button type="button" class="btn btn-danger btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" :disabled="verifiyingId">
                                                        Reject
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="#" v-for="(rejected, rejectedIndex) in rejectedRemarks" :key="rejectedIndex" 
                                                            @click="verifyExpense(expenseBy,'reject', rejected.id)">{{rejected.remark}}</a>
                                                    </div>
                                                </div> -->
                                            </div>
                                        </div>
                                    </td>
                                    <td> 
                                        <img v-if="expenseBy.attachment == 'attachments/default.jpg'" class="rounded-circle" :src="`${imgOrigin}/img/brand/no-image.png`" style="height: 70px; width: 90px;">

                                        <a v-else :href="imageLink+expenseBy.attachment" target="__blank" @click="markAsUnverified(expenseBy)">
                                            <img class="rounded-circle" :src="imageLink+expenseBy.attachment" style="height: 70px; width: 70px" @error="noImage">
                                        </a>
                                    </td>
                                    <td style="white-space:unset; max-width:250px">
                                        <div>{{ expenseBy.expenses_type.name }}</div>
                                        <div v-if="!isEmpty(expenseBy.representaion)">
                                            <div class="mt-2"><strong>Purpose</strong></div> 
                                            {{expenseBy.representaion.purpose}}
                                                
                                            <div class="mt-1"><strong>Attendees</strong></div>
                                            {{expenseBy.representaion.attendees}}
                                        </div>
                                    </td>
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

        <!-- Reject Expense Modal -->
        <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <span class="closed" data-dismiss="modal">&times;</span>
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content border border-danger">
                <div class="modal-header">
                    <h4 class="modal-title text-danger" id="addCompanyLabel">Reject Expense</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" v-if="!isEmpty(selectedExpense)">
                    <div class="d-flex mb-4 p-3 border">
                        <div class="col">
                            <span><strong>Amount:</strong></span> 
                            <br> PHP {{selectedExpense.amount | _amount}}
                        </div>
                        <div class="col">
                            <span><strong>Expense Type:</strong></span> 
                            <br> {{selectedExpense.expenses_type.name}}</div>
                        <div class="col">
                            <span><strong>Entry Date:</strong></span> 
                            <br> {{selectedExpense.created_at | _date }}</div>
                    </div>

                    <div class="form-group">
                        <label class="form-control-label">Select Reject Reason  (<em class="text-danger">* required</em>)</label> 
                        <app-select :options="rejectedRemarks" v-model="rejectedExpense.rejected_reason_id" label="remark"/>
                    </div>

                    <div class="form-group" v-if="rejectedExpense.rejected_reason_id == 4">
                        <label class="form-control-label">Enter Amount To Deduct (<em class="text-danger">* required</em>)</label> 
                        <input type="number" class="form-control" v-model="rejectedExpense.deducted_amount">
                    </div>
                    
                    <!-- Start: Error Message-->
                    <error-messages :form-errors="rejectExpenseError" v-if="!isEmpty(rejectExpenseError)"/>
                    <!-- End: Error Message -->    

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-round btn-fill" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger btn-round btn-fill" @click="verifyExpense(selectedExpense, 'reject')">Submit Reject</button>
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
            company: null,
            expense_verify_status: '',
            tsrName: '',
            date: '',
            errors: [],
            companies: [],
            keywords: '',
            currentPage: 0,
            itemsPerPage: 10,
            verifiyingId: null,
            expenseStatusCount: {
                expensesCount: 0,
                verifiedCount: 0,
                unverifiedCount: 0,
            },
            rejectedRemarks: [],
            expenseVerificationStatuses: [],
            imgOrigin: window.location.origin,
            selectedExpense: {},
            rejectedExpense: {},
            rejectExpenseError: {}
        }
    },
    created(){
        this.fetchCompanies();
        this.fetchExpenseRejectedRemarks();
        this.fetchExpenseVerificationStatuses();
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
        fetchCompanies(){
            axios.get('/companies-all')
            .then(response => {
                this.companies = response.data;
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            })
        },
        fetchExpenseRejectedRemarks(){
            axios.get('/expense-rejected-remarks')
            .then(response => {
                this.rejectedRemarks = response.data;
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            })
        },
        fetchExpenseVerificationStatuses(){
            axios.get('/expense-verification-statuses')
            .then(response => {
                this.expenseVerificationStatuses = response.data;
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            })
        },
        fetchExpenses(){
            this.errors = [];
            this.expenses = [];
            this.fetchingExpense = true;
            axios.post('/expense-report-bydate', {
                startDate: this.startDate,
                endDate: this.endDate,
                company: this.company,
                // expense_verify_status: this.expense_verify_status
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
        verifyExpense(expense, mode, id = null) {
            //return if not allowed to verify
            if(!(this.expenseVerifierRole || this.isItRole || this.salesHeadRole)) { return; }
            
            //reset error message if any
            this.rejectExpenseError = {};

            let vm = this;
            let alertStatus = "mark as " + (mode == 'verify' ? 'verified' : ( mode == 'unverify' ? 'unverified' : 'rejected'))
            if(confirm(`Are you sure you want to ${alertStatus} this attachment?`)) {
                vm.verifiyingId = expense.id;
                axios.post(`/verify-expense-attachment/${expense.id}`,{mode, ...this.rejectedExpense})
                .then(res => {
                    vm.doFetchExpenseByTsr(expense.expenses_entry_id)
                    $('#rejectModal').modal('hide');
                })
                .catch(error => {
                    if(error.response.status === 422) {
                        this.rejectExpenseError = error.response.data.errors;
                    } else {
                        this.errors = error.response.data.errors;
                    }
                })
            }
        },
        markAsUnverified(expense) {
            //Return if already verified
            if(!(this.expenseVerifierRole)) { return; }

            //Return if already verified
            if(expense.verified_status_id > 0) { return }

            axios.post(`/verify-expense-attachment/${expense.id}`,{mode: 'unverify'})
            .then(res => {
                this.doFetchExpenseByTsr(expense.expenses_entry_id)
            })
        },
        openRejectExpenseModal(expense){
            this.selectedExpense = expense;
            this.rejectedExpense = {};
            this.rejectExpenseError = {};
            $('#rejectModal').modal('show');
        },
        isEmpty(data) {
            return _.isEmpty(data);
        },
        expenseStatus(item) {
            let {verified_expense_count, unverified_expense_count, rejected_expense_count, pending_expense_count, expenses_model_count} = item;
            return {
                verified: verified_expense_count,
                unverified: unverified_expense_count + pending_expense_count,
                rejected: rejected_expense_count,
                // pending: pending_expense_count
            }
        }
    },
    computed:{
        filteredExpenses(){
            let self = this;

            const filterExpense =  self.expenses.filter(expense => {
                return expense.tsr_name.toLowerCase().includes(this.keywords.toLowerCase());
                // return expense.user.name.toLowerCase().includes(this.keywords.toLowerCase());
            });

            if (this.expense_verify_status != "") {
                switch (this.expense_verify_status) {
                    // case 0:
                    //     return filterExpense.filter(expense => expense.pending_expense_count > 0);
                    //     break;
                    case 1:
                        return filterExpense.filter(expense => expense.verified_expense_count > 0);
                        break;
                    case 2:
                        return filterExpense.filter(expense => (expense.unverified_expense_count + expense.pending_expense_count) > 0);
                        break;
                    case 3:
                        return filterExpense.filter(expense => expense.rejected_expense_count > 0);
                        break;
                }
            }

            return filterExpense;
        },
        filteredExpensesStats(){
            let self = this;
            let expensesCount = 0;
            let verifiedCount = 0;
            let unverifiedCount = 0;
            let rejectedCount = 0;
            let pendingCount = 0;

            if(!_.isEmpty(self.filteredExpenses)) {
                _.each(self.filteredExpenses, (item) => {
                    verifiedCount = verifiedCount + item.verified_expense_count;
                    unverifiedCount = unverifiedCount + (item.unverified_expense_count + item.pending_expense_count);
                    rejectedCount = rejectedCount + item.rejected_expense_count;
                    // pendingCount = pendingCount + item.pending_expense_count;
                    expensesCount = expensesCount + item.expenses_model_count;
                })
            }

            return {expensesCount, verifiedCount, unverifiedCount, rejectedCount, pendingCount}
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
            ];

            return _.includes(userLevel, this.userLevel) || this.expenseVerifier;
        },
        isItRole() {
            return this.userRole == 1  // IT
        },
        salesHeadRole() {
            let userRole = [
                1,  // IT,
                2,  // President,
                3,  // EVP,
                4,  // VP/Sales Head
            ];
            return _.includes(userRole, this.userRole);
        }
    },
}
</script>

<style>

</style>
