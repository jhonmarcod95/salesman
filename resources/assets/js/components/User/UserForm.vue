<template>
    <div>
        <div class="header bg-green pb-6 pt-5 pt-md-6"> </div>
        <!-- Page content -->
        <div class="container-fluid mt--7">
            <div class="row mt-5">
                <div class="col-xl-12 order-xl-1">
                    <div class="card bg-secondary shadow">
                        <div class="card-header bg-white border-0">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h3 class="mb-0">Create User</h3>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form>
                                <h6 class="heading-small text-muted mb-4">User Information</h6>
                                <div class="pl-lg-4">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-username">Name</label>
                                                <input type="text" id="input-username" class="form-control form-control-alternative" v-model="user.name">
                                                <span class="text-danger" v-if="errors.name">{{ errors.name[0] }}</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-email">Email</label>
                                                <input type="email" id="input-email" class="form-control form-control-alternative" v-model="user.email">
                                                <span class="text-danger" v-if="errors.email">{{ errors.email[0] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="input-first-name">Password</label>
                                                <input type="password" id="input-first-name" class="form-control form-control-alternative" v-model="user.password">
                                                <span class="text-danger" v-if="errors.password">{{ errors.password[0] }}</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="role">Role</label>
                                                <select class="form-control" v-model="user.role">
                                                    <option v-for="(role,r) in roles" v-bind:key="r" :value="role.id"> {{ role.name }}</option>
                                                </select>
                                                <span class="text-danger" v-if="errors.role">{{ errors.role[0] }}</span>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="role">Company</label>
                                                <multiselect
                                                        v-model="user.company"
                                                        :options="companies"
                                                        :multiple="true"
                                                        track-by="id"
                                                        :custom-label="customLabelCompany"
                                                        placeholder="Select Company"
                                                        id="selected_company"
                                                        @input="fetchDivisions(user)"
                                                    >
                                                </multiselect>
                                                <span class="text-danger" v-if="errors.company  ">{{ errors.company[0] }}</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-control-label" for="division">Division</label>
                                                
                                                <multiselect
                                                        v-model="user.division"
                                                        :options="divisions"
                                                        :multiple="true"
                                                        track-by="id"
                                                        :custom-label="customLabelCompany"
                                                        placeholder="Select Division"
                                                        id="selected_company"
                                                    >
                                                </multiselect>
                                                <span class="text-danger" v-if="errors.division  ">{{ errors.division[0] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pl-lg-4">
                                    <div class="row">
                                        <div class="text">
                                            <button @click="addUser(user)" type="button" class="btn btn-primary mt-4">Save</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<script>
import Multiselect from 'vue-multiselect';
export default {
    components:{
        Multiselect
    },
    data(){
        return{
            user:{
                name: ' ',
                email: ' ',
                password: '',
                role: '',
                company: '',
                division : ''
            },
            roles: [],
            companies: [],
            divisions: [],
            errors: []
        }
    },
    created(){
        this.fetchRoles();
        this.fetchCompanies();
    },
    methods:{
        customLabelCompany (company) {
            return `${company.name  }`
        },
        addUser(user){
            let comapanyids = [];
            let selected_company = user.company;
            if(selected_company)
            {
                selected_company.forEach((selected_company) => {
                comapanyids.push(selected_company.id);
                });
            }

            let divisionids = [];
            let selected_division = user.division;
            if(selected_division)
            {
                selected_division.forEach((selected_division) => {
                    divisionids.push(selected_division.id);
                });
            }

            axios.post('/user', {
                name: user.name,
                email: user.email,
                password: user.password,
                role: user.role,
                company: comapanyids,
                division : divisionids
            })
            .then(response => { 
                window.location.href = response.data.redirect;
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            });
        },
        fetchRoles(){
            axios.get('/roles')
            .then(response => {
                this.roles = response.data;
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            })
        },

        fetchDivisions(user){
            let comapanyids = [];
            let selected_company = user.company;
            if(selected_company)
            {
                selected_company.forEach((selected_company) => {
                comapanyids.push(selected_company.id);
                });
            }
            axios.post('/divisions',
            {
                company: comapanyids,
            })
            .then(response => {
                this.divisions = response.data;
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            })
        },

        fetchCompanies(){
            axios.get('/companies-all')
            .then(response => { 
                this.companies = response.data;
            })
            .catch(error => { 
                this.errors = error.response.data.errors;
            })
        }
    }
}
</script>
