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
                            <div class="row ml-2">
                                <div class="col-md-3 float-left">
                                    <div class="form-group">
                                        <label for="name" class="form-control-label">Search</label> 
                                        <input type="text" class="form-control" placeholder="Search" v-model="keywords" id="name">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="start_date" class="form-control-label">Filter Verified Status</label> 
                                        <multiselect
                                            v-model="filterVerified"
                                            :options="verifiedStatuses"
                                            :multiple="false"
                                            placeholder="Verified Status"
                                            id="selected_filter_verified"
                                        >
                                        </multiselect>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-sm btn-primary" @click="fetchFilterCustomers()"> Apply Filter</button>
                                </div>
                            </div>

                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">Verified</th>
                                    <th scope="col">Customer Code</th>
                                    <th scope="col">Company Code</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Street</th>
                                    <th scope="col">Town or City</th>
                                    <th scope="col">Province</th>
                                    <th scope="col">Google Map Address</th>
                                    <th scope="col">Classification</th>
                                    <th scope="col">Telephone 1</th>
                                    <th scope="col">Telephone 2</th>
                                    <th scope="col">Fax number</th>
                                    <th scope="col">Status</th>
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
                                        <td class="text-center">
                                            <label class="container">
                                                <input type="checkbox" style="width:20px;height:20px;"  :id="customer.id" :value="customer.verified_status"  true-value="1" false-value="0" v-model="customer.verified_status" @click="changeVerifiedStatus(customer,$event)">
                                                <span class="checkmark"></span>
                                            </label>
                                        </td>
                                        <td>{{ customer.customer_code }}</td>
                                        <td>{{ customer.company_code }}</td>
                                        <td>{{ customer.name }}</td>
                                        <td>{{ customer.street }}</td>
                                        <td>{{ customer.town_city }}</td>
                                        <td>{{ customer.province }}</td>
                                        <td>{{ customer.google_address }}</td>
                                        <td>{{ customer.customer_classification }}</td>
                                        <td>{{ customer.telephone_1 }}</td>
                                        <td>{{ customer.telephone_2 }}</td>
                                        <td>{{ customer.fax_number }}</td>  
                                        <td>{{ customer.customer_status }}</td>  
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
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<script>
import JsonExcel from 'vue-json-excel'
import Multiselect from 'vue-multiselect';
import Swal from 'sweetalert2'

export default {
    components: { 'downloadExcel': JsonExcel, Multiselect },
    data(){
        return{
            filterVerified : '',
            verifiedStatuses : ['Verified' , 'All'],
            customers: [],
            customer_id: '',
            errors: [],
            keywords: '',
            currentPage: 0,
            itemsPerPage: 10,
            json_fields: {
                'CUSTOMER CODE' : 'customer_code',
                'COMPANY CODE' : 'company_code',
                'CUSTOMER NAME': 'name',
                'Telephone 1' : 'telephone_1',
                'Telephone 2' : 'telephone_2',
                'ADDRESS': 'google_address',
                'REGION CODE' : 'region_code',
                'REGION' : 'region',
                'PROVINCE' : 'province',
                'SUB REGION': {
                    callback: (value) => {
                        if(value.province == 'CALOOCAN' || value.province == 'MALABON' || value.province == 'MANDALUYONG' || value.province == 'MANILA' || value.province == 'MARIKINA' || value.province == 'METRO MANILA' || value.province == 'NAVOTAS' || value.province == 'PASIG' || value.province == 'QUEZON CITY' || value.province == 'SAN JUAN' || value.province == 'VALENZUELA'){
                            return 'NORTH';
                        }else if(value.province == 'LAS PINAS' || value.province == 'MAKATI' || value.province == 'MUNTINLUPA' || value.province == 'PARANAQUE' || value.province == 'PASAY CITY' || value.province == 'PATEROS' || value.province == 'TAGUIG'){
                           return 'SOUTH';
                        }else{
                            return '';
                        }
                    }
                },
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
                'STATUS' : {
                    callback: (value) => {
                        if(value.status == 1){
                            return 'Active';
                        }else if(value.status == 2){
                           return 'Inactive';
                        }else if(value.status == 3){
                           return 'Prospect';
                        }else if(value.status == 4){
                           return 'Closed';
                        }
                        else{
                            return '';
                        }
                    }
                },
                'REMARKS' : 'remarks',
                'STATUS' : 'customer_status',
                'Distributor' : 'distributor_name',
                'Brand Use' : 'brand_used',
                'Monthly Volume' : 'monthly_volume',
                'Date Converted' : 'date_converted',
                'CREATION DATE': 'created_at',
            }
        }
    },
    created(){
        this.fetchCustomer();
    },
    methods:{
        changeVerifiedStatus: function(customer,event) {
            let check = event.target.checked;
            let verified_status;
            let verified_status_desc;
            if(check == true){
                verified_status = 1;
                verified_status_desc = 'Verified';
            }else{
                verified_status = 0;
                verified_status_desc = 'Unverified';
            }

            var index = this.customers.findIndex(item => item.id == customer.id);
        
            axios.post('/change-verified-status/' + customer.id,{
                customer_id: customer.id,
                verified_status: verified_status,
            })
            .then(response => {
                this.customers.splice(index,1,response.data);

                Swal.fire({
                    title: 'Success!',
                    text: 'Customer: '+ customer.name +' has been successfully ' + verified_status_desc +'.',
                    icon: 'success',
                    confirmButtonText: 'Okay'
                })

            })
            .catch(error => { 
                this.errors = error.response.data.errors;
            })
        },
        fetchFilterCustomers(){
            let v = this;
            axios.post('/customers-all-filter',{
                verified_status: v.filterVerified,
            })
            .then(response => {
                this.customers = response.data;
            })
            .catch(error => { 
                this.errors = error.response.data.errors;
            })
        },
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
                if(customer){
                    if(customer.name && customer.customer_code){
                        return customer.name.toLowerCase().includes(this.keywords.toLowerCase()) || customer.customer_code.toLowerCase().includes(this.keywords.toLowerCase())
                    }else if(customer.name){
                        return customer.name.toLowerCase().includes(this.keywords.toLowerCase());
                    }else if(customer.customer_code){
                        return customer.customer_code.toLowerCase().includes(this.keywords.toLowerCase());
                    }   
                }
                
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

