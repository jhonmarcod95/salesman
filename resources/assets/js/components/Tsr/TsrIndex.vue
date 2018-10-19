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
                                    <h3 class="mb-0">TSR List</h3>
                                </div>
                                <div class="col text-right">
                                    <a :href="addLink" class="btn btn-sm btn-primary">Add New</a>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="col-md-4">
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
                                    <th scope="col">Contact Number</th>
                                    <th scope="col">Birthday</th>
                                    <th scope="col">Contact Person</th>
                                    <th scope="col">Personal Email</th>
                                    <th scope="col">Plate Number</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(tsr, t) in filteredQueues" v-bind:key="t">
                                        <td class="text-right">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a class="dropdown-item" :href="editLink+tsr.id">Edit</a>
                                                    <a class="dropdown-item" data-toggle="modal" href="#changePassword" @click="getTsrId(tsr.user_id)">Change Password</a>
                                                    <a class="dropdown-item" href="#">Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                        <th scope="row">
                                            <div class="media align-items-center">
                                                <a href="#" class="avatar rounded-circle mr-3">
                                                    <img alt="Image placeholder" src="/img/theme/team-4-800x800.jpg">
                                                </a>
                                                <div class="media-body">
                                                    <span class="mb-0 text-sm">{{ tsr.first_name + ' ' + tsr.last_name }}</span>
                                                </div>
                                            </div>
                                        </th>
                                        <td> {{ tsr.email }} </td>
                                        <td> {{ tsr.contact_number }} </td>
                                        <td> {{ tsr.date_of_birth }}</td>
                                        <td> {{ tsr.contact_person }}</td>
                                        <td> {{ tsr.personal_email }}</td>
                                        <td> {{ tsr.plate_number }}</td>
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
        <!-- Change Password Modal -->
        <div  class="modal fade" id="changePassword" tabindex="-1" role="dialog" aria-labelledby="addCompanyLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="addCompanyLabel">Change Password</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="form-control-label" for="new-password">New Password</label>
                            <input type="password" id="new-password" class="form-control form-control-alternative" v-model="user.new_password">
                            <span class="text-danger" v-if="errors.new_password">{{ errors.new_password[0] }}</span>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="form-control-label" for="new-password">Confirm New Password</label>
                            <input type="password" id="new-password" class="form-control form-control-alternative" v-model="user.new_password_confirmation">
                            <span class="text-danger" v-if="errors.new_password_confirmation">{{ errors.new_password_confirmation[0] }}</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-default btn-round btn-fill" data-dismiss="modal">Close</button>
                <button @click="changePassword(user)" type="button" class="btn btn-secondary" >Save</button>
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
            tsrs: [],
            errors: [],
            keywords: '',
            user: {
                user_id: '',
                new_password: '',
                new_password_confirmation:'',
            },
            currentPage: 0,
            itemsPerPage: 10,
        }
    },
    created(){
        this.fetchTsr();
    },
    methods:{
        getTsrId(id){
            this.user.user_id = id;
        },
        fetchTsr(){
            axios.get('/tsr-all')
            .then(response => { 
                this.tsrs = response.data;
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            })
        },
        changePassword(user){
            axios.post('/change-password', {
                user_id: this.user.user_id,
                new_password: user.new_password,
                new_password_confirmation: user.new_password_confirmation
            })
            .then(response => {
                $('#changePassword').modal('hide');
                alert('Password successfully changed');
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
        filteredTsrs(){
            let self = this;
            return self.tsrs.filter(tsr => {
                return tsr.first_name.toLowerCase().includes(this.keywords.toLowerCase())
            });
        },
        totalPages() {
            return Math.ceil(this.filteredTsrs.length / this.itemsPerPage)
        },

        filteredQueues() {
            var index = this.currentPage * this.itemsPerPage;
            var queues_array = this.filteredTsrs.slice(index, index + this.itemsPerPage);

            if(this.currentPage >= this.totalPages) {
                this.currentPage = this.totalPages - 1
            }

            if(this.currentPage == -1) {
                this.currentPage = 0;
            }

            return queues_array;
        },
        addLink(){
            return window.location.origin+'/tsr/create';
        },
        editLink(){
            return window.location.origin+'/tsr-edit/';
        },   
    }
}
</script>
