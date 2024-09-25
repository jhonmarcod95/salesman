<template>
    <div>
        <div class="header bg-green pb-6 pt-5 pt-md-6"></div>
        <div class="container-fluid mt--7">
            <!-- Table -->
            <div class="row mt-5">
                <div class="col">
                    <div class="card mb-4 shadow">

                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="mb-0">DMS Submitted Expenses Report</h3>
                                </div>
                                <expense-report-nav :user-role="userRole"/>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="row ml-2">
                                <div class="col-md-2 float-left">
                                    <div class="form-group">
                                        <label for="name" class="form-control-label">Company</label> 
                                        <app-select :options="companies" v-model="filterData.company_id" label="name" @input="searchKeyUp"/>
                                    </div>
                                </div>
                                <div class="col-md-3 float-left">
                                    <div class="form-group">
                                        <label for="name" class="form-control-label">User</label> 
                                        <app-select :options="users" v-model="filterData.user_id" label="name" @input="searchKeyUp"/>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="start_date" class="form-control-label">Month Year</label> 
                                        <input type="month" class="form-control form-control-alternative" v-model="filterData.month_year" @input="searchKeyUp">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="end_date" class="form-control-label">Status</label> 
                                        <select class="form-control" v-model="filterData.expense_status" @input="searchKeyUp">
                                            <option value=""> All </option>
                                            <option value="1"> Completed </option>
                                            <option value="2"> Partially Completed </option>
                                            <option value="3"> Pending </option>
                                            <option value="4"> Rejected </option>
                                            <!-- <option v-for="(item, index) in expenseVerificationStatuses" :key="index" :value="item.id">{{item.name}}</option> -->
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-sm btn-primary mt-4" @click="resetSearch">Clear Filter</button> 
                                    <button v-if="isItRole" class="btn btn-sm btn-success mt-4" @click="exportReport()"> Export Excel</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Begin: DMS Submitted -->
                    <div class="card shadow mb-4">
                        <div class="card-header">
                            <h3 class="mb-0">
                                DMS Submitted Expenses
                                <span v-if="!isEmpty(items) && !isProcessing">({{ pagination.total }})</span>
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="position-relative">
                                <!-- Begin:Block UI -->
                                <app-block-ui v-if="!isEmpty(items) && isProcessing"></app-block-ui>
                                <!-- End:Block UI -->

                                <div class="table-responsive">
                                    <table class="table align-items-center table-flush">
                                        <thead class="thead-light">
                                        <tr>
                                            <th scope="col">TSR</th>
                                            <!-- <th scope="col">Company</th> -->
                                            <th scope="col">Month</th>
                                            <th scope="col">Year</th>
                                            <th scope="col">Expense Status</th>
                                            <th scope="col">Expense Count</th>
                                            <th scope="col">Date Submitted</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-if="isEmpty(items) && isProcessing">
                                                <td colspan="10">Loading Data...</td>
                                            </tr>
                                            <tr v-else v-for="(expense, e) in items" v-bind:key="e">
                                                <td>
                                                    <strong>{{ expense.user.name }}</strong> <br>
                                                    <span>{{ expense.user.companies ? expense.user.companies[0].name : '' }}</span>
                                                </td>
                                                <!-- <td>{{ expense.user.companies ? expense.user.companies[0].name : '' }}</td> -->
                                                <td>{{ expense.month }}</td>
                                                <td>{{ expense.year }}</td>
                                                <td>
                                                    <div class="mb-0"><span style="width:90px; display: inline-block;">Verified: </span>{{ expense.expense_status.verified }}</div>
                                                    <div class="mb-0"><span style="width:90px; display: inline-block;">Unverified: </span>{{ expense.expense_status.unverified }}</div>
                                                    <div class="mb-0"><span style="width:90px; display: inline-block;">Rejected: </span>{{ expense.expense_status.rejected }}</div>
                                                    <!-- <div class="mb-0"><span style="width:90px; display: inline-block;">Not Verified: </span>{{ expense.expense_status.not_verified }}</div> -->
                                                </td>
                                                <td>{{ expense.expense_status.expense_count }}</td>
                                                <td>
                                                    {{ expense.created_at | _date}}
                                                </td>
                                            </tr>
                                            <tr v-if="isEmpty(items) && !isProcessing">
                                                <td>No data available in the table</td>
                                            </tr>
                                        </tbody>
                                        <!-- <tbody v-else>
                                            
                                        </tbody> -->
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <!--begin::Pagination-->
                            <table-pagination v-if="!isEmpty(items)" :pagination="pagination" v-on:updatePage="goToPage" v-on:doChangeLimit="changePageCount"/>
                            <!--end::Pagination-->
                        </div>
                    </div>
                    <!-- End: DMS Submitted -->

                    <!-- Begin: Pending For Submission -->
                    <ExpenseDmsPendingToReceived :filterParams="filterData" class="mb-4"/>
                    <!-- End: Pending For Submission -->

                    <!-- Begin: Pending For Submission -->
                    <ExpenseDmsNoExpenses :filterParams="filterData"/>
                    <!-- End: Pending For Submission -->
                    
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-round btn-fill" data-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<script>
import moment from 'moment';
import listFormMixins from '../../list-form-mixins.vue';
import ExpenseDmsPendingToReceived from './ExpenseDmsPendingToReceived.vue';
import ExpenseDmsNoExpenses from './ExpenseDmsNoExpenses.vue';
export default {
    mixins: [listFormMixins],
    props:['userLevel','userRole','expenseVerifier'],
    components: {ExpenseDmsPendingToReceived,ExpenseDmsNoExpenses},
    data(){
        return{
            endpoint: '/dms-received-expense',
            items: [],
            filterData: {
                month_year: moment().subtract(1, 'months').format('YYYY-MM')
            },
            fetchingExpense: false,
            expenseByTsr: {},
            users: [],
            companies: [],
            tsrName: '',
            expenseVerificationStatuses: []
        }
    },
    created(){
        this.getSelectOptions('companies', '/companies-all')	
        this.getSelectOptions('users', '/selection-users')
        this.getSelectOptions('expenseVerificationStatuses', '/expense-verification-statuses')
        this.fetchList();
    },
    methods:{
        moment,
        noImage(event){
            event.target.src = window.location.origin+'/img/brand/no-image.png';
        },
        fetchExpenses(){
            this.errors = [];
            this.expenses = [];
            this.fetchingExpense = true;
            axios.post('/expense-report-bydate', {
                startDate: this.startDate,
                endDate: this.endDate,
                company: this.company,
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
        resetSearch() {
            this.filterData = {
                month_year: moment().subtract(1, 'months').format('YYYY-MM')
            }
            this.fetchList();
        },
        exportReport() {
            //=============
            // Configuration object
            let url = `${this.endpoint}/export`;
            let params = this.filterData;
            let queryString = new URLSearchParams(params).toString();

            // Manually constructing the URI
            const requestUri = `${url}?${queryString}`;
            //=============

            //link to download
            let link = document.createElement("a");

            //donload/export excel
            link.href = requestUri;
            link.click();
        }
    },
    computed:{
        filteredQueues() {},
        imageLink(){
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
        presidentRole() {
            let userRole = [
                2,  // President,
                // 3,  // EVP,
            ];
            return _.includes(userRole, this.userRole);
        },
        salesHeadRole() {
            return this.userRole == 4  // VP/Sales Head
        },
        filterVerified() {
            return this.filterData.expense_status == null || this.filterData.expense_status == '' || this.filterData.expense_status == '1';
        },
        filterUnverified() {
            return this.filterData.expense_status == null || this.filterData.expense_status == '' || this.filterData.expense_status == '2';
        },
        filterRejected() {
            return this.filterData.expense_status == null || this.filterData.expense_status == '' || this.filterData.expense_status == '3';
        }
    },
}
</script>

<style>

</style>