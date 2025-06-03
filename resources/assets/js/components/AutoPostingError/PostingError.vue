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
                                    <h3 class="mb-0">Posting Errors</h3>
                                </div>
                                <div class="col text-right">

                                    <!-- <download-excel
                                        :data   = "customers"
                                        :fields = "json_fields"
                                        class   = "btn btn-sm btn-default"
                                        name    = "Customers.xls">
                                            Export to excel
                                    </download-excel> -->

                                </div>
                            </div>
                        </div>
                        <!--Search Filter-->
                        <!-- <div class="mb-3">
                            <div class="row ml-2">
                                <div class="col-md-3 float-left">
                                    <div class="form-group">
                                        <label for="name" class="form-control-label">Search</label> 
                                        <input type="text" class="form-control" placeholder="Search" v-model="keywords" id="name">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-sm btn-primary" @click="fetchFilterCustomers()"> Apply Filter</button>
                                </div>
                            </div>
                        </div> -->

                        <!--Table-->
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Username</th>
                                    <th scope="col">Return Message</th>
                                    <th scope="col">Created at</th>
                                    <th scope="col">Updated at</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Company Name</th>
                                    <th scope="col">Cover Week</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in filteredQueues">
                                        <td>{{ item.id }}</td>
                                        <td>{{ item.username }}</td>
                                        <td>{{ item.return_message }}</td>
                                        <td>{{ item.creation_date }}</td>
                                        <td>{{ item.update_date }}</td>
                                        <td>{{ item.email }}</td>
                                        <td>{{ item.company_name }}</td>
                                        <td>{{ item.cover_week }}</td>
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
    </div>
</template>
<script>
import JsonExcel from 'vue-json-excel';

export default {
    components: { 'downloadExcel': JsonExcel },
    data(){
        return{
            filterVerified : '',
            verifiedStatuses : ['Verified' , 'All'],
            postingErrors: [],
            errors: [],
            keywords: '',
            currentPage: 0,
            itemsPerPage: 10,
            // json_fields: {
            //     'CUSTOMER NAME': 'name',
            // }
        }
    },
    created(){
        axios.get('/posting-error/all')
        .then(response => {
            this.postingErrors = response.data;
        })
        .catch(error => {
            this.errors = this.errors.response.data.errors;
        });
    },
    methods:{
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
        filteredList(){
            let self = this;
            return self.postingErrors.filter(e => {
                if(e){
                    // if(customer.name && customer.customer_code){
                    //     return customer.name.toLowerCase().includes(this.keywords.toLowerCase()) || customer.customer_code.toLowerCase().includes(this.keywords.toLowerCase())
                    // }else if(customer.name){
                    //     return customer.name.toLowerCase().includes(this.keywords.toLowerCase());
                    // }else if(customer.customer_code){
                    //     return customer.customer_code.toLowerCase().includes(this.keywords.toLowerCase());
                    // }   
                    return true;
                }
                
            });
        },
        totalPages() {
            return Math.ceil(this.filteredList.length / this.itemsPerPage);
        },
        filteredQueues() {
            var index = this.currentPage * this.itemsPerPage;
            var queues_array = this.filteredList.slice(index, index + this.itemsPerPage);

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

