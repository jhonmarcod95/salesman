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
                                    <h3 class="mb-0">Account List</h3>
                                </div>`
                                <div class="col text-right">
                                    <a class="btn btn-sm btn-primary" data-toggle="modal" href="#addModal">Add New</a>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="col-md-4 float-left">
                                <input type="text" class="form-control" placeholder="Search" v-model="keywords" id="search">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">SAP ID</th>
                                    <th scope="col">SAP server</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(sap_user, s) in filteredQueues" v-bind:key="s">
                                        <td class="text-right">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a class="dropdown-item" @click="copyObject(sap_user)" href="#editModal" data-toggle="modal">Edit</a>
                                                    <a class="dropdown-item" href="#deleteModal" data-toggle="modal" @click="getSapUserId(sap_user.id)">Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ sap_user.sap_id }}</td>
                                        <td>{{ sap_user.sap_server}}</td>
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
        <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <input type="hidden" v-model="user_id"> -->
                        <h5 class="modal-title" id="exampleModalLabel">Edit SAP account</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success" v-if="sap_user_added">
                            <strong>Success!</strong> SAP user succesfully added
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name" class="form-control-label">SAP ID</label> 
                                    <input type="text" class="form-control" v-model="sap_user.sap_id" id="sap_id">
                                    <span class="text-danger" v-if="errors.sap_id">{{ errors.sap_id[0] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name" class="form-control-label">SAP PASSWORD</label> 
                                    <input type="password" class="form-control" v-model="sap_user.sap_password" id="sap_password">
                                     <span class="text-danger" v-if="errors.sap_password">{{ errors.sap_password[0] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name" class="form-control-label">SAP SERVER</label>
                                    <select class="form-control" v-model="sap_user.sap_server" id="sap_server">
                                       <option v-for="(sap_server,s) in sap_servers" v-bind:key="s" :value="sap_server.sap_server"> {{ sap_server.sap_server }}</option>
                                    </select>  
                                    <span class="text-danger" v-if="errors.sap_server">{{ errors.sap_server[0] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss='modal'>Close</button>
                        <button class="btn btn-primary" @click="addSapUser(sap_user)">Save</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <input type="hidden" v-model="user_id"> -->
                        <h5 class="modal-title" id="exampleModalLabel">Edit SAP account</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-success" v-if="sap_user_updated">
                            <strong>Success!</strong> SAP user succesfully updated
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name" class="form-control-label">SAP ID</label> 
                                    <input type="text" class="form-control" v-model="sap_user_copied.sap_id" id="sap_id">
                                    <span class="text-danger" v-if="errors.sap_id">{{ errors.sap_id[0] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name" class="form-control-label">SAP PASSWORD</label> 
                                    <input type="password" class="form-control" v-model="sap_user_copied.sap_password" id="sap_password">
                                     <span class="text-danger" v-if="errors.sap_password">{{ errors.sap_password[0] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name" class="form-control-label">SAP SERVER</label>
                                    <select class="form-control" v-model="sap_user_copied.sap_server" id="sap_server">
                                       <option v-for="(sap_server,s) in sap_servers" v-bind:key="s" :value="sap_server.sap_server"> {{ sap_server.sap_server }}</option>
                                    </select>  
                                    <span class="text-danger" v-if="errors.sap_server">{{ errors.sap_server[0] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss='modal'>Close</button>
                        <button class="btn btn-primary" @click="updateSapUser(sap_user_copied)">Save</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Delete SAP account</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    Are you sure you want to delete this SAP user account?
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss='modal'>Close</button>
                        <button class="btn btn-warning" @click="deleteSapUser()">Delete</button>
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
                sap_users:[],
                sap_user:[],
                sap_user_copied: [],
                sap_servers: [],
                errors: [],
                sap_user_idsap_user_id: '',
                keywords: '',
                sap_user_added: false,
                sap_user_updated: false,
                currentPage: 0, 
                itemsPerPage: 10,
            }
        },
        created(){
            this.fectSapUsers();
            this.fetchSapServers();
        },
        methods:{
            getSapUserId(id){
                this.sap_user_id = id
            },
            copyObject(sap_user){
                this.sap_user_copied = Object.assign({}, sap_user);
            },
            resetData(){
                this.sap_user = []
            },
            fectSapUsers(){
                axios.get('/sap/accounts')
                .then(response => {
                    this.sap_users = response.data;
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                });
            },
            fetchSapServers(){
                axios.get('/sap/server')
                .then(response => {
                    this.sap_servers = response.data;
                })
                .catch(error => {
                    this.errors = error.response.data.errors
                })
            },
            addSapUser(sap_user){
                axios.post('/sap/account',{
                    'sap_id' : sap_user.sap_id,
                    'sap_password' : sap_user.sap_password,
                    'sap_server' : sap_user.sap_server,
                })
                .then(response => {
                    this.sap_users.unshift(response.data);
                    this.resetData();
                    this.sap_user_added = true;
                })
                .catch(error => { 
                    this.errors = error.response.data.errors;
                    this.sap_user_added = false;
                })
            },
            updateSapUser(sap_user_copied){
                var index = this.sap_users.findIndex(item => item.id == sap_user_copied.id);
                axios.post(`/sap/account/${sap_user_copied.id}`,{
                    'sap_id' : sap_user_copied.sap_id,
                    'sap_password' : sap_user_copied.sap_password,
                    'sap_server' : sap_user_copied.sap_server,
                    '_method': 'PATCH'
                })
                .then(response => {
                    this.sap_user_updated = true;
                    this.sap_users.splice(index,1,response.data);
                }) 
                .catch(error => {
                    this.sap_user_updated = false;
                    this.errors = error.response.data.errors;
                })
            },
            deleteSapUser(){
                var index = this.sap_users.findIndex(item => item.id == this.sap_user_id);
                axios.delete(`/sap/account/${this.sap_user_id}`)
                .then(response => {
                    $('#deleteModal').modal('hide');
                    alert('SAP user account successfully deleted');
                    this.sap_users.splice(index,1);
                })
                .catch(error => {
                    this.error = error.response.data.errors;
                });
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
            filteredSapUsers(){
                let self = this;
                return self.sap_users.filter(sap_user => {
                    return sap_user.sap_id.toLowerCase().includes(this.keywords.toLowerCase())
                });
            },
            totalPages() {
                return Math.ceil(this.filteredSapUsers.length / this.itemsPerPage)
            },
            filteredQueues() {
                var index = this.currentPage * this.itemsPerPage;
                var queues_array = this.filteredSapUsers.slice(index, index + this.itemsPerPage);

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
