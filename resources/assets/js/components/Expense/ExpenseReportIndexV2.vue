<template>
    <div>
        <div class="header bg-green pb-6 pt-5 pt-md-6"></div>
        <div class="container-fluid mt--7">
            <!-- Table -->
            <div class="card shadow mb-4 mt-5">
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
                    <div class="row mx-2">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="start_date" class="form-control-label">Start Date</label> 
                                <input type="date" id="start_date" class="form-control form-control-alternative" v-model="filterData.start_date" @input="searchKeyUp">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="end_date" class="form-control-label">End Date</label> 
                                <input type="date" id="end_date" class="form-control form-control-alternative" v-model="filterData.end_date" @input="searchKeyUp" :min="filterData.start_date"> 
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Expense Option</label>
                                <div class="radio-inline mt-2">
                                    <label class="radio mr-2">
                                        <input type="radio" value="all" v-model="filterData.expense_option" @input="searchKeyUp"/>
                                        <span></span>
                                        All
                                    </label>
                                    <label class="radio mr-2">
                                        <input type="radio" value="with_expenses" v-model="filterData.expense_option" @input="searchKeyUp"/>
                                        <span></span>
                                        With Expense
                                    </label>
                                    <label class="radio">
                                        <input type="radio" value="without_expenses" v-model="filterData.expense_option" @input="searchKeyUp"/>
                                        <span></span>
                                        Without Expense
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mx-2">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name" class="form-control-label">User</label> 
                                <app-select :options="users" v-model="filterData.user_id" label="name" @input="searchKeyUp"/>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-control-label" for="role">Company</label>
                                <app-select :options="companies" v-model="filterData.company" label="name" @input="searchKeyUp"/>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="end_date" class="form-control-label">Status</label> 
                                <select class="form-control" v-model="filterData.expense_verify_status" @input="searchKeyUp">
                                    <option value=""> All </option>
                                    <option v-for="(item, index) in expenseVerificationStatuses" :key="index" :value="item.id">{{item.name}}</option>
                                    <option value="0">Pending</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-sm btn-primary mt-4" @click="resetSearch">
                                Clear Filter
                            </button> 
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-7">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <span class="stat-loading" v-if="fetchingExpenseStats">
                                Loading...
                            </span>
                            <button v-else class="stat-loading btn bg-light" @click="getVerifiedStats">
                                Refresh <i class="fas fa-sync-alt ml-2"></i>
                            </button>
                            <div class="row text-center">
                                <div class="col">
                                    <div class="font-weight-bold text-sm">
                                        Attachment
                                    </div>
                                    <span>{{ verifiedExpenseStats.expenses_model_count || 0 }} </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-bold text-sm">
                                        Verified ({{verifiedExpenseStats.verified_percentage || 0}}%)
                                    </div>
                                    <span>{{ verifiedExpenseStats.verified_expense_count || 0 }} </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-bold text-sm">
                                        Unverified ({{verifiedExpenseStats.unverified_percentage || 0}}%)
                                    </div>
                                    <span>{{ verifiedExpenseStats.unverified_expense_count || 0 }} </span>
                                </div>
                                <div class="col text-warning">
                                    <div class="font-weight-bold text-sm">
                                        Rejected ({{verifiedExpenseStats.rejected_percentage || 0}}%)
                                    </div>
                                    <span>{{ verifiedExpenseStats.rejected_expense_count || 0 }} </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <span class="stat-loading" v-if="fetchingExpenseStats">
                                Loading...
                            </span>
                            <button v-else class="stat-loading btn bg-light" @click="getVerifiedStats">
                                Refresh <i class="fas fa-sync-alt ml-2"></i>
                            </button>
                            <div class="row">
                                <div class="col"><div class="font-weight-bold text-sm">
                                        Total Expenses
                                    </div>
                                    <span>PHP {{ verifiedExpenseStats.total_expenses | _amount }} </span>
                                </div>
                                <div class="col"><div class="font-weight-bold text-sm">
                                        Approved Claims
                                    </div>
                                    <span>PHP {{ verifiedExpenseStats.verified_amount | _amount }} </span>
                                </div>
                                <div class="col text-warning"><div class="font-weight-bold text-sm">
                                        Rejected
                                    </div>
                                    <span>PHP {{ verifiedExpenseStats.rejected_amount | _amount }} </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-body">
                    <div class="position-relative">
                        <!-- Begin:Block UI -->
                        <app-block-ui v-if="!isEmpty(items) && isProcessing"></app-block-ui>
                        <!-- End:Block UI -->

                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">TSR</th>
                                    <th scope="col">Company</th>
                                    <th scope="col">Expense Entry</th>
                                    <th scope="col">Expense Submitted</th>
                                    <th scope="col">Receipt Status</th>
                                    <th scope="col">Total Expenses</th>
                                    <th scope="col">Verified</th>
                                    <th scope="col">Rejected</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="isEmpty(items) && isProcessing">
                                        <td colspan="10">Loading Data...</td>
                                    </tr>
                                    <tr v-else v-for="(user, e) in items" v-bind:key="e">
                                        <td class="text-right" v-if="userLevel != 5">
                                            <button v-if="user.expenses_model_count" class="btn btn-sm text-black-50" @click="fetchExpenseByTsr(user)">View</button>
                                        </td>
                                        <td v-else></td>
                                        <td>{{ user.name }}</td>
                                        <td>{{ user.company }}</td>
                                        <td>{{ user.expense_entry_count }}</td>
                                        <td>{{ user.expenses_model_count }}</td>
                                        <td>
                                            <div class="mb-0"><span style="width:90px; display: inline-block;">Unverified: </span>{{ user.unverified_expense_count }}</div>
                                            <div class="mb-0"><span style="width:90px; display: inline-block;">Verified: </span>{{ user.verified_expense_count }}</div>
                                            <div class="mb-0"><span style="width:90px; display: inline-block;">Rejected: </span>{{ user.rejected_expense_count }}</div>
                                        </td>
                                        <td>PHP {{ user.total_expenses | _amount }}</td>
                                        <td>PHP {{ user.verified_amount | _amount }}</td>
                                        <td>PHP {{ user.rejected_amount | _amount }}</td>
                                    </tr>
                                    <tr v-if="isEmpty(items) && !isProcessing">
                                        <td>No data available in the table</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer py-4" v-if="items.length">
                    <!--begin::Pagination-->
                    <table-pagination v-if="items.length > 0" :pagination="pagination" v-on:updatePage="goToPage" v-on:doChangeLimit="changePageCount"/>
                    <!--end::Pagination-->
                </div>
            </div>
        </div>

        <div class="custom-modal-container" :class="isRejecModalOpen ? 'display-block' : ''" tabindex="0" role="dialog">
            <div class="modal border border-danger">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCompanyLabel">Reject Expense</h5>
                </div>
                <div class="modal-body" v-if="!isEmpty(selectedExpense)">
                    <div class="d-flex mb-4 p-3 border">
                        <div class="col">
                            <span><strong>Amount:</strong></span> 
                            <br> PHP {{selectedExpense.amount | _amount}}
                        </div>
                        <div class="col">
                            <span><strong>Expense Type:</strong></span> 
                            <br> {{selectedExpense.expenses_type.name}}
                        </div>
                        <div class="col">
                            <span><strong>Entry Date:</strong></span> 
                            <br> {{selectedExpense.created_at | _date }}
                        </div>
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
                    <button type="button" class="btn btn-default btn-round btn-fill" @click="closeRejectExpenseModal">Close</button>
                    <button type="button" class="btn btn-danger btn-round btn-fill" @click="verifyExpense(selectedExpense, 'reject')">
                        Submit Reject
                        <span v-if="verifiyingId">...</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- View Expense Modal -->
        <div class="modal fade" id="viewModal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                        <div class="col"><h3>TSR: {{ selectedUser.name }}</h3></div>
                        <div class="col"><h3>Expense Entry: {{ selectedUser.expenses_model_count }} </h3></div>
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
                                                <div><strong :class="expenseBy.verified_status_id == 1 ? 'text-success': 'text-danger'">{{ expenseBy.expense_verification_status.name }}</strong></div>
                                                <div style="text-wrap: wrap;" v-if="expenseBy.expense_verification_status.id == 3 /**Rejected */">
                                                    {{ expenseBy.expense_rejected_remarks.remark }}
                                                </div>
                                            </div>
                                            <div><small><em>-DMS Received-</em></small></div>
                                        </div>
                                        <div v-else>
                                            <div v-if="!(expenseBy.verified_status_id == 0 || expenseBy.verified_status_id == 2)">
                                                <div><strong :class="expenseBy.verified_status_id == 1 ? 'text-success': 'text-danger'">{{ expenseBy.expense_verification_status.name }}</strong></div>
                                                <div style="text-wrap: wrap;" v-if="expenseBy.expense_verification_status.id == 3 /**Rejected */">
                                                    {{ expenseBy.expense_rejected_remarks.remark }}
                                                    <div v-if="expenseBy.expense_rejected_reason_id == 4">(Deduct PHP{{ expenseBy.rejected_deducted_amount | _amount }}) </div>
                                                </div>
                                                <div class="mt-2" v-if="isItRole">
                                                    <div><small>{{expenseBy.verifier.name}}</small></div>
                                                    <small>{{expenseBy.date_verified | _date}}</small>
                                                </div>

                                                <div v-if="!expenseBy.verification_perion_expired && salesHeadRole" class="btn btn-light btn-sm mt-2" @click="verifyExpense(expenseBy,'unset')">Reset Verification</div>
                                            </div>
                                            <div v-else>
                                                <div v-if="expenseBy.verification_perion_expired">
                                                    <small>Verification Period Expired</small>
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
                                                </div>
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

    </div>
