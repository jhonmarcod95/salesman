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
                                    <h3 class="mb-0">Change Schedule</h3>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="row ml-2">
                                <div class="col-md-4 float-left">
                                    <div class="form-group">
                                        <label for="name" class="form-control-label">Search</label> 
                                        <input type="text" class="form-control" placeholder="Search" v-model="keywords" id="name">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-control-label" for="status">Status</label>
                                        <select class="form-control" v-model="request_status">
                                            <option value="">All</option>
                                            <option value="0">Pending</option>
                                            <option value="1">Approved</option>
                                            <option value="2">Disapproved</option>
                                        </select>
                                        <span class="text-danger" v-if="errors.company  ">{{ errors.company[0] }}</span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="start_date" class="form-control-label">Start Date</label> 
                                        <input type="date" id="start_date" class="form-control form-control-alternative" v-model="startDate">
                                        <span class="text-danger" v-if="errors.startDate"> {{ errors.startDate[0] }} </span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="end_date" class="form-control-label">End Date</label> 
                                        <input type="date" id="end_date" class="form-control form-control-alternative" v-model="endDate">
                                        <span class="text-danger" v-if="errors.endDate"> {{ errors.endDate[0] }} </span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-sm btn-primary" @click="fetchRequests"> Filter</button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">TSR</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Code</th>
                                    <th scope="col">Customer</th>
                                    <th scope="col">Address</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Start time</th>
                                    <th scope="col">End time</th>
                                    <th scope="col">Status</th>
                                </tr>
                                </thead>
                                <tbody v-if="requests.length">
                                    <tr v-for="(requests, r) in filteredQueues" v-bind:key="r">
                                        <td class="text-right">
                                            <div class="dropdown" v-if="requests.isApproved == 0 && moment(requests.date).isSameOrAfter(moment(), 'day')">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a class="dropdown-item" href="#approveModal" data-toggle="modal" @click="getRequestId(requests)">Approve</a>
                                                    <a class="dropdown-item" href="#disapproveModal" data-toggle="modal" @click="getRequestId(requests)">Disapprove</a>
                                                </div>
                                            </div>
                                            <div v-else></div>
                                        </td>
                                        <td>{{ requests.user.name }}</td>
                                        <td>{{ requests.type }}</td>
                                        <td>{{ requests.code }}</td>
                                        <td>{{ requests.name }}</td>
                                        <td>{{ requests.address }}</td>
                                        <td>{{ requests.date }}</td>
                                        <td>{{ requests.start_time }}</td>
                                        <td>{{ requests.end_time }}</td>
                                        <td>
                                            <span v-if="requests.isApproved == 0">PENDING</span>
                                            <span class="text-green" v-else-if="requests.isApproved == 1">APPROVED</span>
                                            <span class="text-danger" v-else-if="requests.isApproved == 2">DISAPPROVED</span>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td>No data available in the table</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer py-4" v-if="requests.length">
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

            <!-- Approve Modal-->
            <div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <!-- <input type="hidden" v-model="user_id"> -->
                            <h5 class="modal-title" id="exampleModalLabel">Approve Schedule</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        Are you sure you want to approve this Schedule?
                                    </div>
                                </div>
                            </div>
                          <div class="row">
                            <div class="col-md-12">
                              <div class="form-group">
                                <input class="form-control" type="text" maxlength="255" placeholder="Input Google Map URL address" v-model="request.google_map_url">
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-dismiss='modal'>Close</button>
                            <button class="btn btn-primary" @click="approveSched(request)">Approve</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Disapprove Modal-->
            <div class="modal fade" id="disapproveModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Disapprove Schedule</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        Are you sure you want to disapprove this Schedule?
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-dismiss='modal'>Close</button>
                            <button class="btn btn-warning" @click="disApproveSched(request)">Disapprove</button>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</template>

<script>
import moment from 'moment';
export default {
    data(){
        return{
            requests: [],
            request_status: '',
            startDate: '',
            request: [],
            endDate: '',
            errors: [],
            keywords:'',
            currentPage: 0,
            itemsPerPage: 10,
        }
    },
    created(){
        // this.fetchRequests();
    },
    methods:{
        moment,
        fetchRequests(){
            this.errors = [];
            axios.post('/change-schedule-bydate', {
                request_status: this.request_status,
                startDate: this.startDate,
                endDate: this.endDate,
            })
            .then(response =>{
                this.requests = response.data;
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            })
        },
        getRequestId(request){
           return this.request = request;
        },
        approveSched(request){

            axios.post('/schedules/store', {
                id: request.id,
                user_id: request.user_id,
                type: request.type,
                start_date: request.date,
                end_date: request.date,
                radius: '0.5',
                start_time: request.start_time,
                end_time: request.end_time,
                customer_codes: [request.code],
                name: request.name,
                address: request.google_map_url == '' ? request.address : request.google_map_url,
            })
            .then(response => {
                var index = this.requests.findIndex(item => item.id == this.request.id);
                this.requests[index].isApproved = 1;
                $('#approveModal').modal('hide');
                alert('Schedule approved succesfully');
            })
            .catch(error => {
                this.errors = error.response.data.errors;
                alert('Google Map URL address cannot be recognized');
            })
        },
        disApproveSched(request){
            axios.post('/change-schedule-disapproved', { 
                id: request.id
            })
            .then(response => { 
                var index = this.requests.findIndex(item => item.id == this.request.id);
                this.requests[index].isApproved = 2;
                $('#disapproveModal').modal('hide');
                alert('Schedule disapproved succesfully');
            })
            .catch(error => { 
                this.errors = error.response.errors.data;
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
        filteredRequest(){
            let self = this;
            return self.requests.filter(request => {
                return request.user.name.toLowerCase().includes(this.keywords.toLowerCase())
            });
        },
        totalPages() {
            return Math.ceil(this.filteredRequest.length / this.itemsPerPage)
        },
        filteredQueues() {
            var index = this.currentPage * this.itemsPerPage;
            var queues_array = this.filteredRequest.slice(index, index + this.itemsPerPage);

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
