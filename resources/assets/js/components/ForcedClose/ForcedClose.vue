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
                                    <h3 class="mb-0">Agents Schedules</h3>
                                </div>`
                                <!-- <div class="col text-right">
                                    <a :href="addLink" class="btn btn-sm btn-primary">Add New</a>
                                </div> -->
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="row mx-2">
                                <div class="col-md-3">
                                    <!-- <app-select :options="agents" v-model="filter_agent" :custom-label="customLabel" placeholder="Agents"/> -->
                                
                                    <multiselect
                                        v-model="filter_agent"
                                        :options="agents"
                                        :multiple="false"
                                        track-by="id"
                                        :custom-label="customLabel"
                                        placeholder="Select Agent or User"
                                        @input="fetchSchedule"
                                    />
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="date" id="start_date" class="form-control form-control-alternative" v-model="startDate" @change="fetchSchedule">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">Customer Name</th>
                                    <th scope="col">Customer Code</th>
                                    <th scope="col">Schedule</th>
                                    <th scope="col">Sign In</th>
                                    <th scope="col">Sign Out</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Current</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(schedule, u) in filteredQueues" v-bind:key="u">
                                        <td class="text-right">
                                            <a v-if="schedule.status == 2" class="btn btn-sm btn-danger" href="#forceCloseModal" data-toggle="modal" @click="getUserId(schedule.id,schedule.name,u)">Force Close</a>
                                        </td>
                                        <td>{{ schedule.name }}</td>
                                        <td>{{ schedule.code }}</td>
                                        <td>{{ schedule.date }}</td>
                                        <td>{{ schedule.schedule_attendances ? schedule.schedule_attendances.sign_in : 'No Attendance' }}</td>
                                        <td>{{ schedule.schedule_attendances ? schedule.schedule_attendances.sign_out : 'No Attendance' }}</td>
                                        <td>{{ schedule.status }}</td>
                                        <td>{{ schedule.isCurrent }}</td>
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
        <div class="modal fade" id="forceCloseModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <input type="hidden" v-model="schedule_id">
                        <h5 class="modal-title" id="exampleModalLabel">Force Close Schedule - {{ customer_name }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="new_date">Attendance Date</label>
                                    <input type="date" id="new_date" class="form-control form-control-alternative" v-model="newDate">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="new_time">Attendance Time</label>
                                    <input type="time" id="new_time" class="form-control form-control-alternative" v-model="newTime">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss='modal'>Close</button>
                        <button class="btn btn-warning" @click="forcedCloseSchedule(schedule_id)">Proceed</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<script>
import { isEmpty } from 'lodash';
import Multiselect from 'vue-multiselect';
export default {
    data(){
        return{
            users:[],
            agents:[],
            schedules:[],
            roles:[],
            errors:[],
            schedIndex: '',
            startDate: '',
            newDate: '',
            newTime: '',
            keywords: '',
            schedule_id: '',
            customer_name: '',
            role: '',
            filter_agent: '',
            filter_role: '',
            currentPage: 0,
            itemsPerPage: 10,
        }
    },
    created(){
        this.fectAgents();
        // this.setDate();
    },
    methods:{
        getUserId(id,name,index){
            this.schedule_id = id;
            this.customer_name = name;
            this.schedIndex = index;
        },
        fectAgents(){
            axios.get('/forced-closes')
            .then(response => {
                this.agents = response.data.agents;
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            })
        },
        fetchSchedule(){
            if (this.filter_agent !== '' && this.startDate) {
                axios.post('/fetch-schedules',{
                    agent: this.filter_agent,
                    date: this.startDate
                })
                .then(response => {
                    this.schedules = response.data;
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                })
            }
        },
        forcedCloseSchedule(id){
            if(confirm("Are you sure you want to force close this schedule?")) {
                axios.post('close-attendance',{
                    schedule_id: id,
                    new_date: this.newDate,
                    new_time: this.newTime
                })
                .then(response =>{
                    this.schedules.splice(this.schedIndex,1,response.data)
                    $('#forceCloseModal').modal('hide');
                    alart('User Succesfully Closed Schedule');
                })
                .catch(error => {
                    this.errors = error.response.data.error;
                })
                this.users.splice(userIndex,1);
            }
        },
        customLabel(data) {
            var companies = '';
            if(data){
                if (data.companies) {
                    data.companies.forEach((element,index) => {
                        if (index > 0) {
                            companies += ','+element.name;
                        } else {
                            companies += element.name;
                        }
                    });
                }
                return `${data.name}` + ` - ` + `${companies}`
            }else{
                return '';
            }

        },
        setDate(){
            var date = new Date(Date.now());
            // var tomorrow = new Date(new Date().setDate(new Date().getDate() + 1));
            this.startDate = this.DateFormat(date);
            // this.endDate = this.DateFormat(tomorrow);
        },
        DateFormat(d){
            return d.getFullYear() + "-" + ("0" + (d.getMonth() + 1)).slice(-2) + "-" + ("0"+(d.getDate())).slice(-2);
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
            return self.schedules.filter(schedule => {
                if(schedule.name.toLowerCase().includes(this.keywords.toLowerCase())) {
                    return schedule;
                }
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