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
                                    <h3 class="mb-0">Expense Deduction Report</h3>
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
                                Expense Deductions
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
                                            <th scope="col"></th>
                                            <th scope="col">TSR</th>
                                            <th scope="col">Month</th>
                                            <th scope="col">Year</th>
                                            <th scope="col">Amount for Deduction</th>
                                            <th scope="col">Expense Deducted Count</th>
                                            <th scope="col">Balance Amount for Deduction</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-if="isEmpty(items) && isProcessing">
                                                <td colspan="10">Loading Data...</td>
                                            </tr>
                                            <tr v-else v-for="(expense, e) in items" v-bind:key="e">
                                                <td class="text-right">
                                                    <button class="btn btn-sm text-black-50" @click="viewDeductedExpense(expense.month+ ' '+expense.year,
                                                        expense.unverified_amount + expense.rejected_amount,expense.user.name,expense.deductions)">View</button>
                                                </td>
                                                <td>
                                                    <strong>{{ expense.user.name }}</strong> <br>
                                                    <span>{{ expense.user.companies ? expense.user.companies[0].name : '' }}</span>
                                                </td>
                                                <td>{{ expense.month }}</td>
                                                <td>{{ expense.year }}</td>
                                                <td> PHP {{ (expense.unverified_amount + expense.rejected_amount).toFixed(2) }}</td>
                                                <td>{{ expense.deductions.length }}</td>
                                                <td>PHP {{ (expense.balance_rejected_amount).toFixed(2) }}</td>
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
                </div>
            </div>
        </div>

        <!-- View Expense Modal -->
        <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <span class="closed" data-dismiss="modal">&times;</span>
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCompanyLabel">Expense Deduction</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                  <div class="row">
                        <div class="col-md-4"> <h3>TSR: {{ tsr_name }}</h3> </div>
                        <div class="col-md-4"><h3>Month / Year: {{ deduction_month_year }} </h3></div>
                        <div class="col-md-4"><h3>Amount for Deduction: PHP {{ (deduction_amount).toFixed(2) }} </h3></div>
                    </div>
                    <div class="table-responsive" style="overflow-x:unset">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                            <tr>
                                <th scope="col">Expense ID</th>
                                <th scope="col">Attachment</th>
                                <th scope="col">Type of Expense</th>
                                <th scope="col">Entry Date</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Deducted Amount</th>
                                <th scope="col">Deduction Date</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(deduction, d) in deductions" v-bind:key="d">
                                    <td>{{ deduction.expense.id }}</td>
                                    <td>  <img class="rounded-circle" :src="imageLink+deduction.expense.attachment" style="height: 70px; width:70px" @error="noImage"> </td>
                                    <td>{{ deduction.expense.expenses_type.name }}</td>
                                    <td>{{ deduction.expense.created_at }} </td>
                                    <td>PHP {{ deduction.expense.amount.toFixed(2) }} </td>
                                    <td>PHP {{ (deduction.expense.amount - deduction.expense.posted_amount).toFixed(2) }} </td>
                                    <td>{{ deduction.created_at }}</td>
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
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<script>
import moment from 'moment';
import listFormMixins from '../../list-form-mixins.vue';
export default {
    mixins: [listFormMixins],
    props:['userLevel','userRole','expenseVerifier','accessDmsReceived'],
    data(){
        return{
            endpoint: '/expenses-deduction-report',
            items: [],
            filterData: {
                month_year: moment().subtract(1, 'months').format('YYYY-MM')
            },
            users: [],
            companies: [],
            tsr_name: '',
            deduction_month_year: '',
            deduction_amount: 0,
            deductions: []
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
        resetSearch() {
            this.filterData = {
                month_year: moment().subtract(1, 'months').format('YYYY-MM')
            }
            this.fetchList();
        },
        viewDeductedExpense(deduction_month_year,deduction_amount,tsr_name, deductions){
            this.deduction_month_year = deduction_month_year;
            this.deduction_amount = deduction_amount;
            this.tsr_name = tsr_name;
            this.deductions = deductions;
            $('#viewModal').modal('show');
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
        imageLink(){
            return window.location.origin+'/storage/';
        },
        isItRole() {
            return this.userRole == 1  // IT
        }
    },
}
</script>