<template>
    <div>
        <div class="header bg-green pb-6 pt-5 pt-md-6"></div>
        <!-- Page content -->
        <div class="container-fluid mt--7">
            <!-- Table -->
            <div class="row mt-5">
                <div class="col">
                    <div class="card shadow">

                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="mb-0">No Voucher Payments</h3>
                                </div>
                                <div class="col text-right">

                                    <download-excel
                                        :data   = "payments"
                                        :fields = "json_fields"
                                        class   = "btn btn-sm btn-default"
                                        name    = "NoVoucherPayments.xls">
                                            Export to excel
                                    </download-excel>

                                </div>
                            </div>
                        </div>
                        <!--Search Filter-->
                        <div class="mb-3">
                            <div class="row ml-2 align-items-center">
                                <div class="col-md-4 float-left">
                                    <div class="form-group">
                                        <label for="start_date" class="form-control-label">Start Date</label> 
                                        <input type="date" class="form-control" v-model="keywords.start_date" id="start_date" :max="keywords.end_date">
                                    </div>
                                </div>
                                <div class="col-md-4 float-left">
                                    <div class="form-group">
                                        <label for="end_date" class="form-control-label">End Date</label> 
                                        <input type="date" class="form-control" v-model="keywords.end_date" id="end_date" :min="keywords.start_date">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-success" @click="fetchList()">Filter</button>
                                </div>
                            </div>
                        </div>

                        <!--Error Messages-->
                        <div class="float-left m-3">
                            <div v-for="error in errors" class="text-danger">-{{ error }}</div>
                        </div>

                        <!--Table-->
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Created at</th>
                                    <th scope="col">Company</th>
                                    <th scope="col">Reference Number</th>
                                    <th scope="col">AP User</th>
                                    <th scope="col">Vendor Code</th>
                                    <th scope="col">Vendor Name</th>
                                    <th scope="col">Document Type</th>
                                    <th scope="col">Payment Terms</th>
                                    <th scope="col">Header Text</th>
                                    <th scope="col">Document Date</th>
                                    <th scope="col">Posting Date</th>
                                    <th scope="col">Baseline Date</th>
                                    <th scope="col">Document Code</th>
                                    <th scope="col">Expense Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="loading">
								        <span class="spinner spinner-primary mr-10"></span>
                                        <span class="text-muted text-center p-3">Loading...</span>
                                    </tr>
                                    <tr v-else-if="filteredQueues.length <= 0">
                                        <div class="text-muted text-center p-3">No results found</div>
                                    </tr>
                                    <tr v-else v-for="item in filteredQueues">
                                        <td>{{ item.id }}</td>
                                        <td>{{ item.created_at.slice(0, 10) }}</td>
                                        <td>{{ item.company_name }}</td>
                                        <td>{{ item.reference_number }}</td>
                                        <td>{{ item.ap_user }}</td>
                                        <td>{{ item.vendor_code }}</td>
                                        <td>{{ item.vendor_name }}</td>
                                        <td>{{ item.document_type }}</td>
                                        <td>{{ item.payment_terms }}</td>
                                        <td>{{ item.header_text }}</td>
                                        <td>{{ item.document_date }}</td>
                                        <td>{{ item.posting_date }}</td>
                                        <td>{{ item.baseline_date }}</td>
                                        <td>{{ item.document_code }}</td>
                                        <td>{{ item.expense_from }} to {{ item.expense_to }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer py-4">
                            <nav aria-label="...">
                                <ul class="pagination justify-content-end mb-0">
                                    <li class="page-item">
                                        <button :disabled="!showPreviousLink()" class="page-link" v-on:click="setPage(0)"> <i class="fas fa-angle-double-left"></i> </button>
                                    </li>
                                    <li class="page-item">
                                        <button :disabled="!showPreviousLink()" class="page-link" v-on:click="setPage(currentPage - 1)"> <i class="fas fa-angle-left"></i> </button>
                                    </li>
                                    <li class="page-item">  
                                        Page {{ currentPage + 1 }} of {{ totalPages }}  
                                    </li>
                                    <li class="page-item">
                                        <button :disabled="!showNextLink()" class="page-link" v-on:click="setPage(currentPage + 1)"><i class="fas fa-angle-right"></i> </button>
                                    </li>
                                    <li class="page-item">
                                        <button :disabled="!showNextLink()" class="page-link" v-on:click="setPage(totalPages - 1)"><i class="fas fa-angle-double-right"></i> </button>
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
import JsonExcel from 'vue-json-excel';
export default {
    components: { 'downloadExcel': JsonExcel },
    data(){
        return{
            loading: false,
            verifiedStatuses : ['Verified' , 'All'],
            payments: [],
            errors: [],
            keywords: {
                username: '',
                start_date: '',
                end_date: ''
            },
            currentPage: 0,
            itemsPerPage: 10,
            json_fields: {
                'ID': 'id',
                'Created At': 'created_at',
                'Company': 'company_name',
                'Reference Number': 'reference_number' ,
                'AP User': 'ap_user',
                'Vendor Code': 'vendor_code',
                'Vendor Name': 'vendor_name',
                'Document Type': 'document_type',
                'Payment Terms': 'payment_terms',
                'Header Text': 'header_text',
                'Document Date': 'document_date',
                'Posting Date': 'posting_date',
                'Baseline Date': 'baseline_date',
                'Vendor Name': 'vendor_name',
                'Document Code': 'document_code',
                'Expense From': 'expense_from',
                'Expense To': 'expense_to',
            }
        }
    },
    // created(){
    //     this.fetchList();
    // },
    methods:{
        fetchList() {
            this.loading = true;
            //default date range 2018-present
            if (!this.keywords.end_date) this.keywords.end_date = new Date().toJSON().slice(0, 10);
            if (!this.keywords.start_date) this.keywords.start_date = '2018-01-01';
            axios.post('/no-voucher-payment/all', this.keywords)
            .then(response => {
                this.payments = response.data;
                this.loading = false;
            })
            .catch(error => {
                this.errors = this.errors.response.data.errors;
                this.loading = false;
            });
        },
        setPage(pageNumber) {
            this.currentPage = pageNumber;
        },
        showPreviousLink() {
            return this.currentPage == 0 ? false : true;
        },
        showNextLink() {
            return this.currentPage == (this.totalPages - 1) ? false : true;
        },
        resetKeywords() {
            this.keywords =  {
                start_date: '',
                end_date: ''
            };
        }
    },
    computed:{
        totalPages() {
            return Math.ceil(this.payments.length / this.itemsPerPage);
        },
        filteredQueues() {
            var list = this.payments;
            var index = this.currentPage * this.itemsPerPage;
            var queues_array = list.slice(index, index + this.itemsPerPage);
            if(this.currentPage >= this.totalPages) {
                this.currentPage = this.totalPages - 1;
            }
            if(this.currentPage == -1) {
                this.currentPage = 0;
            }
            return queues_array;
        },
    }
    
}
</script>