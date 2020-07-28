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
                                    <h3 class="mb-0">Missed Itineraries</h3>
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
                                    <button class="btn btn-sm btn-primary" @click="fetchItineraries"> Filter</button>

                                    <json-excel class = "btn btn-sm btn-success mr-5" :data= "missedItineraries" :fields = "json_fields" name= "Missed Itineraries.xls">Export to Excel</json-excel> 

                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">TSR</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Code</th>
                                    <th scope="col">Customer</th>
                                    <th scope="col">Address</th>
                                    <th scope="col">Schedule Date</th>
                                    <th scope="col">Start time</th>
                                    <th scope="col">End time</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(itinerary, r) in filteredQueues" v-bind:key="r">
                                        <td>{{ itinerary.user.name }}</td>
                                        <td>{{ itinerary.type }}</td>
                                        <td>{{ itinerary.code }}</td>
                                        <td>{{ itinerary.name }}</td>
                                        <td>{{ itinerary.address }}</td>
                                        <td>{{ itinerary.date }}</td>
                                        <td>{{ itinerary.start_time }}</td>
                                        <td>{{ itinerary.end_time }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer py-4" v-if="missedItineraries.length">
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
                missedItineraries : [],
                companies : [],
                currentPage: 0,
                itemsPerPage: 10,
                json_fields : {
                    'TSR NAME': {
                        callback: (value) => {
                           return value.user.name;
                        }
                    },
                    'TYPE': 'type',
                    'CODE': 'code',
                    'CUSTOMER': 'name',
                    'ADDRESS': 'address',
                    'SCHEDULE DATE': 'date',
                    'START TIME': 'start_time',
                    'END TIME': 'end_time',
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
            fetchItineraries() {
               this.errors = [];
                axios.post('/missed-itineraries-data', {
                    startDate: this.startDate,
                    endDate: this.endDate,
                    company: this.company,
                })
                .then(response =>{
                    this.missedItineraries = response.data;
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
        computed: {
            filteredMissedItineraries(){
                let self = this;
                return self.missedItineraries.filter(itinerary => {
                    return itinerary.user.name.toLowerCase().includes(this.keywords.toLowerCase())
                });
            },
            totalPages() {
                return Math.ceil(this.filteredMissedItineraries.length / this.itemsPerPage)
            },
            filteredQueues() {
                var index = this.currentPage * this.itemsPerPage;
                var queues_array = this.filteredMissedItineraries.slice(index, index + this.itemsPerPage);

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
