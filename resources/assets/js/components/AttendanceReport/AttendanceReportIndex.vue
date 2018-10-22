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
                                    <h3 class="mb-0">Attendance Report</h3>
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
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="start_date" class="form-control-label">Start Date</label> 
                                        <input type="date" id="start_date" class="form-control form-control-alternative" v-model="startDate">
                                        <span class="text-danger" v-if="errors.startDate"> {{ errors.startDate[0] }} </span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="end_date" class="form-control-label">End Date</label> 
                                        <input type="date" id="end_date" class="form-control form-control-alternative" v-model="endDate">
                                        <span class="text-danger" v-if="errors.endDate"> {{ errors.endDate[0] }} </span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-sm btn-primary" @click="fetchSchedules"> Filter</button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">TSR</th>
                                    <th scope="col">Customer</th>
                                    <!-- <th scope="col">Date</th>
                                    <th scope="col">Schedule</th> -->
                                    <th scope="col">In / Out</th>
                                    <th scope="col">Rendered</th>
                                    <th scope="col">Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(schedule, s) in filteredQueues" v-bind:key="s">
                                    <td class="text-right">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <a class="dropdown-item" href="javascript:void(0)"  data-toggle="modal" data-target="#editModal">Edit</a>
                                                    <a class="dropdown-item" href="#deleteModal" data-toggle="modal" >Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ schedule.user.name }}</td>
                                        <td>
                                            Customer: {{ schedule.name }} <br>
                                            Date: {{ moment(schedule.date).format('ll') }} <br>
                                            Schedule: {{  moment(schedule.start_time, "HH:mm:ss").format("hh:mm A")  }} - {{ moment(schedule.end_time, "HH:mm:ss").format("hh:mm A") }}
                                        </td>
                                        <td>
                                            <span v-if="schedule.attendances"> IN: {{ moment(schedule.attendances.sign_in ).format('lll') }}</span> <br>
                                            <span v-if="schedule.attendances"> OUT: {{ moment(schedule.attendances.sign_out).format('lll') }} </span>
                                        </td>
                                        <td>
                                            <span v-if="schedule.attendances">
                                                {{ rendered(schedule.attendances.sign_out, schedule.attendances.sign_in) }}
                                                <!-- {{ (moment(schedule.attendances.sign_out).format('HHmm') -  moment(schedule.attendances.sign_in).format('HHmm')) }} -->
                                            </span>
                                        </td>
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
    </div>
</template>

<script>
import moment from 'moment';
export default {
    data(){
        return{
            tsrs: [],
            schedules: [],
            startDate: '',
            endDate: '',
            errors: [],
            keywords: '',
            currentPage: 0,
            itemsPerPage: 10,
        }
    },
    created(){
        // this.fetchTsrs();
    },
    methods:{
        moment,
        fetchSchedules(){
            axios.post('/attendance-report-bydate', {
                startDate: this.startDate,
                endDate: this.endDate
            })
            .then(response => {
                this.schedules = response.data; 
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            })
        },
        // fetchTsrs(){
        //     axios.get('/attendance-report-all')
        //     .then(response=>{
        //         this.tsrs = response.data;
        //     })
        //     .catch(error =>{
        //         this.errors = error.response.data.errors;
        //     })
        // },
        rendered(endTime, startTime){
            // let total =hours +" hours " + minutes + " minutes";>
            // let diff = moment(endTime).format('HHmm') -  moment(startTime).format('HHmm');

            console.log(moment(endTime).format("HH:mm"));
            // return total;
                                            
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
        filteredSchedules(){
            let self = this;
            return self.schedules.filter(schedule => {
                return schedule.name.toLowerCase().includes(this.keywords.toLowerCase())
            });
        },
        totalPages() {
            return Math.ceil(this.filteredSchedules.length / this.itemsPerPage)
        },
        filteredQueues() {
            var index = this.currentPage * this.itemsPerPage;
            var queues_array = this.filteredSchedules.slice(index, index + this.itemsPerPage);

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

<style>

</style>
