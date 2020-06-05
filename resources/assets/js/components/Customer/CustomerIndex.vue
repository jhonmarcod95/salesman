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
                                    <h3 class="mb-0">Customer List</h3>
                                </div>
                                <div class="col text-right">
                                    <a :href="addLink" class="btn btn-sm btn-primary">Add New</a>

                                    <download-excel
                                        :data   = "customers"
                                        :fields = "json_fields"
                                        class   = "btn btn-sm btn-default"
                                        name    = "Customers.xls">
                                            Export to excel
                                    </download-excel>

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
                                    <th scope="col">Customer Code</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Street</th>
                                    <th scope="col">Town or City</th>
                                    <th scope="col">Province</th>
                                    <th scope="col">Google Map Address</th>
                                    <th scope="col">Classification</th>
                                    <th scope="col">Telephone 1</th>
                                    <th scope="col">Telephone 2</th>
                                    <th scope="col">Fax number</th>
                                    <th scope="col">Remarks</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(customer,c) in filteredQueues" v-bind:key="c">
                                        <td class="text-right">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a class="dropdown-item" :href="editLink+customer.id">Edit</a>
                                                    <a class="dropdown-item" href="#deleteModal" data-toggle="modal" @click="getCustomerId(customer.id)">Delete</a>
                                                    <a class="dropdown-item" @click="getGeocode(customer.lat,customer.lng)">View address</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ customer.customer_code }}</td>
                                        <td>{{ customer.name }}</td>
                                        <td>{{ customer.street }}</td>
                                        <td>{{ customer.town_city }}</td>
                                        <td>{{ customer.province }}</td>
                                        <td>{{ customer.google_address }}</td>
                                        <td>{{ customer.customer_classification }}</td>
                                        <td>{{ customer.telephone_1 }}</td>
                                        <td>{{ customer.telephone_2 }}</td>
                                        <td>{{ customer.fax_number }}</td>      
                                        <td>{{ customer.remarks }}</td>
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
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Delete Customer</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    Are you sure you want to delete this Customer?
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss='modal'>Close</button>
                        <button class="btn btn-warning" @click="deleteCustomer">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import JsonExcel from 'vue-json-excel'
export default {
    components: { 'downloadExcel': JsonExcel },
    data(){
        return{
            customers: [],
            customer_id: '',
            errors: [],
            keywords: '',
            currentPage: 0,
            itemsPerPage: 10,
            json_fields: {
                'CUSTOMER NAME': 'name',
                'ADDRESS': 'google_address',
                'PROVINCE' : 'province',
                'CLASSIFICATION': {
                    callback: (value) => {
                        if(value.classification == 10 || value.classification == 16){
                            return 'DIRECT';
                        }else if(value.classification == 8 || value.classification == 9){
                           return 'INDIRECT';
                        }else{
                            return '';
                        }
                    }
                },
            }
        }
    },
    created(){
        this.fetchCustomer();
    },
    methods:{
      getCustomerId(id){
          this.customer_id = id;
      },
      getGeocode(lat,lng){
        var geo_code = lat + ',' + lng;
        if(lat && lng){
            window.open('https://www.google.com/maps/place/'+ geo_code, '_blank');
        }else{
            alert('Google Map Address not available.');
        }
      },
      fetchCustomer(){
          axios.get('/customers-all')
          .then(response => {
              this.customers = response.data;
          })
          .catch(error => {
              this.errors = error.response.data.errors;
          })
      },
      deleteCustomer(){
            var index = this.customers.findIndex(item => item.id == this.customer_id);
            axios.delete(`/customers/${this.customer_id}`)
            .then(response => {
                $('#deleteModal').modal('hide');
                alert('Customer successfully deleted');
                this.customers.splice(index,1);
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
        filteredCustomers(){
            let self = this;

            return self.customers.filter(customer => {
                return customer.name.toLowerCase().includes(this.keywords.toLowerCase())
            });
        },
        totalPages() {
            return Math.ceil(this.filteredCustomers.length / this.itemsPerPage)
        },
        filteredQueues() {
            var index = this.currentPage * this.itemsPerPage;
            var queues_array = this.filteredCustomers.slice(index, index + this.itemsPerPage);

            if(this.currentPage >= this.totalPages) {
                this.currentPage = this.totalPages - 1
            }

            if(this.currentPage == -1) {
                this.currentPage = 0;
            }

            return queues_array;
        },
        addLink(){
            return window.location.origin+'/customers/create';
        },
        editLink(){
            return window.location.origin+'/customers-edit/';
        }
    }
    
}
</script>