</template>

<script>
import moment from 'moment';
import listFormMixins from '../../list-form-mixins.vue';
export default {
    mixins: [listFormMixins],
    props:['userLevel','userRole','expenseVerifier'],
    data(){
        return{
            expenseByTsr: [],
            verifiyingId: null,
            expenseStatusCount: {
                expensesCount: 0,
                verifiedCount: 0,
                unverifiedCount: 0,
            },
            
            endpoint: '/expenses-report',
            items: [],
            companies: [],
            rejectedRemarks: [],
            expenseVerificationStatuses: [],
            users: [],
            verifiedExpenseStats: [],
            fetchingExpenseStats: false,

            filterData: {
                // start_date: '2023-12-01',
                // end_date: '2023-12-31',
                expense_option: 'all',
                start_date: moment().startOf('month').format('YYYY-MM-DD'),
                end_date: moment().endOf('month').format('YYYY-MM-DD')
            },
            imgOrigin: window.location.origin,
            selectedUser: {},
            selectedExpense: {},
            rejectedExpense: {},
            rejectExpenseError: {},
            isRejecModalOpen: false
        }
    },
    created(){
        this.getSelectOptions('users', '/selection-users')
        this.getSelectOptions('companies', '/companies-all')
        this.getSelectOptions('rejectedRemarks', '/expense-rejected-remarks')
        this.getSelectOptions('expenseVerificationStatuses', '/expense-verification-statuses')
        this.fetchInitialData();
    },
    methods:{
        moment,
        fetchInitialData() {
            this.fetchList();
            this.getVerifiedStats()
        },
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
        fetchExpenseByTsr(user){
            this.selectedUser = user;
            let date_params = {
                start_date: this.filterData.start_date,
                end_date: this.filterData.end_date
            }
            axios.get(`${this.endpoint}/expenses/${user.id}`, {params: date_params})
            .then(response => { 
                this.expenseByTsr = response.data;
                // this.date = created;
                $('#viewModal').modal('show');
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            })
        },
        doFetchExpenseByTsr(user_id) {
            let date_params = {
                start_date: this.filterData.start_date,
                end_date: this.filterData.end_date
            }
            axios.get(`${this.endpoint}/expenses/${user_id}`, {params: date_params})
            .then(response => { 
                this.verifiyingId = null;
                this.expenseByTsr = response.data;
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            })
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
                    vm.doFetchExpenseByTsr(expense.user_id)
                    vm.fetchList();
                    vm.closeRejectExpenseModal();
                    // $('#viewModal').modal('show');
                    // $('#rejectModal').modal('hide');
                })
                .catch(error => {
                    if(error.response.status === 422) {
                        this.rejectExpenseError = error.response.data.errors;
                    } else {
                        this.errors = error.response.data.errors;
                    }
                    vm.verifiyingId = null;
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
                this.doFetchExpenseByTsr(expense.user_id)
            })
        },
        openRejectExpenseModal(expense){
            this.selectedExpense = expense;
            this.rejectedExpense = {};
            this.rejectExpenseError = {};
            this.isRejecModalOpen = true
            // $('#rejectModal').modal('show');
        },
        closeRejectExpenseModal() {
            this.selectedExpense = {};
            this.rejectedExpense = {};
            this.rejectExpenseError = {};
            this.isRejecModalOpen = false
        },
        expenseStatus(item) {
            let {verified_expense_count, unverified_expense_count, rejected_expense_count, pending_expense_count, expenses_model_count} = item;
            return {
                verified: verified_expense_count,
                unverified: unverified_expense_count,
                rejected: rejected_expense_count,
                pending: pending_expense_count
            }
        },
        getVerifiedStats() {
            this.fetchingExpenseStats = true;
            axios.get(`${this.endpoint}/verified-stat`, {params: this.filterData})
            .then(response => { 
                this.verifiedExpenseStats = response.data;
                this.fetchingExpenseStats = false;
            })
            .catch(error => {
                this.errors = error.response.data.errors;
                this.fetchingExpenseStats = false;
            })
        },
        searchKeyUp() {
            clearTimeout(this.keyTimeout);
            this.keyTimeout = setTimeout(() => {
                this.isProcessing = true;
                this.fetchList();
                this.getVerifiedStats();
            }, 500)
        },
        resetSearch() {
            this.filterData = {
                // start_date: '2023-12-01',
                // end_date: '2023-12-31',
                expense_option: 'all',
                start_date: moment().startOf('month').format('YYYY-MM-DD'),
                end_date: moment().endOf('month').format('YYYY-MM-DD')
            };
            this.fetchList();
            this.getVerifiedStats();
        }
    },
    computed:{
        imageLink(){
            return 'http://salesforce.lafilgroup.net:8666/storage/';
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
