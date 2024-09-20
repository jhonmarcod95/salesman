<template>
    <div>
        <div class="header bg-green py-5 pt-md-6 text-center">
            <!-- <img src="/img/brand/group-of-companies.png" style="height: 100px">
            <h2 class="text-white">Salesforce App</h2> -->
        </div>
        <!-- Page content -->
        <div class="container-fluid mt--6">
            <!-- Table -->
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card shadow">
                            <div class="card-body" :class="atd_data.atd_accepted ? 'pb-5' : ''">
                                <div class="text-center mb-5">
                                    <img src="/img/brand/group-of-companies.png" class="mb-2" style="height: 80px">
                                    <h4>Salesforce App</h4>
                                </div>
                                
                                <h3 class="mb-3 text-center">Authority To Deduct</h3>

                                <div v-if="!atd_data.atd_accepted">
                                    <p class="text-justify">
                                        Hello <strong>{{ name }}</strong>. Lorem ipsum dolor sit amet, <strong>La Filipina Uy Gongco Group of Companies</strong>, 
                                        sed do eiusmod tempor incididunt ut labore et dolore magna
                                        aliqua. Ut enim ad minim veniam, quis nostrud exercitation 
                                        ullamco laboris nisi ut aliquip ex ea commodo consequat. 
                                    </p>
                                    
                                    <p class="text-justify">
                                        Duis aute irure dolor in reprehenderit in voluptate velit 
                                        esse cillum dolore eu fugiat nulla pariatur. Excepteur sint 
                                        occaecat cupidatat non proident, sunt in culpa qui officia 
                                        deserunt mollit anim id est laborum.
                                    </p>
                                </div>
                                <div v-else class="text-center border p-3">
                                    <div class="mb-1">
                                        <i class="fa fa-check"></i>
                                        <span v-if="atd_accepted">Already</span>
                                        Authorized!
                                    </div>
                                    <div><small>{{ atd_data.atd_accepted_date | _date }}</small></div>
                                    <div><small>{{ atd_data.name | _uppercase }}</small></div>
                                </div>
                            </div>
                            <div class="card-footer text-center" v-if="!atd_data.atd_accepted">
                                <button class="btn btn-primary" @click="showAuthForm">Authorize</button>
                            </div>
                        </div>
                    </div>
                </div>
        </div>

        <!-- Begin: Authentication -->
        <div class="auth-container" v-if="openAuthForm">
            <div class="auth-form">
                <div class="my-3">
                    <div class="mb-2"><strong>{{ name | _uppercase }}</strong></div>
                    Please enter your Salesforce App password to authorize these changes.
                </div>

                <div class="form-group mb-0">
                    <input type="password" class="form-control" :class="{'is-invalid': hasError}" placeholder="Password" v-model="loginData.password">
                    <div class="text-danger mt-1" v-if="!isEmpty(errors)">{{ errors }}</div>
                </div>

                <div class="btn btn-primary mt-2 w-100" @click="authenticate" :disabled="authenticating">
                    Submit
                    <span v-if="authenticating">...</span>
                </div>
            </div>
        </div>
        <!-- End: Authentication -->
    </div>
</template>
<script>
    export default {
        props: ['name','email','atd_accepted','atd_accepted_date'],
        data() {
            return {
                openAuthForm: false,
                authenticating: false,
                loginData: {
                    email: this.email,
                    password: ''
                },
                errors: '',
                atd_data: {}
            }
        },
        created() {
            this.atd_data = {
                name: this.name,
                user_email: this.email,
                atd_accepted: this.atd_accepted,
                atd_accepted_date: this.atd_accepted_date
            }
        },
        methods: {
            showAuthForm() {
                this.openAuthForm = true;
            },
            authenticate() {
                this.authenticating = true,
                axios.post('/authority-to-deduct/authenticate', this.loginData)
                .then(response => {
                    this.atd_data = response.data;
                    this.authenticating = false,
                    this.openAuthForm = false;
                    this.errors = '';
                })
                .catch(error => {
                    console.log(error.response.data);
                    if(error.response.status === 401) {
						this.errors = error.response.data.message;
					} else if(error.response.status === 422) { 
                        this.errors = error.response.data.errors.password[0];
                    }
					this.authenticating = false;
                });
            }
        }, 
        computed: {
            hasError() {
                return !_.isEmpty(this.errors);
            }
        }
    }
</script>

<style scopep>
    p { text-indent: 25px; }

    .auth-container {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 100%;
        background: #00000073;
        display: flex;
        justify-content: center;
    }

    .auth-container .auth-form {
        background: #ffffff;
        position: fixed;
        margin-top: 100px;
        border-radius: 5px;
        padding: 20px;
        width: 300px;
        height: auto;
        text-align: center;
    }

    .auth-container .auth-form input { 
        text-align: center; 
    }
</style>