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
                                </div>`
                                <div class="col text-right">
                                    <a :href="addLink" class="btn btn-sm btn-primary">Add New</a>
                                </div>
                                <div class="col-md-12">
                                    <label for="name">Search</label>
                                    <input type="text" class="form-control" placeholder="Search" v-model="keywords" id="name">
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Created At</th>
                                    <th scope="col">Updated At</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(user, u) in filteredQueues" v-bind:key="u">
                                        <td class="text-right">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a class="dropdown-item" :href="editLink+user.id">Edit</a>
                                                    <a class="dropdown-item" href="#deleteModal" data-toggle="modal" @click="getUserId(user.id)">Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ user.name }}</td>
                                        <td>{{ user.email }}</td>
                                        <td></td>
                                        <td></td>
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
                        <h5 class="modal-title" id="exampleModalLabel">Update Schedule</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    Are you sure you want to delete this User?
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
export default {
    data(){
        return{
            users:[],
            errors:[],
            keywords: '',
            user_id: '',
            currentPage: 0,
            itemsPerPage: 10,
        }
    },
    created(){
        this.fectUsers();
    },
    methods:{
        getUserId(id){
            return this.user_id = id;
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
                alart('User Succesfully deleted');
            })
            .catch(error => {
                this.errors = error.response.data.error;
            })
            this.users.splice(userIndex,1);
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
