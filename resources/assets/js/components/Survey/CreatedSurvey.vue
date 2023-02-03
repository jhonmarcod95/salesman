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
                                    <h3 class="mb-0">Created Survey</h3>
                                </div>
                                 <div class="col-4 text-right">
                                    <a class="btn btn-outline-primary mb-2" data-toggle="modal" data-target="#questionaireModal">New</a>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="row pl-2 pr-2">

                                <div class="col-md-3" v-if="userRole == 1 || userRole == 2 || userRole == 10 || userRole == 13">
                                    <div class="form-group">
                                        <label class="form-control-label" for="role">Company</label>
                                        <select class="form-control" v-model="company">
                                            <option v-for="(company,c) in companies" v-bind:key="c" :value="company.id"> {{ company.name }}</option>
                                        </select>
                                        <span class="text-danger" v-if="errors.company  ">{{ errors.company[0] }}</span>
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
                                    <div class="form-group">
                                         <label for="end_date" class="form-control-label">&nbsp;</label>
                                         <button type="button" class="btn btn-primary btn-lg btn-block" :class="{ ' disabled' : loading === true }" :disabled="loading === true"  @click="fetchQuestionnaire">Filter</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                    <th scope="col" style="width:10px"></th>
                                    <th scope="col">Company</th>
                                    <th scope="col">Header</th>
                                    <th scope="col">Question</th>
                                    <th scope="col">Status</th>
                                </thead>
                                <tbody v-for="(survey,i) in filteredSurveys" :key="i" class="list">
                                    <td class="text-right">
                                        <div class="dropdown">
                                            <a class="btn btn-sm btn-icon-only text-light" href="#" role="button"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                <a class="dropdown-item" href="javascript:void(0)"  data-toggle="modal" data-target="#questionaireEditModal" @click="generatEdit(survey.id)">Edit</a>
                                                <a class="dropdown-item" href="javascript:void(0)"  data-toggle="modal" data-target="#questionaireDeleteModal" @click="generatEdit(survey.id)">Delete</a>
                                                <!-- <a v-if="schedule.attendances && schedule.attendances.sign_out !== null" class="dropdown-item" href="javascript:void(0)"  data-toggle="modal" data-target="#photoModal" @click="getImage(schedule)">Sign out Photo</a> -->
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        {{ survey.company.name }}
                                    </td>
                                    <td>
                                        {{ survey.header }}
                                    </td>
                                    <td>
                                        <div v-for="(quest,q) in survey.survey_questionnaires" :key="q">
                                            {{ quest.question }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="font-weight-bold text-danger" v-if="survey.survey_questionnaires[0].status == 0">
                                            Inactive
                                        </span>
                                        <span class="font-weight-bold text-green" v-if="survey.survey_questionnaires[0].status == 1">
                                            Active
                                        </span>
                                    </td>
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

        <!-- Questionaire Create Modal -->
        <div class="modal fade" id="questionaireModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <span class="closed" data-dismiss="modal" id="closedQuestionnaireModal">&times;</span>
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xl-12">
                                <h3>Create Questionnaire/Survey</h3>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="form-group">
                                    <label class="form-control-label" for="role">Company</label>
                                    <select class="form-control" v-model="questionaire_company">
                                        <option v-for="(company,c) in companies" v-bind:key="c" :value="company.id"> {{ company.name }}</option>
                                    </select>
                                    <span class="text-danger" v-if="errors.company  ">{{ errors.company[0] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row" v-if="questionaire_company != ''">
                            <div class="col-xl-12">
                                <label class="form-control-label" for="header">Header</label>
                                <input type="text" class="form-control" placeholder="Header" v-model="questionaire_header">
                            </div>
                        </div>
                        <div v-if="questionaire_company != ''">
                            <div class="row mt-3">
                                <div class="col-xl-12">
                                    <label for="item">Question/Survey</label>
                                    <textarea name="" rows="10" cols="5" style="height: 40px" class="form-control" v-model="questionaire_form[0].quest"></textarea>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-xl-12">
                                    <label for="item">Question/Survey</label>
                                    <textarea name="" rows="10" style="height: 40px" class="form-control" v-model="questionaire_form[1].quest"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3 text-right">
                            <div class="col-xl-12">
                                <button class="btn btn-primary" v-if="questionaire_company != ''" @click="postQuestionnaire">Save</button>
                                <button class="btn btn-danger" data-dismiss="modal" @click="cancelQuestionnaire">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Questionaire Edit Modal -->
        <div class="modal fade" id="questionaireEditModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <span class="closed" data-dismiss="modal" id="closedEditQuestionnaireModal">&times;</span>
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xl-12">
                                <h3>Edit Questionnaire/Survey</h3>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-xl-12">
                                <div class="form-group">
                                    <label class="form-control-label" for="role">Company</label>
                                    <select class="form-control" v-model="questionaire_company">
                                        <option v-for="(company,c) in companies" v-bind:key="c" :value="company.id"> {{ company.name }}</option>
                                    </select>
                                    <span class="text-danger" v-if="errors.company  ">{{ errors.company[0] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row" v-if="questionaire_company != ''">
                            <div class="col-xl-12">
                                <label class="form-control-label" for="header">Header</label>
                                <input type="text" class="form-control" placeholder="Header" v-model="questionaire_header">
                            </div>
                        </div>
                        <div v-if="questionaire_company != ''">
                            <div class="row mt-3">
                                <div class="col-xl-12">
                                    <label for="item">Question/Survey</label>
                                    <textarea name="" rows="10" cols="5" style="height: 40px" class="form-control" v-model="questionaire_form[0].quest"></textarea>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-xl-12">
                                    <label for="item">Question/Survey</label>
                                    <textarea name="" rows="10" style="height: 40px" class="form-control" v-model="questionaire_form[1].quest"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3 text-right">
                            <div class="col-xl-12">
                                <button class="btn btn-primary" v-if="questionaire_company != ''" @click="editQuestionnaire">Save</button>
                                <button class="btn btn-danger" data-dismiss="modal" @click="cancelQuestionnaire">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Questionaire Delete Modal -->
        <div class="modal fade" id="questionaireDeleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <span class="closed" data-dismiss="modal" id="closedDeleteQuestionnaireModal">&times;</span>
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xl-12">
                                <h3>Delete Questionnaire/Survey</h3>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-xl-12">
                                <h3>Please confirm to delete questionnaire.</h3>
                            </div>
                        </div>
                        <div class="row mt-3 text-right">
                            <div class="col-xl-12">
                                <button class="btn btn-primary" @click="deleteQuestionnaire">Confirm</button>
                                <button class="btn btn-danger" data-dismiss="modal" @click="cancelQuestionnaire">Cancel</button>
                            </div>
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
            loading: false,
            errors: [],
            questionnaire: [],
            companies: [],
            company: '',
            startDate: '',
            endDate: '',
            keywords: '',
            currentPage: 0,
            itemsPerPage: 10,

            questionaire_company: '',
            questionaire_header: '',
            questionnaire_id: '',

            questionaire_form: [{
                quest: '',
            },{
                quest: '',
            },],
        }
    },
    props: ['userRole'],
    created(){
        this.fetchCompanies();
    },
    methods:{
        moment,
        fetchCompanies(){
            axios.get('/companies-all')
            .then(response => {
                this.companies = response.data;
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            })
        },

        fetchQuestionnaire(){
            this.loading = true
            axios.post('/surveys/fetch', {
                startDate: this.startDate,
                endDate: this.endDate,
                company: this.company
            })
            .then(response => {
                console.log('check result: ', response.status)
                if(response.status === 200) {
                    this.questionnaire = response.data;
                    this.errors = [];
                    this.loading = false
                }
            })
            .catch(error => {
                this.loading = false
                console.log(error.response.data.errors);
                this.errors = error.response.data.errors;
            })
        },

        postQuestionnaire(){
            this.loading = true;
            axios.post('/api/surveys/create', {
                company: this.questionaire_company,
                header: this.questionaire_header,
                questionnaire: JSON.stringify(this.questionaire_form),
            })
            .then(response => {
                console.log('check result: ', response.status)
                if(response.status === 200) {
                    this.errors = [];
                    this.loading = false;
                    this.cancelQuestionnaire();
                    document.getElementById('closedQuestionnaireModal').click();
                }
            })
            .catch(error => {
                this.loading = false
                this.errors = error.response.data.errors;
            })
        },

        editQuestionnaire(){
            this.loading = true;
            axios.post('/api/surveys/edit-questionnaire', {
                id: this.questionnaire_id,
                company: this.questionaire_company,
                header: this.questionaire_header,
                questionnaire: JSON.stringify(this.questionaire_form),
            })
            .then(response => {
                console.log('check result: ', response.status)
                if(response.status === 200) {
                    this.errors = [];
                    this.loading = false;
                    this.fetchQuestionnaire();
                    this.cancelQuestionnaire();
                    document.getElementById('closedEditQuestionnaireModal').click();
                }
            })
            .catch(error => {
                this.loading = false
                this.errors = error.response.data.errors;
            })
        },

        deleteQuestionnaire(){
            this.loading = true;
            axios.post('/api/surveys/delete-questionnaire', {
                id: this.questionnaire_id,
            })
            .then(response => {
                console.log('check result: ', response.status)
                if(response.status === 200) {
                    this.errors = [];
                    this.loading = false;
                    this.fetchQuestionnaire();
                    this.cancelQuestionnaire();
                    document.getElementById('closedDeleteQuestionnaireModal').click();
                }
            })
            .catch(error => {
                this.loading = false
                this.errors = error.response.data.errors;
            })
        },

        generatEdit(ID){
            this.questionnaire_id = ID;
            this.questionnaire.forEach(element => {
                if (parseFloat(ID) == parseFloat(element.id)) {
                    this.questionaire_company = element.company.id
                    this.questionaire_header = element.header

                    this.questionaire_form[0].quest = element.survey_questionnaires[0].question;
                    this.questionaire_form[1].quest = element.survey_questionnaires[1] == null ? '' : element.survey_questionnaires[1].question;
                }
                
            });
        },

        cancelQuestionnaire(){
            this.questionaire_company = '';
            this.questionaire_header = '';

            this.questionaire_form = [{
                quest: '',
            },{
                quest: '',
            }];
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
    },
    computed:{
        totalPages() {
            return Math.ceil(this.questionnaire.length / this.itemsPerPage)
        },
        flattenSurvey() {
            return this.questionnaire.flat();
        },
        filteredSurveys() {
            var index = this.currentPage * this.itemsPerPage;
            var queues_array = this.questionnaire.slice(index, index + this.itemsPerPage);

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