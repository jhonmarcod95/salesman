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
                                    <h3 class="mb-0">User List</h3>
                                </div>
                                <div class="col text-right">
                                    <a :href="addLink" class="btn btn-sm btn-primary">Add New</a>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="col-md-4 float-left">
                                <input type="text" class="form-control" placeholder="Search" v-model="keywords" id="name">
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <!-- <th scope="col">Role</th>
                                    <th scope="col">Company</th> -->
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(user, u) in filteredQueues" v-bind:key="u">
                                        <td class="text-right">
                                            <div class="dropdown" v-if="role === 'It'">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a class="dropdown-item" :href="editLink+user.id">Edit</a>
                                                    <a class="dropdown-item" href="javascript:;"
                                                    v-if="user.dormant_days>=90&&(user.remarks!=null||user.remarks!='')"
                                                    @click="reactivateAccount(user.id,user.name)">Reactivate</a>
                                                    <a class="dropdown-item text-danger" href="#deleteModal"
                                                    data-toggle="modal" @click="SelectUser(user.id,user.name)">Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ user.name }}</td>
                                        <td>{{ user.email }}</td>
                                        <!-- <td>{{ user.roles[0].name }}</td>
                                        <td>
                                            <span v-for="(company, c) in user.companies" :key="c">
                                                {{ company.name }} <br/>
                                            </span>
                                        </td>
                                        <td></td> -->
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
                        <input type="hidden" v-model="user_id">
                        <h5 class="modal-title" id="exampleModalLabel">Delete Schedule</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    Are you sure you want to delete {{ username }}?
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss='modal'>Close</button>
                        <button class="btn btn-warning" @click="deleteUser(user_id)">Delete</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import Swal from "sweetalert2";
export default {
    data(){
        return{
            users:[],
            errors:[],
            keywords: '',
            user_id: '',
            username: '',
            role: '',
            currentPage: 0,
            itemsPerPage: 10,
        }
    },
    created(){
        this.fectUsers();
        this.getAuthRole();
    },
    methods:{
        SelectUser(id, name){
            this.user_id = id;
            this.username = name;
        },
        getAuthRole(){
            axios.get('/auth-role')
                .then(response => {
                    this.role = response.data;
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                })
        },
        fectUsers(){
            axios.get('/users-all')
            .then(response => {
                this.users = response.data;
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            })
        },
        deleteUser(id){
            let userIndex = this.users.findIndex(item => item.id == id);
            axios.delete(`/user/${id}`)
            .then(response =>{
                $('#deleteModal').modal('hide');
                alert('User' + this.username + 'succesfully deleted.');
            })
            .catch(error => {
                this.errors = error.response.data.error;
            })
            this.users.splice(userIndex,1);
        },
        reactivateAccount(id, name){
            // console.log(id);
            Swal.fire({
            title: "Account Reactivation",
            text: "Are you sure you want to reactivate " + name + "'s account?",
            showCancelButton: true,
            cancelButtonColor: "#666666",
            confirmButtonColor: "#111166",
            confirmButtonText: "Confirm"
            }).then(response =>{
                if (response.isConfirmed)
                {
                    axios.patch(`/user/reactivate/${id}`);
                    this.accountReactivated(name);
                }
            });
        },
        accountReactivated(name) {
            Swal.fire({
            title: "Account reactivated!",
            text: "User " + name + " has been successfully reactivated.",
            confirmButtonColor: "#666666",
            confirmButtonText: "Okay"
            }).then(window.location.reload());
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
        filteredUsers(){
            let self = this;
            return self.users.filter(user => {
                return user.name.toLowerCase().includes(this.keywords.toLowerCase()) ||
                       user.email.toLowerCase().includes(this.keywords.toLowerCase())
            });
        },
        totalPages() {
            return Math.ceil(this.filteredUsers.length / this.itemsPerPage)
        },
        filteredQueues() {
            var index = this.currentPage * this.itemsPerPage;
            var queues_array = this.filteredUsers.slice(index, index + this.itemsPerPage);

            if(this.currentPage >= this.totalPages) {
                this.currentPage = this.totalPages - 1
            }

            if(this.currentPage == -1) {
                this.currentPage = 0;
            }

            return queues_array;
        },
        addLink(){
            return window.location.origin+'/users/create';
        },
        editLink(){
            return window.location.origin+'/user-edit/';
        },
    }
}
</script>
