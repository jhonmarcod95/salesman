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
                                    <h3 class="mb-0">Posting Errors</h3>
                                </div>
                                <div class="col text-right">

                                    <download-excel
                                        :data   = "postingErrors"
                                        :fields = "json_fields"
                                        class   = "btn btn-sm btn-default"
                                        name    = "PostingErrors.xls">
                                            Export to excel
                                    </download-excel>

                                </div>
                            </div>
                        </div>
                        <!--Search Filter-->
                        <div class="mb-3">
                            <div class="row ml-2 align-items-center">
                                <div class="col-md-3 float-left">
                                    <div class="form-group">
                                        <label for="name" class="form-control-label">Username</label> 
                                        <input type="text" class="form-control" placeholder="Search" v-model="keywords.username" id="name">
                                    </div>
                                </div>
                                <div class="col-md-3 float-left">
                                    <div class="form-group">
                                        <label for="start_date" class="form-control-label">Start Date</label> 
                                        <input type="date" class="form-control" v-model="keywords.start_date" id="start_date" :max="keywords.end_date">
                                    </div>
                                </div>
                                <div class="col-md-3 float-left">
                                    <div class="form-group">
                                        <label for="end_date" class="form-control-label">End Date</label> 
                                        <input type="date" class="form-control" v-model="keywords.end_date" id="end_date" :min="keywords.start_date">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <!-- <button class="btn btn-primary" @click="resetKeywords()">Reset</button> -->
                                    <button class="btn btn-success" @click="fetchList()">Search</button>
                                </div>
                            </div>
                        </div>

                        <!--Error Messages-->
                        <div class="float-left m-3">
                            <div v-for="error in errors" class="text-danger">-{{ error }}</div>
                        </div>

                        <!--Table-->
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Username</th>
                                    <th scope="col">Return Message</th>
                                    <th scope="col">Created at</th>
                                    <th scope="col">Updated at</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Company Name</th>
                                    <th scope="col">Cover Week</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="filteredQueues.length <= 0">
                                        <div class="text-muted text-center p-3">No results found</div>
                                    </tr>
                                    <tr v-else v-for="item in filteredQueues">
                                        <td>{{ item.id }}</td>
                                        <td>{{ item.username }}</td>
                                        <td>{{ item.return_message }}</td>
                                        <td>{{ item.creation_date }}</td>
                                        <td>{{ item.update_date }}</td>
                                        <td>{{ item.email }}</td>
                                        <td>{{ item.company_name }}</td>
                                        <td>{{ item.cover_week }}</td>
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
import JsonExcel from 'vue-json-excel';

export default {
    components: { 'downloadExcel': JsonExcel },
    data(){
        return{
            filterVerified : '',
            verifiedStatuses : ['Verified' , 'All'],
            postingErrors: [],
            errors: [],
            keywords: {
                username: '',
                start_date: '',
                end_date: ''
            },
            currentPage: 0,
            itemsPerPage: 10,
            json_fields: {
                'ID': 'id',
                'Username': 'username',
                'Return Message': 'return_message',
                'Created at': 'creation_date' ,
                'Updated at': 'update_date',
                'Email': 'email',
                'Company Name': 'company_name',
                'Cover Week': 'cover_week'
            }
        }
    },
    created(){
        // this.fetchList();
    },
    methods:{
        fetchList() {
            //Set date range to current date if null
            if (!this.keywords.end_date) this.keywords.end_date = new Date().toJSON().slice(0, 10);
            if (!this.keywords.start_date) this.keywords.start_date = '2018-01-01';

            axios.post('/posting-error/all', this.keywords)
            .then(response => {
                this.postingErrors = response.data;
            })
            .catch(error => {
                this.errors = this.errors.response.data.errors;
            });
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
        },
        resetKeywords() {
            this.keywords =  {
                username: '',
                start_date: '',
                end_date: ''
            };
        }
    },
    computed:{
        filteredList(){
            let list = this.postingErrors;
            
            // //filter by username
            // if (this.keywords.username) list = list.filter( e => { 
            //     return e.username.toLowerCase().includes(this.keywords.username) 
            // });
            // //filter by date range
            // if (this.keywords.start_date && this.keywords.end_date) list = list.filter ( e => {
            //     return Date.parse(e.creation_date) >= Date.parse(this.keywords.start_date) &&
            //     Date.parse(e.creation_date) <= Date.parse(this.keywords.end_date)
            // } );

            return list;
        },
        totalPages() {
            return Math.ceil(this.filteredList.length / this.itemsPerPage);
        },
        filteredQueues() {
            var index = this.currentPage * this.itemsPerPage;
            var queues_array = this.filteredList.slice(index, index + this.itemsPerPage);

            if(this.currentPage >= this.totalPages) {
                this.currentPage = this.totalPages - 1;
            }

            if(this.currentPage == -1) {
                this.currentPage = 0;
            }

            return queues_array;
        },
    }
    
}
</script>

