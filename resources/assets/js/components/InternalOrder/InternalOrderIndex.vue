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
                                    <h3 class="mb-0">Internal Order List</h3>
                                </div>
                               <div class="col text-right">
                                   <a href="#addModal" data-toggle="modal" class="btn btn-sm btn-primary">Add New</a>
                               </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control form-control-sm" placeholder="Search" v-model="keywords" id="name">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                   <th scope="col"></th>
                                    <th scope="col">ID</th>
                                    <th scope="col">User</th>
                                    <th scope="col">Expense Type</th>
                                    <th scope="col">Charge Type</th>
                                    <th scope="col">Amount Limit Per Day</th>
                                    <th scope="col">Internal Order</th>
                                    <th scope="col">SAP Server</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(internalOrder,i) in filteredQueues" v-bind:key="i">
                                        <td class="text-right">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a class="dropdown-item" href="#editModal" data-toggle="modal" @click="copyObject(internalOrder)">Edit</a>
                                                    <a class="dropdown-item" href="#deleteModal" data-toggle="modal" @click="getInternalOrderId(internalOrder.id)">Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ internalOrder.id }}</td>
                                        <td>{{ internalOrder.user.name }}</td>
                                        <td>{{ internalOrder.charge_type.expense_charge_type.expense_type.name }}</td>
                                        <td>{{ internalOrder.charge_type.name }}</td>
                                        <td>{{ getExpenseRate(internalOrder).amount }}</td>
                                        <td>{{ internalOrder.internal_order }}</td>
                                        <td>{{ internalOrder.sap_server }}</td>
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

        <!-- Add New Internal Order Modal -->
        <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <loader v-if="isLoading"></loader>
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Internal Order</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                            <div class="col-lg-12">
                               <div class="form-group bmd-form-group">
                                   <label class="form-control-label" for="user">User</label>
                                   <select id="user_id" class="form-control" v-model="internal_order.tsr">
                                       <option v-for="(tsr, t) in tsrs" :key="t" :value="tsr">{{ tsr.first_name + ' ' + tsr.last_name}}</option> 
                                   </select>
                                </div>
                            </div>
                            <div class="col-lg-12">
                               <div class="form-group bmd-form-group">
                                   <label class="form-control-label" for="expense_type">Expense Type</label> 
                                   <select class="form-control" v-model="internal_order.expense_type">
                                       <option v-for="(expense_type, e) in expense_types" :key="e" :value="expense_type.expense_charge_type.charge_type.name">{{ expense_type.name }}</option> 
                                   </select>
                                   <span class="text-danger small" v-if="errors.charge_type">{{ errors.charge_type[0] }}</span>
                               </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label" for="internal_order">Internal Order</label>
                                    <input type="text" id="internal_order" class="form-control form-control-alternative" v-model="internal_order.internal_order">
                                    <span class="text-danger small" v-if="errors.internal_order">{{ errors.internal_order[0] }}</span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label" for="amount">Desired Amount</label>
                                    <input type="text" id="amount" class="form-control form-control-alternative" v-model="internal_order.amount">
                                    <span class="text-danger small" v-if="errors.amount">{{ errors.amount[0] }}</span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label" for="amount">Validity Date</label>
                                    <input type="date" id="validity_date" class="form-control form-control-alternative" v-model="internal_order.validity_date" :disabled="!internal_order.amount">
                                    <span class="text-danger small" v-if="errors.validity_date">{{ errors.validity_date[0] }}</span>
                                </div>
                            </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss='modal'>Close</button>
                        <button class="btn btn-primary" @click="addInternalOrder(internal_order)">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <loader v-if="isLoading"></loader>
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Delete Interal</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    Are you sure you want to delete this Internal Order?
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss='modal'>Close</button>
                        <button class="btn btn-warning" @click="deleteInternalOrder()">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <loader v-if="isLoading"></loader>
           <div class="modal-dialog modal-dialog-centered" role="document">
               <div class="modal-content">
                   <div class="modal-header">
                       <h5 class="modal-title" id="exampleModalLabel">Edit Internal Order</h5>
                       <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                           <span aria-hidden="true">&times;</span>
                       </button>
                   </div>
                   <div class="modal-body">
                       <!-- <div class="row"> -->
                        
                           <div class="col-lg-12">
                               <div class="form-group bmd-form-group">
                                   <label class="form-control-label" for="classification">User</label>
                                   <select id="user_id" class="form-control" v-model="internal_order_copied.user_id" disabled>
                                       <option v-for="(tsr, t) in tsrs" :key="t" :value="tsr.user_id">{{ tsr.first_name + ' ' + tsr.last_name}}</option> 
                                   </select>
                                </div>
                            </div>
                            <div class="col-lg-12">
                               <div class="form-group bmd-form-group">
                                   <label class="form-control-label" for="classification">Expense Type</label> 
                                   <select class="form-control" v-model="internal_order_copied_charge_type">
                                       <option v-for="(expense_type, e) in expense_types" :key="e" :value="expense_type.expense_charge_type.charge_type.name">{{ expense_type.name }}</option> 
                                   </select>
                                   <span class="text-danger small" v-if="errors.charge_type">{{ errors.charge_type[0] }}</span>
                               </div>
                           </div>
                           <div class="col-md-12">
                               <div class="form-group">
                                   <label class="form-control-label" for="internal_order">Internal Order</label>
                                   <input type="text" id="internal_order" class="form-control form-control-alternative" v-model="internal_order_copied.internal_order">
                                   <span class="text-danger small" v-if="errors.internal_order">{{ errors.internal_order[0] }}</span>
                               </div>
                           </div>
                           <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label" for="amount">Desired Amount</label>
                                    <input type="number" id="amount" class="form-control form-control-alternative" v-model="default_amount.amount" :min="1">
                                    <span class="text-danger small" v-if="errors.amount">{{ errors.amount[0] }}</span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label" for="amount">Validity Date</label>
                                    <input type="date" id="validity_date" class="form-control form-control-alternative" v-model="default_amount.validity_date" :disabled="!default_amount.amount">
                                    <span class="text-danger small" v-if="errors.validity_date">{{ errors.validity_date[0] }}</span>
                                </div>
                            </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss='modal'>Close</button>
                        <button class="btn btn-primary" @click="updateInternalOrder(internal_order_copied, internal_order_copied_charge_type, default_amount)">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import { min } from 'lodash';

