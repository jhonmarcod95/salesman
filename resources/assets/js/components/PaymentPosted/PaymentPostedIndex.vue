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
                                    <h3 class="mb-0">Posted Expenses</h3>
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
                                            <option v-for="(company,c) in companies" v-bind:key="c" :value="company.name"> {{ company.name }}</option>
                                        </select>
                                        <span class="text-danger" v-if="errors.company  ">{{ errors.company[0] }}</span>
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
                                <div class="col-md-2">
                                    <button class="btn btn-sm btn-primary" @click="fetchPaymentHeaders"> Filter</button>
                                    <download-excel
                                        :data   = "paymentHeaders"
                                        :fields = "json_fields"
                                        class   = "btn btn-sm btn-default"
                                        name    = "Posted Expense report.xls">
                                            Export to excel
                                    </download-excel>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">Document Code</th>
                                    <th scope="col">Amount(PHP)</th>
                                    <th scope="col">Company Code</th>
                                    <th scope="col">Company Name</th>
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
                                </tr>
                                </thead>
                                <tbody v-if="paymentHeaders.length">
                                    <tr v-for="(paymentHeader, p) in filteredQueues" v-bind:key="p">
                                        <td class="text-right">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a class="dropdown-item" href="#simulateModal" data-toggle="modal" @click="copyObject(paymentHeader)">View</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ paymentHeader.document_code }}</td>
                                        <td>{{ paymentHeader.payment_detail[0].amount.toString().slice(1)+ '.00' }}</td>
                                        <td>{{ paymentHeader.company_code }}</td>
                                        <td>{{ paymentHeader.company_name }}</td>
                                        <td>{{ paymentHeader.reference_number }}</td>
                                        <td>{{ paymentHeader.ap_user }}</td>
                                        <td>{{ paymentHeader.vendor_code }}</td>
                                        <td>{{ paymentHeader.vendor_name }}</td>
                                        <td>{{ paymentHeader.document_type }}</td>
                                        <td>{{ paymentHeader.payment_terms }}</td>
                                        <td>{{ paymentHeader.header_text }}</td>
                                        <td>{{ paymentHeader.document_date }}</td>
                                        <td>{{ paymentHeader.posting_date }}</td>
                                        <td>{{ paymentHeader.baseline_date }}</td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                       <tr>
                                           <td>No data available in the table</td>
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

        <!-- View Simulate Modal -->
        <div class="modal fade" id="simulateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <span class="closed" data-dismiss="modal">&times;</span>
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="max-width: 1300px">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCompanyLabel">Accounting Entry</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" >
                    <form>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="company-code">Company Code</label>
                                    <input type="text" id="company-code" class="form-control form-control-alternative" v-model="copiedObject.company_code" disabled>
                                    <span class="text-danger" v-if="errors.company_code">{{ errors.company_code }}</span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="user">AP User</label>
                                    <input type="text" id="user" class="form-control form-control-alternative" v-model="copiedObject.ap_user" disabled>
                                    <span class="text-danger" v-if="errors.user">{{ errors.user}}</span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="document-type">Document Type</label>
                                    <input type="text" id="document-type" class="form-control" disabled v-model="copiedObject.document_type">
                                    <span class="text-danger" v-if="errors.document_type">{{ errors.document_type }}</span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="document-date">Document Date</label>
                                    <input type="date" id="document-date" class="form-control" v-model="copiedObject.document_date" disabled>
                                    <span class="text-danger" v-if="errors.document_date">{{ errors.document_date }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="company-name">Company Name</label>
                                    <input type="text" id="company-name" class="form-control" v-model="copiedObject.company_name" disabled>
                                    <span class="text-danger" v-if="errors.company_name">{{ errors.company_name }}</span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="vendor-code">Vendor Code</label>
                                    <input type="text" id="vendor-code" class="form-control" v-model="copiedObject.vendor_code" disabled>
                                    <span class="text-danger" v-if="errors.vendor_code">{{ errors.vendor_code }}</span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="payment-terms">Payment Terms</label>
                                    <input type="text" id="payment-terms" class="form-control form-control-alternative" v-model="copiedObject.payment_terms" disabled>
                                    <span class="text-danger" v-if="errors.payment_terms">{{ errors.payment_terms }}</span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="posting-date">Posting Date</label>
                                    <input type="date" id="posting-date" class="form-control form-control-alternative" v-model="copiedObject.posting_date" disabled>
                                    <span class="text-danger" v-if="errors.posting_date">{{ errors.posting_date }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="reference-number">Reference Number</label>
                                    <input type="text" id="reference-number" class="form-control form-control-alternative" v-model="copiedObject.reference_number" disabled>
                                    <span class="text-danger" v-if="errors.reference_number">{{ errors.reference_number }}</span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="vendor-name">Vendor Name</label>
                                    <input type="text" id="vendor-name" class="form-control form-control-alternative" v-model="copiedObject.vendor_name" disabled>
                                    <span class="text-danger" v-if="errors.vendor_name">{{ errors.vendor_name }}</span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="header-text">Header Text</label>
                                    <input type="text" id="header-text" class="form-control form-control-alternative" v-model="copiedObject.header_text" disabled>
                                    <span class="text-danger" v-if="errors.header_text">{{ errors.header_text }}</span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="posting-date">Baseline Date</label>
                                    <input type="date" id="baseline-date" class="form-control form-control-alternative" v-model="copiedObject.baseline_date" disabled>
                                    <span class="text-danger" v-if="errors.baseline_date">{{ errors.baseline_date }}</span>
                                </div>
                            </div>
                        </div>
                    </form>    
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                            <tr>
                                <th scope="col">Item</th>
                                <th scope="col">Item text</th>
                                <th scope="col">Attachment</th>
                                <th scope="col">GL account</th>
                                <th scope="col">Description</th>
                                <th scope="col">Assignment</th>
                                <th scope="col">Input tax code</th>
                                <th scope="col">Internal order</th>
                                <th scope="col">Amount</th>
                                <th scope="col">Charge type</th>
                                <th scope="col">Business area</th>
                                <th scope="col">OR number</th>
                                <th scope="col">Supplier name</th>
                                <th scope="col">Supplier address</th>
                                <th scope="col">Supplier TIN number</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(paymentDetail, p) in copiedObject.payment_detail" v-bind:key="p">
                                    <td>{{ paymentDetail.item }}</td>
                                    <td>{{ paymentDetail.item_text }}</td>
                                    <td v-if="paymentDetail.internal_order && paymentDetail.internal_order !== '~'"><a :href="imageLink+copiedObject.payments[p - 1].expense.attachment" target="__blank"><img class="rounded-circle" :src="imageLink+copiedObject.payments[p - 1].expense.attachment" style="height: 70px; width: 70px" @error="noImage"></a></td>
                                    <td v-else></td>
                                    <td>{{ paymentDetail.gl_account }}</td>
                                    <td>{{ paymentDetail.description.toUpperCase() }}</td>
                                    <td>{{ paymentDetail.assignment }}</td>
                                    <td>{{ paymentDetail.input_tax_code }}</td>
                                    <td>{{ paymentDetail.internal_order }}</td>
                                    <td>{{ paymentDetail.amount }}</td>
                                    <td>{{ paymentDetail.charge_type }}</td>
                                    <td>{{ paymentDetail.business_area }}</td>
                                    <td>{{ paymentDetail.or_number }}</td>
                                    <td>{{ paymentDetail.supplier_name }}</td>
                                    <td>{{ paymentDetail.supplier_address }}</td>
                                    <td>{{ paymentDetail.supplier_tin_number }}</td>
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
import loader from '../Loader';
import JsonExcel from 'vue-json-excel';

export default {
    components: { loader, 'downloadExcel': JsonExcel },
    data(){
        return{
            paymentHeaders: [],
            companies: [],
            company: '',
            startDate: '',
            endDate: '',
            copiedObject: [],
            errors: [],
            keywords: '',
            currentPage: 0,
            itemsPerPage: 10,
            loading: false,
            json_fields: {
                'DOCUMENT CODE': 'document_code',
                'AMOUNT(PHP)': {
                    callback: (value) => {
                        return value.payment_detail[0].amount.toString().slice(1) + '.00';
                    },
                },
                'COMPANY CODE': 'company_code',
                'COMPANY NAME': 'company_name',
                'REFERENCE NUMBER': 'reference_number',
                'AP USER': 'ap_user',
                'VENDOR CODE': 'vendor_code',
                'VENDOR NAME': 'vendor_name',
                'DOCUMENT TYPE': 'document_type',
                'PAYMENT TERMS': 'payment_terms',
                'HEADER TEXT': 'header_text',
                'DOCUMENT DATE': 'document_date',
                'POSTING DATE': 'posting_date',
                'BASELINE DATE': 'baseline_date',
            }
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
        copyObject(paymentHeader){
            this.copiedObject = Object.assign({}, paymentHeader)
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
        fetchPaymentHeaders(){
            this.errors = [];
            axios.post('/expense-posteds',{
                company: this.company ? this.company : "",
                startDate: this.startDate,
                endDate: this.endDate
            })
            .then(response => { 
                this.paymentHeaders = response.data;
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
        filteredPaymentHeaders(){
            let self = this;
            return self.paymentHeaders.filter(paymentHeader => {
                return paymentHeader.vendor_name.toLowerCase().includes(this.keywords.toLowerCase())
            });
        },
        totalPages() {
            return Math.ceil(this.filteredPaymentHeaders.length / this.itemsPerPage)
        },
        filteredQueues() {
            var index = this.currentPage * this.itemsPerPage;
            var queues_array = this.filteredPaymentHeaders.slice(index, index + this.itemsPerPage);

            if(this.currentPage >= this.totalPages) {
                this.currentPage = this.totalPages - 1
            }

            if(this.currentPage == -1) {
                this.currentPage = 0;
            }

            return queues_array;
        },
        imageLink(){
            return window.location.origin+'/storage/';
        }
    },
}
</script>