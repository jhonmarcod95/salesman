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
                                    <h3 class="mb-0">DMS Submitted Expenses Report</h3>
                                </div>
                                <div class="d-flex">
                                    <div><a class="btn btn-sm btn-outline-default mr-2" href="/expenses-report"> Expenses Report</a></div>
                                    <div><a class="btn btn-sm btn-default mr-2" href="/dms-received-expense"> DMS Submitted Expense</a></div>
                                    <div><a class="btn btn-sm btn-outline-default mr-2" href="/expenses-top-spender-report"> Expense Top Spender</a></div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="row ml-2">
                                <div class="col-md-5 float-left">
                                    <div class="form-group">
                                        <label for="name" class="form-control-label">Search</label> 
                                        <app-select :options="users" v-model="filterData.user_id" label="name" @input="searchKeyUp"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="start_date" class="form-control-label">Month Year</label> 
                                        <input type="month" class="form-control form-control-alternative" v-model="filterData.month_year" @input="searchKeyUp">
                                    </div>
                                </div>
                                <!-- <div class="col-md-3" v-if="userRole == 1 || userRole == 2 || userRole == 10 || userRole == 13"> -->
                                    <!-- <div class="form-group">
                                        <label class="form-control-label" for="role">Company</label>
                                        <select class="form-control" v-model="filterData.company" @input="searchKeyUp">
                                            <option v-for="(company,c) in companies" v-bind:key="c" :value="company.id"> {{ company.name }}</option>
                                        </select>
                                    </div> -->
                                <!-- </div> -->
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="end_date" class="form-control-label">Status</label> 

                                        <select
                                            class="form-control"
                                            v-model="filterData.status"
                                            @input="searchKeyUp">
                                        <option value=""> Select Status </option>
                                        <option value="verified">Verified</option>
                                        <option value="unverified">Unverified</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <button class="btn btn-sm btn-primary mt-4" @click="resetSearch">
                                        Clear Filter
                                    </button> 
                                </div>

                                <!-- <div class="col-md-12">
                                    <span>Attachment: {{ expensesCount || 0 }}</span> |
                                    <span>Verified: {{ verifiedCount || 0 }}</span> |
                                    <span class="text-warning">Unverified: {{ unverifiedCount || 0 }}</span>
                                </div> -->
                            </div>
                        </div>
                        <div class="position-relative">
                            <!-- Begin:Block UI -->
                            <app-block-ui v-if="!isEmpty(items) && isProcessing"></app-block-ui>
                            <!-- End:Block UI -->

                            <div class="table-responsive">
                                <table class="table align-items-center table-flush">
                                    <thead class="thead-light">
                                    <tr>
                                        <th scope="col">TSR</th>
                                        <th scope="col">Company</th>
                                        <th scope="col">Month</th>
                                        <th scope="col">Year</th>
                                        <th scope="col">Date Submitted</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="isEmpty(items) && isProcessing">
                                            <td colspan="10">Loading Data...</td>
                                        </tr>
                                        <tr v-else v-for="(expense, e) in items" v-bind:key="e">
                                            <!-- <td class="text-right" v-if="userLevel != 5">
                                                <button v-if="expense.id != null" class="btn btn-sm text-black-50" @click="fetchExpenseByTsr(expense.id, expense.tsr_name, expense.created_at)">View</button>
                                            </td> -->
                                            <!-- <td v-else></td> -->
                                            <td>{{ expense.user.name }}</td>
                                            <td>{{ expense.user.companies ? expense.user.companies[0].name : '' }}</td>
                                            <td>{{ expense.month }}</td>
                                            <td>{{ expense.year }}</td>
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
                       <div class="card-footer py-4" v-if="items.length">
                            <!--begin::Pagination-->
					        <table-pagination v-if="items.length > 0" :pagination="pagination" v-on:updatePage="goToPage" v-on:doChangeLimit="changePageCount"/>
					        <!--end::Pagination-->
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
                <!-- <div class="modal-body text-center">
                  <div class="row">
                        <div class="col"><h3>TSR: {{ tsrName }}</h3></div>
                        <div class="col"><h3>Date: {{ moment(date).format('ll') }} </h3></div>
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
                </div> -->
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
export default {
    mixins: [listFormMixins],
    props:['userLevel','userRole','expenseVerifier'],
    data(){
        return{
            endpoint: '/dms-received-expense',
            items: [],
            fetchingExpense: false,
            expenseByTsr: {},
            users: [],
            tsrName: ''
        }
    },
    created(){
        this.getSelectOptions('companies', '/companies-all')	
        this.getSelectOptions('users', '/selection-users')
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
            this.filterData = {};
            this.fetchList();
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
