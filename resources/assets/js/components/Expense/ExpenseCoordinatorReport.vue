<template>
    <div>
        <div class="header bg-green pb-6 pt-5 pt-md-6"></div>
        <div class="container-fluid mt--7">
            <!-- Table -->
            <div class="card shadow mb-4 mt-5">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="mb-0">Coordinator Report</h3>
                        </div>
                        <expense-report-nav :user-role="userRole"/>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="row mx-2">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="start_date" class="form-control-label">Month Year</label> 
                                <input type="month" class="form-control form-control-alternative" v-model="filterData.month_year" @input="getWeekNumber">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name" class="form-control-label">Week</label> 
                                <app-select :options="weekRanges" v-model="filterData.week_id" label="name" @input="getSelectedWeekRange"/>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="form-control-label" for="role">Company</label>
                                <app-select :options="companies" v-model="filterData.company" label="name" @input="selectCompanyCoordinator"/>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name" class="form-control-label">Coordinator</label> 
                                <app-select :options="coordinators" v-model="filterData.coordinator_id" label="name" @input="searchKeyUp"/>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-sm btn-primary mt-4" @click="resetSearch">
                                Clear Filter
                            </button>
                            <button class="btn btn-sm btn-primary mt-4" @click="exportReport">
                                Export
                            </button>
                        </div>
                    </div>
                </div>
            </div>    
        
            <div class="row">
                <div class="col-md-7">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <span class="stat-loading bg-light" v-if="fetchingExpenseStats">
                                Loading...
                            </span>
                            <button v-else class="stat-loading btn bg-light" @click="getVerifiedStats">
                                Refresh <i class="fas fa-sync-alt ml-2"></i>
                            </button>
                            <div class="row text-center">
                                <div class="col">
                                    <div class="font-weight-bold text-sm">
                                       Validated Receipt
                                    </div>
                                    <span>{{ verifiedExpenseStats.validated_expense_count || 0 }} </span>
                                </div>
                                <div class="col">
                                    <div class="font-weight-bold text-sm">
                                        Verified ({{verifiedExpenseStats.verified_percentage || 0}}%)
                                    </div>
                                    <span>{{ verifiedExpenseStats.verified_expense_count || 0 }} </span>
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
                            <span class="stat-loading bg-light" v-if="fetchingExpenseStats">
                                Loading...
                            </span>
                            <button v-else class="stat-loading btn bg-light" @click="getVerifiedStats">
                                Refresh <i class="fas fa-sync-alt ml-2"></i>
                            </button>
                            <div class="row">
                                <div class="col"><div class="font-weight-bold text-sm">
                                        Validated Expenses
                                    </div>
                                    <span>PHP {{ verifiedExpenseStats.total_validated_amount || 0 | _amount }} </span>
                                </div>
                                <div class="col"><div class="font-weight-bold text-sm">
                                        Approved Claims
                                    </div>
                                    <span>PHP {{ verifiedExpenseStats.verified_amount || 0 | _amount }} </span>
                                </div>
                                <div class="col text-warning"><div class="font-weight-bold text-sm">
                                        Rejected
                                    </div>
                                    <span>PHP {{ verifiedExpenseStats.rejected_amount || 0 | _amount }} </span>
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
                                    <th scope="col">Total Validated</th>
                                    <th scope="col">Receipt Status</th>
                                    <th scope="col">Amount Validated</th>
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
                                            <button v-if="user.validated_expense_count" class="btn btn-sm text-black-50" @click="fetchExpenseByTsr(user)">View</button>
                                        </td>
                                        <td>
                                            <strong>{{ user.name }}</strong> <br>
                                            <span>{{ user.company }}</span>
                                        </td>
                                        <td>{{ user.validated_expense_count }}</td>
                                        <td>
                                            <div class="mb-0"><span style="width:90px; display: inline-block;">Verified: </span>{{ user.verified_expense_count }}</div>
                                            <div class="mb-0"><span style="width:90px; display: inline-block;">Rejected: </span>{{ user.rejected_expense_count }}</div>
                                        </td>
                                        <td>PHP {{ user.total_validated_amount | _amount }}</td>
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

        <div class="custom-modal-container" :class="isHistoryModalOpen ? 'display-block' : ''" tabindex="0" role="dialog">
            <div class="modal border" style="margin-top: 100px;">
                <div class="modal-header px-5 pt-5 align-items-center jsutify-content-between">
                    <h4 class="modal-title" id="addCompanyLabel">Expense History</h4>
                    <button type="button" class="close" @click="closeHistoryModal"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body px-5">
                    <div class="d-flex" v-for="(history, index) in expenseHistory" :key="index">
                        <div><small>{{history.date}}</small></div>
                        <div class="border-left mx-4 text-lg">
                            <span class="m--1">•</span>
                        </div>
                        <div class="pb-4">
                            <h6 class="mb-0 text-primary font-weight-bold text-sm">{{history.action}}</h6>
                            <small class="font-weight-bold">{{history.verifier}}</small>
                            <div class="mt-1">
                                <div style="line-height: 1;" v-for="(detail, key, detailIndex) in history.details" :key="detailIndex">
                                    <small>{{key}}: {{detail}}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-round btn-fill" @click="closeHistoryModal">Close</button>
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
                                <th scope="col" v-if="expenseVerifierRole || salesHeadRole || isItRole || presidentRole">Verify</th>
                                <th scope="col">Attachment</th>
                                <th scope="col">Type of Expense</th>
                                <th scope="col">Entry Date</th>
                                <th scope="col">Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(expenseBy, e) in expenseByTsr" v-bind:key="e">
                                    <td v-if="expenseVerifierRole || salesHeadRole || isItRole || presidentRole">
                                        <div v-if="!isUnverified(expenseBy.verified_status_id)">
                                            <div><strong :class="expenseBy.verified_status_id == 1 ? 'text-success': 'text-danger'">{{ expenseBy.expense_verification_status.name }}</strong></div>
                                            <div style="text-wrap: wrap;" v-if="expenseBy.expense_verification_status.id == 3 /**Rejected */">
                                                {{ expenseBy.expense_rejected_remarks.remark }}
                                                <div v-if="expenseBy.expense_rejected_reason_id == 4">(Deduct PHP{{ expenseBy.rejected_deducted_amount | _amount }}) </div>
                                            </div>
                                        </div>
                                        <div class="mt-2" style="line-height: 1.3;" v-if="isItRole && !isEmpty(expenseBy.verifier) && !isUnverified(expenseBy.verified_status_id)">
                                            <div><small>{{expenseBy.verifier.name}}</small></div>
                                            <small>{{expenseBy.date_verified | _date}}</small>
                                        </div>

                                        <div v-if="!isEmpty(expenseBy.dms_reference)">
                                            <div v-if="isUnverified(expenseBy.verified_status_id)">
                                                <em>Did Not Verified</em>
                                            </div>
                                            <div><small><em>-DMS Received-</em></small></div>
                                        </div>

                                        <div class="mt-2">
                                            <a href="javascript:;" v-if="expenseBy.history_count" @click="fetchHistory(expenseBy.id)">History</a>
                                        </div> 
                                    </td>
                                    <td> 
                                        <img v-if="expenseBy.attachment == 'attachments/default.jpg'" class="rounded-circle" :src="`${imgOrigin}/img/brand/no-image.png`" style="height: 70px; width: 90px;">

                                        <a v-else :href="imageLink+expenseBy.attachment" target="__blank" @click="markAsUnverified(expenseBy)">
                                            <img class="rounded-circle" :src="imageLink+expenseBy.attachment" style="height: 70px; width:70px" @error="noImage">
                                        </a>
                                    </td>
                                    <td style="white-space:unset; max-width:250px">
                                        <div>
                                            {{ expenseBy.expenses_type.name }}
                                            <span v-if="!isEmpty(expenseBy.grassroots)">
                                                <span v-if="!isEmpty(expenseBy.grassroots.grassroot_expense_type)">
                                                    ({{expenseBy.grassroots.grassroot_expense_type.name}})
                                                </span>
                                            </span>
                                        </div>
                                        
                                        <div v-if="!isEmpty(expenseBy.representaion)">
                                            <div class="mt-2"><strong>Purpose</strong></div> 
                                            {{expenseBy.representaion.purpose}}
                                                
                                            <div class="mt-1"><strong>Attendees</strong></div>
                                            {{expenseBy.representaion.attendees}}
                                        </div>

                                        <div v-if="!isEmpty(expenseBy.grassroots)">
                                            <div class="mt-2"><strong>Remarks</strong></div> 
                                            {{expenseBy.grassroots.remarks}}
                                        </div>

                                        <div v-if="!isEmpty(expenseBy.route_transportation)">
                                            <div class="mt-2"><strong>{{expenseBy.route_transportation.transportation.mode}}</strong></div> 
                                            <div>From: {{expenseBy.route_transportation.from}}</div>
                                            <div>To: {{expenseBy.route_transportation.to}}</div>

                                            <div class="mt-2" v-if="!isEmpty(expenseBy.route_transportation.remarks)">
                                                <strong>Remarks</strong>
                                                <div>{{expenseBy.route_transportation.remarks}}</div>
                                            </div> 
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
    props:['userLevel','userRole','expenseVerifier','accessDmsReceived'],
    data(){
        return{
            expenseByTsr: [],
            verifiyingId: null,
            expenseStatusCount: {
                expensesCount: 0,
                verifiedCount: 0,
                unverifiedCount: 0,
            },
            
            endpoint: '/coordinator-report',
            items: [],
            companies: [],
            users: [],
            coordinators: [],
            verifiedExpenseStats: [],
            fetchingExpenseStats: false,

            filterData: {
                week_id: 1,
                month_year: moment().format('YYYY-MM'),
                start_date: moment().startOf('month').format('YYYY-MM-DD'),
                end_date: moment().endOf('month').format('YYYY-MM-DD'),
            },
            weekRanges: [],
            imgOrigin: window.location.origin,
            selectedUser: {},
            selectedExpense: {},
            isHistoryModalOpen: false,
            expenseHistory: []
        }
    },
    created(){
        this.getSelectOptions('companies', '/companies-all')
        this.defaultFilterData();
        // this.getSelectOptions('users', '/selection-users')
        this.getSelectOptions('coordinators', '/selection-coordinators/all')
        this.getWeekNumber();
    },
    methods:{
        moment,
        fetchInitialData() {
            this.fetchList();
            this.getVerifiedStats()
        },
        defaultFilterData() {
            this.filterData = {
                week_id: 1,
                month_year: moment().format('YYYY-MM'),
                start_date: moment().startOf('month').format('YYYY-MM-DD'),
                end_date: moment().endOf('month').format('YYYY-MM-DD'),
            }
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
            axios.get(`${this.endpoint}/validated-expenses/${user.id}`, {params: date_params})
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
            //Return if role is IT
            if((this.isItRole)) { return; }

            //Return if already verified
            if(!(this.expenseVerifierRole)) { return; }

            //Return if already verified
            if(expense.verified_status_id > 0) { return }

            axios.post(`/verify-expense-attachment/${expense.id}`,{mode: 'unverify'})
            .then(res => {
                this.doFetchExpenseByTsr(expense.user_id)
            })
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
            this.verifiedExpenseStats = [];
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
            this.defaultFilterData()
            this.fetchList();
            this.getVerifiedStats();
        },
        getWeekNumber() {
            let date = _.split(this.filterData.month_year, '-', 2);

            let startOfMonth = moment(`${date[0]}-${date[1]}-01`, 'YYYY-MM');
            let endOfMonth = startOfMonth.clone().endOf('month');
            
            // Adjust to the first Sunday after the 1st of the month (or the 1st itself if it's a Sunday)
            let currentWeekStart = startOfMonth.clone().startOf('week'); // This will start from Sunday

            let weekRanges = [];

            //Set default 1st week ; 1st to last date of monht
            weekRanges[0] = {
                id: 1,
                name: 'All Week',
                start_date: moment(`${date[0]}-${date[1]}`).startOf('month').format('YYYY-MM-DD'),
                end_date: moment(`${date[0]}-${date[1]}`).endOf('month').format('YYYY-MM-DD')
            }

            //Set start of week loop
            let week_count = 1;

            // Loop through each week in the month
            while (currentWeekStart.isBefore(endOfMonth)) {
                let weekStart = currentWeekStart.clone();
                let weekEnd = currentWeekStart.clone().endOf('week');

                // Ensure we don't include days before the start of the month
                if (weekStart.isBefore(startOfMonth)) {
                    weekStart = startOfMonth.clone();
                }

                // Ensure we don't include days after the end of the month
                if (weekEnd.isAfter(endOfMonth)) {
                    weekEnd = endOfMonth.clone();
                }

                let start_date = weekStart.format('MM/DD/YYYY');
                let end_date = weekEnd.format('MM/DD/YYYY');

                // Add the valid week range to the result
                weekRanges.push({
                    id: weekRanges.length + 1,
                    name: `Week ${week_count} ${start_date} - ${end_date}`,
                    start_date: weekStart.format('YYYY-MM-DD'),
                    end_date: weekEnd.format('YYYY-MM-DD')
                });

                // Move to the next Sunday
                currentWeekStart.add(1, 'week');
                week_count++;
            }

            //Define value of weekRanges
            this.weekRanges = weekRanges;

            //Set Default filter range
            this.getSelectedWeekRange(1)
        },
        getSelectedWeekRange(week_id) {
            let selected_week = _.find(this.weekRanges, (week) => week.id == week_id);
            this.filterData.week_id = week_id;
            this.filterData.start_date = selected_week.start_date;
            this.filterData.end_date = selected_week.end_date;

            this.searchKeyUp();
        },
        isUnverified(status_id) {
            return status_id == 0 || status_id == 2
        },
        fetchHistory(expense_id) {
            axios.get(`/expenses-report/receipt-history/${expense_id}`)
            .then( res => {
                this.isHistoryModalOpen = true;
                this.expenseHistory = res.data;
            })
        },
        closeHistoryModal() {
            this.isHistoryModalOpen = !this.isHistoryModalOpen;
        },
        selectCompanyCoordinator(coor_id) {
            //Reset coordinator value
            this.filterData = _.omit(this.filterData, ['coordinator_id']);

            //Fetch coordinator base on company
            this.getSelectOptions('coordinators', `/selection-coordinators/${coor_id}`)

            //Trigger search
            this.searchKeyUp()
        },
        exportReport(type) {
            //=============
            // Configuration object
            let url = `${this.endpoint}/export`;
            let params = {type, ...this.filterData};
            let queryString = new URLSearchParams(params).toString();

            // Manually constructing the URI
            const requestUri = `${url}?${queryString}`;
            //=============

            //link to download
            let link = document.createElement("a");

            //donload/export excel
            link.href = requestUri;
            link.click();
        },
    },
    computed:{
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
        presidentRole() {
            let userRole = [
                2,  // President,
                // 3,  // EVP,
            ];
            return _.includes(userRole, this.userRole);
        },
        salesHeadRole() {
            return this.userRole == 4  // VP/Sales Head
        }
    },
}
</script>

<style>

</style>
