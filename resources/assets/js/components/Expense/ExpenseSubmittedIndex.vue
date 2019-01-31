
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
                                    <h3 class="mb-0">Expense Submitted</h3>
                                    <h3 v-if="expenseByTsr.length" class="mb-0 float-right">TSR : {{ this.expenseByTsr[0].user.name }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">Attachment</th>
                                    <th scope="col">Type of Expense</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Price</th>
                                </tr>
                                </thead>
                                <tbody v-if="expenseByTsr.length">
                                    <tr v-for="(expenseBy, e) in expenseByTsr" v-bind:key="e">  
                                        <!-- <td v-if='!expenseBy.payments'> <input type="checkbox" checkedname="expenses_id" :value="expenseBy.id" v-model="expenses_id"></td> -->
                                        <td v-if='!expenseBy.payments'> <input type="checkbox" name="expenses_id" :value="expenseBy.id" :checked="true"></td>
                                        <td v-else>Paid</td>
                                        <td> <a :href="imageLink+expenseBy.attachment" target="__blank"><img class="rounded-circle" :src="imageLink+expenseBy.attachment" style="height: 70px; width: 70px" @error="noImage"></a></td>
                                        <td>{{ expenseBy.expenses_type.name }}</td>
                                        <td>{{ moment(expenseBy.created_at).format('ll') }}</td>
                                        <td>PHP {{ expenseBy.amount.toFixed(2) }} </td>
                                    </tr>
                                </tbody>
                            </table>
                            <span class="ml-3">{{ expenseByTsr.length }} item(s)</span>
                            <button v-if="expenseByTsr.length" type="button" class="btn btn-primary btn-round btn-fill float-right mb-3 mr-3"  @click="simulateExpenses(expenseByTsr[0].user_id)">Simulate</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Simulate Modal -->
        <div class="modal fade" id="simulateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <span class="closed" data-dismiss="modal">&times;</span>
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="max-width: 1300px">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCompanyLabel">Simulate</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" v-if="expenseByTsr.length">
                    <form>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="company-code">Company Code</label>
                                    <input type="text" id="company-code" class="form-control form-control-alternative" v-model="expenseByTsr[0].user.companies[0].code">
                                    <span class="text-danger" v-if="errors.company_code">{{ errors.company_code }}</span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="user">AP User</label>
                                    <input type="text" id="user" class="form-control form-control-alternative" v-model="this.simulate[0].sap_user.sap_id" disabled v-if="simulate.length">
                                    <span class="text-danger" v-if="errors.user">{{ errors.user}}</span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="document-type">Document Type</label>
                                    <input type="text" id="document-type" class="form-control" disabled v-model="document_type">
                                    <span class="text-danger" v-if="errors.document_type">{{ errors.document_type }}</span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="document-date">Document Date</label>
                                    <input type="date" id="document-date" class="form-control" v-model="document_date">
                                    <span class="text-danger" v-if="errors.document_date">{{ errors.document_date }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="company-name">Company Name</label>
                                    <input type="text" id="company-name" class="form-control" v-model="expenseByTsr[0].user.companies[0].name" disabled>
                                    <span class="text-danger" v-if="errors.company_name">{{ errors.company_name }}</span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="vendor-code">Vendor Code</label>
                                    <input type="text" id="vendor-code" class="form-control" v-model="expenseByTsr[0].user.vendor.vendor_code" disabled>
                                    <span class="text-danger" v-if="errors.vendor_code">{{ errors.vendor_code }}</span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="payment-terms">Payment Terms</label>
                                    <input type="text" id="payment-terms" class="form-control form-control-alternative" v-model="payment_terms">
                                    <span class="text-danger" v-if="errors.payment_terms">{{ errors.payment_terms }}</span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="posting-date">Posting Date</label>
                                    <input type="date" id="posting-date" class="form-control form-control-alternative" v-model="posting_date">
                                    <span class="text-danger" v-if="errors.posting_date">{{ errors.posting_date }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="reference-number">Reference Number</label>
                                    <input type="text" id="reference-number" class="form-control form-control-alternative">
                                    <span class="text-danger" v-if="errors.reference_number">{{ errors.reference_number }}</span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="vendor-name">Vendor Name</label>
                                    <input type="text" id="vendor-name" class="form-control form-control-alternative" v-model="expenseByTsr[0].user.name" disabled>
                                    <span class="text-danger" v-if="errors.vendor_name">{{ errors.vendor_name }}</span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="header-text">Header Text</label>
                                    <input type="text" id="header-text" class="form-control form-control-alternative" v-model="header_text">
                                    <span class="text-danger" v-if="errors.header_text">{{ errors.header_text }}</span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="posting-date">Baseline Date</label>
                                    <input type="date" id="baseline-date" class="form-control form-control-alternative" v-model="baseline_date">
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
                                <tr v-for="(simulatedExpense, s) in simulatedExpenses" v-bind:key="s">
                                    <td>{{ simulatedExpense.item }}</td>
                                    <td>{{ simulatedExpense.item_text }}</td>
                                    <td>{{ simulatedExpense.gl_account }}</td>
                                    <td>{{ simulatedExpense.description }}</td>
                                    <td>{{ simulatedExpense.assignment }}</td>
                                    <td>{{ simulatedExpense.input_tax_code }}</td>
                                    <td>{{ simulatedExpense.internal_order }}</td>
                                    <td>{{ simulatedExpense.amount }}</td>
                                    <td>{{ simulatedExpense.charge_type }}</td>
                                    <td>{{ simulatedExpense.business_area }}</td>
                                    <td>{{ simulatedExpense.or_number }}</td>
                                    <td>{{ simulatedExpense.supplier_name }}</td>
                                    <td>{{ simulatedExpense.supplier_address }}</td>
                                    <td>{{ simulatedExpense.supplier_tin_number }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-responsive mt-5" v-if="responses.length" style="width: 45% !important">
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
                                <tr v-for="(response, r) in responses" v-bind:key="r">
                                    <td>{{ response.return_message_type }}</td>
                                    <td>{{ response.return_message_id }}</td>
                                    <td>{{ response.return_message_number }}</td>
                                    <td>{{ response.return_message_description }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-round btn-fill" @click="checkExpenses(expenseByTsr,document_type,document_date,payment_terms,posting_date,header_text,baseline_date)">Check</button>
                </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
export default {
    props: ['expenseEntryId', 'dateEntry'],
    data(){
        return {
            simulatedExpenses: [],
            lineOneExpenses: {},
            checkedExpenses: [],
            expenseByTsr: [],
            simulate: [],
            payment_terms: 'NCOD',
            header_text: 'REIMBURSEMENT',
            document_type: 'KR',
            document_date: moment().format('YYYY-MM-DD'),
            posting_date: moment().format('YYYY-MM-DD'),
            baseline_date: moment().format('YYYY-MM-DD'),
            responses: [],
            errors: [],
            currentPage: 0,
            itemsPerPage: 10,
        }
    },
    created(){
        this.fetchExpenseByTsr();
    },
    methods:{
        moment,
        noImage(event){
            event.target.src = window.location.origin+'/img/brand/no-image.png';
        },
        fetchExpenseByTsr(){
            this.errors = [];
            axios.get(`/expense-report-bydate-peruser/${this.expenseEntryId}`)
            .then(response => { 
                this.expenseByTsr = response.data;
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            });
        },
        simulateExpenses(userId){
            this.simulatedExpenses = [];         
            var expenses_id = [];
            var checked = document.querySelectorAll("input[type=checkbox]:checked");
            checked.forEach(function(element) {
               expenses_id.push(parseInt(element.value));
            });
            this.checkedExpenses = this.expenseByTsr.filter(function(item) {
                return expenses_id.includes(item.id);
            });

            var sum = 0;
            this.checkedExpenses.filter(function(item) {
                sum = sum + item.amount;
            });

            this.lineOneExpenses = {
                item: 1,
                item_text: 'REIMBURSEMENT; ' + this.dateEntry,
                gl_account: this.expenseByTsr[0].user.vendor.vendor_code,
                description: this.expenseByTsr[0].user.name,
                assignment: '',
                input_tax_code: '',
                internal_order: '',
                amount: sum * -1,
                charge_type: '',
                business_area: '',
                or_number: '',
                supplier_name: '',
                supplier_address: '',
                supplier_tin_number: ''
            };

            this.simulatedExpenses.push(this.lineOneExpenses);

            axios.get(`/expense-simulate/${this.expenseEntryId}`)
            .then(response => {
                this.simulate = response.data;
                $('#simulateModal').modal('show');
            })
            .catch(error => { 
                this.errors = error.response.data.errors;
            })
        },
        checkExpenses(expenseByTsr,document_type,document_date,payment_terms,posting_date,header_text,baseline_date){
           axios.post('/payments', {
            expenseEntryId: this.expenseEntryId,
            posting_type: 'CHECK',
            app_server: this.simulate[0].sap_server.app_server, // sap_server.app_server
            system_id: this.simulate[0].sap_server.system_id, // sap_server.system_id
            instance_number: this.simulate[0].sap_server.system_number, // sap_server.system_number
            sap_name: this.simulate[0].sap_server.name, // sap_server.name
            client: this.simulate[0].sap_server.client,  // sap_server.client
            sap_user_id: this.simulate[0].sap_user.sap_id, //sap_user.id need to check first the server of tsr
            sap_password: this.simulate[0].sap_user.sap_password, // sap_user.password need to check first the server of tsr
            header_text: header_text,
            company_code: expenseByTsr[0].user.companies[0].code,
            document_date: document_date,
            posting_date: posting_date,
            // document_date: '01-29-2019',
            // posting_date: '01-29-2019',
            document_type: document_type,
            reference_number: 'sample1',
            // baseline_date: '01-29-2019',
            baseline_date: baseline_date,
            vendor_code: expenseByTsr[0].user.vendor.vendor_code,
            payment_terms: payment_terms,
            gl_account_i7: '0010180003',
            gl_account_i3: '0010180001'
        })
        .then(response => {
            this.responses = response.data;
        })
        .catch(error => { 
            this.errors= error.response.data.errors;
        })


            
        //    axios.post('/payments', {
        //        expenseId: expenseId,
        //        userId: userId,
        //    })
        //    .then(response => {
        //        $('#viewModal').modal('hide');
        //        alert('Expense Successfully paid')
        //    })
        //    .catch(error => { 
        //        this.errors= error.response.data.errors;
        //    })
        }
    },
    computed:{
        totalPages() {
            return Math.ceil(this.expenseByTsr.length / this.itemsPerPage)
        },
        filteredQueues() {
            var index = this.currentPage * this.itemsPerPage;
            var queues_array = this.expenseByTsr.slice(index, index + this.itemsPerPage);

            return queues_array;
        },
        imageLink(){
            return window.location.origin+'/storage/';
        },
    }
}
</script>
