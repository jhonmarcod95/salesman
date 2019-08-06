
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
                                        <td v-if='(!expenseBy.payments && expenseBy.receipt_expenses && !expenseBy.route_transportation) || (expenseBy.route_transportation && !expenseBy.payments && !expenseBy.receipt_expenses)'> <input type="checkbox" name="expenses_id" :value="expenseBy.id" checked="checked"></td>
                                        <td v-else-if="!expenseBy.payments && !expenseBy.receipt_expenses && !expenseBy.route_transportation" class="text-danger">Receipt unverified</td>
                                        <td v-else class="text-primary">{{ expenseBy.payments.document_code}}</td>
                                        <td> <a :href="imageLink+expenseBy.attachment" target="__blank"><img class="rounded-circle" :src="imageLink+expenseBy.attachment" style="height: 70px; width: 70px" @error="noImage"></a></td>
                                        <td>{{ expenseBy.expenses_type.name }}</td>
                                        <td>{{ moment(expenseBy.created_at).format('ll') }}</td>
                                        <td>PHP {{ expenseBy.amount.toFixed(2) }} </td>
                                    </tr>
                                </tbody>
                            </table>
                            <span class="ml-3">{{ expenseByTsr.length }} item(s)</span>
                            <div class="text-center mb-2">
                                <span v-if="errorsExpense" class="text-danger">Please Select Expense</span>
                            </div>
                            <button v-if="expenseByTsr.length" type="button" class="btn btn-primary btn-round btn-fill float-right mb-3 mr-3"  :disabled="isDisabled" @click="simulateExpenses(expenseByTsr[0].user_id)">Simulate</button>
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
                                    <input type="text" id="company-code" class="form-control form-control-alternative" v-model="expenseByTsr[0].user.companies[0].code" disabled>
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
                                    <input type="date" id="document-date" class="form-control" v-model="document_date" disabled>
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
                                    <input type="text" id="payment-terms" class="form-control form-control-alternative" v-model="payment_terms" disabled>
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
                                    <input type="text" id="reference-number" class="form-control form-control-alternative" v-model="this.simulate[0].reference_number" disabled v-if="simulate.length">
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
                                    <input type="text" id="header-text" class="form-control form-control-alternative" v-model="header_text" disabled>
                                    <span class="text-danger" v-if="errors.header_text">{{ errors.header_text }}</span>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="posting-date">Baseline Date</label>
                                    <input type="date" id="baseline-date" class="form-control form-control-alternative" v-model="baseline_date" disabled>
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
                                    <td>{{ simulatedExpense.description.toUpperCase() }}</td>
                                    <td>{{ simulatedExpense.assignment }}</td>
                                    <td>{{ simulatedExpense.input_tax_code }}</td>
                                    <td>{{ simulatedExpense.internal_order }}</td>
                                    <td>{{ simulatedExpense.amount }}</td>
                                    <td>{{ simulatedExpense.charge_type.name }}</td>
                                    <td>{{ simulatedExpense.business_area }}</td>
                                    <td>{{ simulatedExpense.or_number }}</td>
                                    <td>{{ simulatedExpense.supplier_name }}</td>
                                    <td>{{ simulatedExpense.supplier_address }}</td>
                                    <td>{{ simulatedExpense.supplier_tin_number }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-danger text-center mt-3 mb-3" v-if="responses.length && sap_errors > 0 && !post_successful">
                        <span>Unable to post due to errors</span>
                    </div>
                     <div class="text-primary text-center mt-3 mb-3" v-if="responses.length && sap_errors == 0 && !post_successful">
                        <span>Ready for posting</span>
                    </div>
                    <div class="text-success text-center mt-3 mb-3" v-if="responses.length && sap_errors == 0 && post_successful">
                        <span>Successfully posted</span>
                    </div>
                    <div class="table-responsive" v-if="responses.length">
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
                    <button id="post_btn" type="button" class="btn btn-primary btn-round btn-fill" @click="checkExpenses(expenseByTsr,simulatedExpenses,document_type,document_date,payment_terms,posting_date,header_text,baseline_date,expenseByTsr[0].user.companies[0].name,expenseByTsr[0].user.name,'POST')">POST</button>
                    <button id="check_btn" type="button" class="btn btn-primary btn-round btn-fill" @click="checkExpenses(expenseByTsr,simulatedExpenses,document_type,document_date,payment_terms,posting_date,header_text,baseline_date,expenseByTsr[0].user.companies[0].name,expenseByTsr[0].user.name,'CHECK')">Check</button>
                </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
import computation from '../../artisans';
import loader from '../Loader'

export default {
    components: { loader },
    props: ['expenseEntryId', 'dateEntry', 'currentWeek'],
    data(){
        return {
            errorsExpense: false,
            post_successful: false,
            simulatedExpenses: [],
            lineOneExpenses: {},
            checkedExpenses: [],
            expenseByTsr: [],
            simulate: [],
            expenses_id: [],
            gl_account_i7: [],
            gl_account_i3: [],
            payment_terms: 'NCOD',
            header_text: 'REIMBURSEMENT',
            document_type: 'KR',
            document_date: moment().format('YYYY-MM-DD'),
            posting_date: moment().format('YYYY-MM-DD'),
            baseline_date: moment().format('YYYY-MM-DD'),
            responses: [],
            sap_errors: 0,
            errors: [],
            currentPage: 0,
            itemsPerPage: 10,
            loading: true
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
            this.loading = true;
            this.errors = [];
            axios.get(`/expense-report-bydate-peruser/${this.expenseEntryId}`)
            .then(response => { 
                this.expenseByTsr = response.data;
                this.loading = false;
            })
            .catch(error => {
                this.errors = error.response.data.errors;
                this.loading = false;
            });
        },
        simulateExpenses(userId){
            document.getElementById("check_btn").disabled = false;
            this.responses = [];
            let vm = this;
            this.simulatedExpenses = [];
            vm.expenses_id = [];
            var checked = document.querySelectorAll("input[type=checkbox]:checked");
            if(!checked.length){
                this.errorsExpense = true;
                return false;
            } this.errorsExpense = false;
            checked.forEach(function(element) {
               vm.expenses_id.push(parseInt(element.value));
            });
            this.checkedExpenses = this.expenseByTsr.filter(function(item) {
                return vm.expenses_id.includes(item.id);
            });

            var createdDate = '';
            var ableToPost = 0;
            if(this.checkedExpenses.length == 1){
                ableToPost = 0;
            }
            var validate = this.checkedExpenses.filter(function(checkedExpense) { //Validation for posting of expense(Posting must be in the same month and year)
                if(createdDate){
                    moment(createdDate).isSame(checkedExpense.created_at, 'month','year') == false ? ableToPost = ableToPost + 1 : '';
                }
                createdDate = checkedExpense.created_at;
            });

            if(ableToPost > 0){
                alert('Please select expenses with the same month and year');
                return false;
            }else{
                 var sum = 0;

                this.checkedExpenses.filter(function(checkedExpense) { //Get the sum of all checked expenes
                    sum = sum + checkedExpense.amount;
                });

                var filteredBusinessArea = this.checkedExpenses[0].user.companies[0].business_area.filter(function(businessArea){ // loop business area to get correct business area
                    return businessArea.location_id == vm.checkedExpenses[0].user.location[0].id;
                });

                var item = 1;
                var tax_amountI3 = 0;
                var tax_amountI7 = 0;
                var tax_amount = 0;
                vm.checkedExpenses.filter(function(checkedExpense) {  //Generate the line 2 to n paramater to post
                    var filteredGl = checkedExpense.expenses_type.expense_charge_type.charge_type.expense_gl.filter(function(gl_account){ // loop expense gl to get correct gl account and description
                                        return gl_account.charge_type == checkedExpense.expenses_type.expense_charge_type.charge_type.name && gl_account.company_code == checkedExpense.user.companies[0].code;
                                    });
                    var filteredInternalOrders =  checkedExpense.user.internal_orders.filter(function(internal_order){ // loop internal order to get correct internal order
                                        return internal_order.charge_type == checkedExpense.expenses_type.expense_charge_type.charge_type.name;
                                    });            
                    item = item + 1;
                    var amount = "";
                    var tax_code = "";
                    var bol_tax_amount = false;
                    if(checkedExpense.user.companies[0].code == "1100" || checkedExpense.user.companies[0].code == "CSCI"){
                        amount = checkedExpense.amount;
                        tax_code = "IX";
                        bol_tax_amount = false;
                    }else if(checkedExpense.user.companies[0].code == "PFMC" && filteredBusinessArea[0].business_area.substring(0,2) == "FD"){
                        amount = checkedExpense.amount;
                        tax_code = "IX";
                    }else{
                        tax_code = checkedExpense.receipt_expenses ? checkedExpense.receipt_expenses.receipt_type.tax_code : "IX";

                        if (tax_code == "IX"){
                            var round_off = checkedExpense.amount;
                            amount = round_off.toFixed(2);
                            var round_off_tax_amount = checkedExpense.amount;
                            tax_amount = round_off_tax_amount.toFixed(2);
                        }
                        else{
                            var round_off = checkedExpense.amount / 1.12; //(Round off to two digit)
                            amount = round_off.toFixed(2);
                            // amount = checkedExpense.amount;
                            var round_off_tax_amount = checkedExpense.amount - (checkedExpense.amount / 1.12);
                            tax_amount = round_off_tax_amount.toFixed(2);
                            bol_tax_amount = true;
                        }
                    }
                    var internal_order = '';
                    if(filteredInternalOrders.length != 0) {  
                        internal_order = filteredInternalOrders[0].internal_order;
                    }else{
                        internal_order = "";
                    }

                    var expenses = { 
                        item: item,
                        item_text: checkedExpense.expenses_type.name.toUpperCase() + ' '+ moment(checkedExpense.created_at).format('L'),
                        gl_account: filteredGl[0].gl_account,
                        description: filteredGl[0].gl_description,
                        assignment: '',
                        // input_tax_code : checkedExpense.receipt_expenses.receipt_type.tax_code,
                        input_tax_code : tax_code,
                        internal_order: internal_order,
                        // amount: checkedExpense.amount,
                        amount: amount,
                        charge_type: checkedExpense.expenses_type.expense_charge_type.charge_type.name,
                        business_area: filteredBusinessArea[0].business_area,
                        or_number:  checkedExpense.receipt_expenses ? checkedExpense.receipt_expenses.receipt_number : '',
                        supplier_name: checkedExpense.receipt_expenses ? checkedExpense.receipt_expenses.vendor_name : '',
                        supplier_address: checkedExpense.receipt_expenses ? checkedExpense.receipt_expenses.vendor_address : '',
                        supplier_tin_number: checkedExpense.receipt_expenses ? checkedExpense.receipt_expenses.tin_number : '',

                    }
                    if(bol_tax_amount && checkedExpense.receipt_expenses.receipt_type.tax_code == 'I3'){
                        // expenses.tax_amountI3 = tax_amount;
                        var tax_amountI3_computation = parseFloat(tax_amountI3) + parseFloat(tax_amount);  
                        tax_amountI3 = tax_amountI3_computation.toFixed(2);
                    }else if (bol_tax_amount && checkedExpense.receipt_expenses.receipt_type.tax_code == 'I7'){
                        // expenses.tax_amountI7 = tax_amount;
                        var tax_amountI7_computation = parseFloat(tax_amountI7) + parseFloat(tax_amount);
                        tax_amountI7 = tax_amountI7_computation.toFixed(2);
                    }else {}
                    item + 1;
                    vm.simulatedExpenses.push(expenses);
                });
                this.gl_account_i7 = Object.values(this.checkedExpenses[0].user.companies[0].gl_taxcode).filter(function (tax_code){
                    return tax_code.tax_code == 'I7';
                });
                        
                if(tax_amountI7){
                    item = item + 1;
                    var expenses = {
                        item: item,
                        item_text: '',
                        gl_account: this.gl_account_i7[0].gl_account,
                        description: this.gl_account_i7[0].gl_description,
                        assignment: '',
                        input_tax_code : 'I7',
                        internal_order: '',
                        amount: tax_amountI7,
                        charge_type: '',
                        business_area: filteredBusinessArea[0].business_area,
                        or_number: '',
                        supplier_name: '',
                        supplier_address: '',
                        supplier_tin_number: '',
                    }
                    vm.simulatedExpenses.push(expenses);
                }
                this.gl_account_i3 = Object.values(this.checkedExpenses[0].user.companies[0].gl_taxcode).filter(function (tax_code){
                    return tax_code.tax_code == 'I3';
                });
                if(tax_amountI3){
                    item = item + 1;
                    var expenses = { 
                        item: item,
                        item_text: '',
                        gl_account: this.gl_account_i3[0].gl_account,
                        description: this.gl_account_i3[0].gl_description,
                        assignment: '',
                        input_tax_code : 'I3',
                        internal_order: '',
                        amount: tax_amountI3,
                        charge_type: '',
                        business_area: filteredBusinessArea[0].business_area,
                        or_number: '',
                        supplier_name: '',
                        supplier_address: '',
                        supplier_tin_number: '',
                    }
                    vm.simulatedExpenses.push(expenses);
                }

                /* line one to the first ***************************/
                var line_one_amount = this.simulatedExpenses.Sum('amount') * -1;
                this.lineOneExpenses = { //Generate the line 1 paramater to post
                    item: 1,
                    item_text: 'SALESFORCE REIMBURSEMENT; ' + this.dateEntry,
                    gl_account: this.expenseByTsr[0].user.vendor.vendor_code,
                    description: this.expenseByTsr[0].user.name,
                    assignment: '',
                    input_tax_code: '',
                    internal_order: '',
                    amount: line_one_amount.toFixed(2),
                    charge_type: '',
                    business_area: filteredBusinessArea[0].business_area,
                    or_number: '',
                    supplier_name: '',
                    supplier_address: '',
                    supplier_tin_number: ''
                };
                this.simulatedExpenses.unshift(this.lineOneExpenses); //push at first index instead last
                /* ****************************************************/

                    axios.get(`/expense-simulate/${this.expenseEntryId}`)
                    .then(response => {
                        this.simulate = response.data;
                        document.getElementById("post_btn").disabled = true; 
                        $('#simulateModal').modal('show');
                    })
                    .catch(error => { 
                        this.errors = error.response.data.errors;
                    })
                }
        },
        checkExpenses(expenseByTsr,simulatedExpenses,document_type,document_date,payment_terms,posting_date,header_text,baseline_date,company_name,vendor_name,posting_type){
            this.loading = true;
            let vm = this;
            vm.sap_errors = 0;
            document.getElementById("post_btn").disabled = true;
            document.getElementById("check_btn").disabled = true; 
            this.responses = [];
            axios.post('/payments', {
                company_name: company_name,
                vendor_name: vendor_name,
                expenseId: vm.expenses_id,
                userId: this.expenseByTsr[0].user.id,
                expenseEntryId: this.expenseEntryId,
                posting_type: posting_type,
                app_server: this.simulate[0].sap_server.app_server, // sap_server.app_server
                system_id: this.simulate[0].sap_server.system_id, // sap_server.system_id
                instance_number: this.simulate[0].sap_server.system_number, // sap_server.system_number
                sap_name: this.simulate[0].sap_server.name, // sap_server.name
                client: this.simulate[0].sap_server.client,  // sap_server.client
                sap_user_id: this.simulate[0].sap_user.sap_id, //sap_user.id need to check first the server of tsr
                sap_password: this.simulate[0].sap_user.sap_password, // sap_user.password need to check first the server of tsr
                header_text: header_text,
                company_code: expenseByTsr[0].user.companies[0].code,
                document_date: moment(document_date).format('L'),
                posting_date: moment(posting_date).format('L'),
                document_type: document_type,
                reference_number: this.simulate[0].reference_number,
                baseline_date: moment(baseline_date).format('L'),
                vendor_code: expenseByTsr[0].user.vendor.vendor_code,
                payment_terms: payment_terms,
                gl_account_i7: this.gl_account_i7[0].gl_account,
                gl_account_i3: this.gl_account_i3[0].gl_account,
                simulatedExpenses: simulatedExpenses
            })
            .then(response => {
                this.responses = response.data;
                this.responses.filter(function(response){
                    if(response.return_message_type == 'E'){
                        vm.sap_errors++
                    }
                });
                if(posting_type == 'CHECK' && this.responses.length){
                    if(vm.sap_errors == 0){
                        this.post_successful = false;
                        document.getElementById("post_btn").disabled = false;
                        document.getElementById("check_btn").disabled = false;
                    }
                }else{
                    if(vm.sap_errors == 0){
                        this.post_successful = true;
                        document.getElementById("check_btn").disabled = true; 
                        document.getElementById("post_btn").disabled = true;
                        this.fetchExpenseByTsr(); 
                    }
                }
                this.loading = false;
            })
            .catch(error => { 
                this.errors = error.response.data.errors;
                this.loading = false;
            })
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
        isDisabled(){
            var current = this.currentWeek.split('-')[0];
            var selected = moment(this.dateEntry.split('to')[0]).format('ll');
            var c = moment(current).format('YYYY MM DD');
            var s = moment(selected).format('YYYY MM DD');

            return false; //temporary only
            if(moment(c).isSame(s, 'day')){
               return false;
            }else if(moment(c).diff(s, 'day') <= 7){
               return false;
            }else{
                return true
            }
        }
    }
}
</script>
