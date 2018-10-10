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
                                    <label for="name">Search by User Name</label>
                                    <input type="text" class="form-control" placeholder="Search" v-model="keywords" id="name">
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Created At</th>
                                    <th scope="col">Updated At</th>
                                    <th scope="col"></th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(user, u) in filteredUsers" v-bind:key="u">
                                        <td>{{ user.name }}</td>
                                        <td>{{ user.email }}</td>
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
            users:[],
            errors:[],
            keywords: ''
        }
    },
    created(){
        this.fectUsers();
    },
    methods:{
        fectUsers(){
            axios.get('/users-all')
            .then(response => {
                this.users = response.data;
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            })
        }
    },
    computed:{
        addLink(){
            return window.location.origin+'/users/create';
        },
        filteredUsers(){
            let self = this;
            return self.users.filter(user => {
                return user.name.toLowerCase().includes(this.keywords.toLowerCase())
            });
        },
    }
}
</script>