;
export default {
    data(){
        return{
            internal_orders: [],
            internal_order: {
                amount: '',
                validity_date: ''
            },
            tsrs: [],
            expense_types: [],
            internal_order_id: '',
            internal_order_copied: [],
            errors: [],
            keywords: '',
            currentPage: 0,
            itemsPerPage: 10,
            internal_order_copied_charge_type: '',
            default_expense_type:'',
            default_amount: '',
            servers: [],
            expense_id: '',
            isLoading: false
        }
    },
    created(){
        this.fetchInternalOrders();
        this.fetchTsrs();
        this.fetchExpensesTypes();
        this.fetchServer();
    },
    methods:{
        getExpenseRate(internalOrder){
            var amount = '';
            internalOrder.user.expense_rate.findIndex(element => {
                if(element.expenses_type_id == internalOrder.charge_type.expense_charge_type.expense_type.id){
                    amount = element;
                }
            })
            return amount;
        },
        copyObject(internalOrder){
            this.errors = [];
            this.internal_order_copied = Object.assign({}, internalOrder);
            this.internal_order_copied_charge_type = internalOrder.charge_type.name;
            this.default_expense_type = internalOrder.charge_type.expense_charge_type.expense_type.id;
            this.default_amount = this.getExpenseRate(internalOrder);
        },
        getInternalOrderId(id){
            this.internal_order_id = id;
        },
        fetchInternalOrders(){
            axios.get('/internal-orders')
            .then(response => {
                this.internal_orders = response.data;
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            })
        },
        fetchTsrs(){
            axios.get('/tsr-all')
            .then(response => {
                this.tsrs = response.data;
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            })
        },
        fetchExpensesTypes(){
            axios.get('/expenses-all')
            .then(response => {
                this.expense_types = response.data;
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            })
        },
        fetchServer(){
            axios.get('/sap/server')
            .then(response => { 
                this.servers = response.data;
            })
            .catch(error => { 
                this.errors = response.data.errors;
            });
        },
        addInternalOrder(internal_order){
            this.isLoading = true;
            this.errors = [];
            var company_id = internal_order.tsr.company_id;
            var user_id = internal_order.tsr.user_id;
            axios.post('/internal-order',{
                'user_id': user_id,
                'charge_type': internal_order.expense_type, 
                'internal_order': internal_order.internal_order,
                'company_id': company_id,
                'amount': internal_order.amount,
                'validity_date': internal_order.validity_date
            })
            .then(response => {
                $('#addModal').modal('hide');
                alert('Internal Order successfully added');
                this.internal_orders.unshift(response.data);
                this.isLoading = false;
                this.internal_order=[];
            })
            .catch(error => {
                this.errors = error.response.data.errors;
                this.isLoading = false;
            })
        },
        updateInternalOrder(internal_order_copied,internal_order_copied_charge_type,default_amount){
            this.isLoading = true;
            this.errors = [];
            var default_expense_type = [];
            default_expense_type = this.internal_order_copied.charge_type.expense_charge_type.expense_type.id;
            var index = this.internal_orders.findIndex(item => item.id == internal_order_copied.id);
            axios.post(`/internal-order/${internal_order_copied.id}`,{
                user_id: internal_order_copied.user_id,
                charge_type: internal_order_copied_charge_type, 
                internal_order: internal_order_copied.internal_order,
                sap_server: internal_order_copied.sap_server,
                amount : default_amount.amount,
                validity_date : default_amount.validity_date,
                default_expense_type: default_expense_type,
                _method: 'PATCH'
            })
            .then(response => {
                this.isLoading = false;
                $('#editModal').modal('hide');
                alert('Internal Order successfully updated');
                this.internal_orders.splice(index,1,response.data);
            })
            .catch(error => {
                this.errors = error.response.data.errors;
                this.isLoading = false;
            })
        },
        deleteInternalOrder(){
            this.isLoading = true;
            var index = this.internal_orders.findIndex(item => item.id == this.internal_order_id);
            axios.delete(`/internal-order/${this.internal_order_id}`)
            .then(response => {
                this.isLoading = false;
                $('#deleteModal').modal('hide');
                alert('Internal Order successfully deleted');
                this.internal_orders.splice(index,1);
            })
            .catch(error => {
                this.errors = error.response.data.errors;
                this.isLoading = false;
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
        filteredInternalOrders(){
            let self = this;
            return self.internal_orders.filter(internal_order => {
                return internal_order.user.name.toLowerCase().includes(this.keywords.toLowerCase())
            });
        },
        totalPages() {
            return Math.ceil(this.filteredInternalOrders.length / this.itemsPerPage)
        },
        filteredQueues() {
            var index = this.currentPage * this.itemsPerPage;
            var queues_array = this.filteredInternalOrders.slice(index, index + this.itemsPerPage);

            if(this.currentPage >= this.totalPages) {
                this.currentPage = this.totalPages - 1
            }

            if(this.currentPage == -1) {
                this.currentPage = 0;
            }

            return queues_array;
        }
    }
    
}
</script>
