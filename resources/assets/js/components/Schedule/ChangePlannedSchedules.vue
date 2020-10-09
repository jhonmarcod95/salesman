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
                                    <h3 class="mb-0">Change Planned Schedules</h3>
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
                                        <label class="form-control-label" for="role">Company</label>
                                        <select class="form-control" v-model="company">
                                            <option v-for="(company,c) in companies" v-bind:key="c" :value="company.id"> {{ company.name }}</option>
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
                                <div class="col-md-12 text-right">
                                    <button class="btn btn-sm btn-primary" @click="fetchChangePlannedSchedules"> Filter</button>

                                    <json-excel class = "btn btn-sm btn-success mr-5" :data= "changePlannedSchedules" :fields = "json_fields" name= "Change Planned Schedules.xls">Export to Excel</json-excel> 

                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <h4 v-if="loading">Please wait while loading....</h4>
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">Change Date</th>
                                    <th scope="col">Old</th>
                                    <th scope="col">New</th>
                                    <th scope="col">Schedule</th>
                                    <th scope="col">Tsr</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, r) in filteredQueues" v-bind:key="r">
                                        <td>{{ item.created_at }}</td>
                                        <td>{{ convertJson(item.old_values) }}</td>
                                        <td>{{ convertJson(item.new_values) }}</td>
                                        <td>
                                            <span>Code: {{ item.schedule.code }} </span> <br>
                                            <span>Name: {{ item.schedule.name }} </span> <br>
                                            <span>Address: {{ item.schedule.address }} </span> <br>
                                            <!-- <span>Date: {{ item.schedule.date }} </span> -->
                                        </td>
                                        <td>
                                           <span>{{ item.schedule.user.name }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer py-4" v-if="changePlannedSchedules.length">
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
    import JsonExcel from 'vue-json-excel'
    export default {
        components: {
            JsonExcel
        },
        data() {
            return {
                keywords: '',
                startDate: '',
                endDate: '',
                company: '',
                errors: [],
                changePlannedSchedules : [],
                companies : [],
                currentPage: 0,
                itemsPerPage: 10,
                loading: false,
                json_fields : {
                    'CHANGE DATE': 'created_at',
                    'OLD': {
                        callback: (value) => {
                           return this.convertJson(value.old_values);
                        }
                    },
                    'NEW': {
                        callback: (value) => {
                           return this.convertJson(value.new_values);
                        }
                    },
                    'CODE': {
                        callback: (value) => {
                            if(value.schedule.code){
                                return value.schedule.code;
                            }else{
                                return "";
                            }
                        }
                    },
                    'SCHEDULE NAME': {
                        callback: (value) => {
                            if(value.schedule.name){
                                return value.schedule.name;
                            }else{
                                return "";
                            }
                        }
                    },
                    'SCHEDULE ADDRESS': {
                        callback: (value) => {
                            if(value.schedule.address){
                                return value.schedule.address;
                            }else{
                                return "";
                            }
                        }
                    },
                    'SCHEDULE DATE': {
                        callback: (value) => {
                            if(value.schedule.date){
                                return value.schedule.date;
                            }else{
                                return "";
                            }
                        }
                    },
                    'TSR': {
                        callback: (value) => {
                            if(value.schedule){
                                return value.schedule.user.name;
                            }else{
                                return "";
                            }
                        }
                    },
                    
                }
            }
        },
        created () {
            this.fetchCompanies();
        },
        methods: {
             fetchCompanies(){
                axios.get('/companies-all')
                .then(response => {
                    this.companies = response.data;
                })
                .catch(error => { 
                    this.errors = error.response.data.errors;
                })
            },
            fetchChangePlannedSchedules() {
               this.errors = [];
               this.loading = true;
                axios.get('/change-planned-schedules-data?company='+this.company+'&startDate='+this.startDate+'&endDate='+this.endDate)
                .then(response =>{
                    this.changePlannedSchedules = response.data;
                    this.loading = false;
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                    this.loading = false;
                })
            },
            convertJson(value){
                var obj = JSON.parse(value);
                return obj.date;
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
        computed: {
            filteredChangePlannedSchedules(){
                let self = this;
                return self.changePlannedSchedules.filter(change_schedule => {
                    return change_schedule.schedule.user.name.toLowerCase().includes(this.keywords.toLowerCase())
                });
            },
            totalPages() {
                return Math.ceil(this.filteredChangePlannedSchedules.length / this.itemsPerPage)
            },
            filteredQueues() {
                var index = this.currentPage * this.itemsPerPage;
                var queues_array = this.filteredChangePlannedSchedules.slice(index, index + this.itemsPerPage);

                if(this.currentPage >= this.totalPages) {
                    this.currentPage = this.totalPages - 1
                }

                if(this.currentPage == -1) {
                    this.currentPage = 0;
                }

                return queues_array;
            }
        },
    }
</script>
