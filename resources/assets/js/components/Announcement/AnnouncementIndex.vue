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
                                    <h3 class="mb-0">Announcement List</h3>
                                </div>`
                                <div class="col text-right">
                                    <a :href="addLink" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addModal">Add New</a>
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
                                    <th scope="col">ID</th>
                                    <th scope="col">User</th>
                                    <th scope="col">Message</th>
                                    <th scope="col">Created At</th>
                                    <th scope="col">Updated At</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(announcement, a) in filteredQueues" v-bind:key="a">
                                        <td class="text-right">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a class="dropdown-item" href="javascript:void(0)" @click="copyObject(announcement)" data-toggle="modal" data-target="#editModal">Edit</a>
                                                    <a class="dropdown-item" href="#deleteModal" data-toggle="modal" @click="getAnnouncementId(announcement.id)">Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ announcement.id }}</td>
                                        <td>{{ announcement.user.name }}</td>
                                        <td>{{ announcement.message }}</td>
                                        <td>{{ announcement.created_at }}</td>
                                        <td>{{ announcement.updated_at }}</td>
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
        <!-- Add Modal -->
        <div  class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addCompanyLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="addCompanyLabel">Add Announcement</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="role">Message</label>
                        <input type="text" class="form-control" placeholder="Message" v-model="announcement.message" id="announcement">
                         <span class="error" v-if="errors.message">{{ errors.message[0] }}</span>
                    </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-default btn-round btn-fill" data-dismiss="modal">Close</button>
                <button @click="addAnnouncement(announcement)" type="button" class="btn btn-secondary" >Save</button>
                </div>
            </div>
            </div>
        </div>
        <!-- Edit Modal -->
        <div  class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="addCompanyLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="addCompanyLabel">Edit Announcement</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="role">Message</label>
                        <input type="text" class="form-control" placeholder="Message" v-model="announcement_copied.message" id="annoucement">
                         <span class="error" v-if="errors.message">{{ errors.message[0] }}</span>
                    </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-default btn-round btn-fill" data-dismiss="modal">Close</button>
                <button @click="updateAnnouncement(announcement_copied)" type="button" class="btn btn-secondary" >Save</button>
                </div>
            </div>
            </div>
        </div>
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Delete Announcement</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    Are you sure you want to delete this Announcement?
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss='modal'>Close</button>
                        <button class="btn btn-warning" @click="deleteAnnouncement">Delete</button>
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
            announcements: [],
            announcement: {
                message: ''
            },
            announcement_copied:[],
            errors: [],
            keywords: '',
            currentPage: 0,
            itemsPerPage: 10,
        }
    },
    created(){
        this.fetchAnnouncements();
    },
    methods:{
        copyObject(announcement){
            this.announcement_copied = Object.assign({}, announcement)
        },
        getAnnouncementId(id){
            this.announcement_id = id;
        },
        fetchAnnouncements(){
            axios.get('/announcements-all')
            .then(response => {
                this.announcements = response.data;
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            })
        },
        addAnnouncement(announcement){
            axios.post('/announcement', {
                message: announcement.message 
            })
            .then(response => { 
                $('#addModal').modal('hide');
                alert('Announcement successfully added');
                this.announcements.unshift(response.data);
                
            })
            .catch(error => { 
                this.errors = error.response.data.errors;
            }) 
        },
        updateAnnouncement(announcement){
            var index = this.announcements.findIndex(item => item.id == announcement.id);
            axios.patch(`/announcement/${announcement.id}`,{
                message: announcement.message
            })
            .then(response => {
                $('#editModal').modal('hide');
                alert('Announcement successfully updated');
                this.announcements.splice(index,1,response.data);
            })
            .catch(error => { 
                this.errors = error.response.data.errors;
            })
        },
        deleteAnnouncement(){
            var index = this.announcements.findIndex(item => item.id == this.announcement_id);
            axios.delete(`/announcement/${this.announcement_id}`)
            .then(response => {
                $('#deleteModal').modal('hide');
                alert('Announcement successfully deleted');
                this.announcements.splice(index,1);
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
        filteredAnnouncementss(){
            let self = this;
            return self.announcements.filter(announcement => {
                return announcement.message.toLowerCase().includes(this.keywords.toLowerCase())
            });
        },
        totalPages() {
            return Math.ceil(this.filteredAnnouncementss.length / this.itemsPerPage)
        },
        filteredQueues() {
            var index = this.currentPage * this.itemsPerPage;
            var queues_array = this.filteredAnnouncementss.slice(index, index + this.itemsPerPage);

            if(this.currentPage >= this.totalPages) {
                this.currentPage = this.totalPages - 1
            }

            if(this.currentPage == -1) {
                this.currentPage = 0;
            }

            return queues_array;
        },
        addLink(){  

        },
        editLink(){

        }
    }
}
</script>