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
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">Area</th>
                                    <th scope="col">Classification</th>
                                    <th scope="col">Customer Code</th>
                                    <th scope="col">Group</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Street</th>
                                    <th scope="col">Town or City</th>
                                    <th scope="col">Province</th>
                                    <th scope="col">Telephone 1</th>
                                    <th scope="col">Telephone 2</th>
                                    <th scope="col">Fax number</th>
                                    <th scope="col">Remarks</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(customer,c) in customers" v-bind:key="c">
                                        <td>{{ customer.area }}</td>
                                        <td>{{ customer.classification }}</td>
                                        <td>{{ customer.customer }}</td>
                                        <td>{{ customer.group }}</td>
                                        <td>{{ customer.name }}</td>
                                        <td>{{ customer.street }}</td>
                                        <td>{{ customer.town_city }}</td>
                                        <td>{{ customer.province_id }}</td>
                                        <td>{{ customer.telephone_1 }}</td>
                                        <td>{{ customer.telephone_2 }}</td>
                                        <td>{{ customer.fax_number }}</td>      
                                        <td>{{ customer.remarks }}</td>
                                        <td class="text-right">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a class="dropdown-item" :href="editLink+customer.id">Edit</a>
                                                    <a class="dropdown-item" href="#">Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer py-4">
                            <nav aria-label="...">
                                <ul class="pagination justify-content-end mb-0">
                                    <li class="page-item disabled">
                                        <a class="page-link" href="#" tabindex="-1">
                                            <i class="fas fa-angle-left"></i>
                                            <span class="sr-only">Previous</span>
                                        </a>
                                    </li>
                                    <li class="page-item active">
                                        <a class="page-link" href="#">1</a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">2 <span class="sr-only">(current)</span></a>
                                    </li>
                                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">
                                            <i class="fas fa-angle-right"></i>
                                            <span class="sr-only">Next</span>
                                        </a>
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
export default {
    data(){
        return{
            customers: [],
            errors: []
        }
    },
    created(){
        this.fetchCustomer();
    },
    methods:{
      fetchCustomer(){
          axios.get('/customers-all')
          .then(response => {
              this.customers = response.data;
          })
          .catch(error => {
              this.errors = error.response.data.errors;
          })
      }  
    },
    computed:{
        addLink(){
            return window.location.origin+'/customers/create';
        },
        editLink(){
            return window.location.origin+'/customers-edit/';
        }
    }
    
}
</script>

