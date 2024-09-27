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
                                
                                <h3 class="mb-3 text-center">Terms of Use</h3>

                                <div v-if="!atd_data.atd_accepted">
                                    <p>
                                       By using the Sales Force Application (“the SFA”), you hereby acknowledge 
                                       that the app is the sole and exclusive property of the 
                                       La Filipina Uy Gongco Group of Companies (the “La Filipina Group”) and that the access 
                                       and use granted to you does not grant you any right, title or interest therein. 
                                    </p>
                                    
                                    <p>You agree not to:</p>
                                    <div class="d-flex">
                                        <p class="ml-5 mr-3">1.</p>
                                        <p>Copy the SFA;</p>
                                    </div>
                                    <div class="d-flex">
                                        <p class="ml-5 mr-3">2.</p>
                                        <p>Modify, translate, adapt or otherwise create derivative works or improvements, whether or not patentable, of the SFA;</p>
                                    </div>
                                    <div class="d-flex">
                                        <p class="ml-5 mr-3">3.</p>
                                        <p>Reverse engineer, disassemble, decompile, decode or otherwise attempt to derive or gain access to the source code of the SFA or any part thereof; </p>
                                    </div>
                                    <div class="d-flex">
                                        <p class="ml-5 mr-3">4.</p>
                                        <p>Remove, delete, alter or obscure any trademarks or any copyright, trademark, patent or other intellectual property or proprietary rights notices from the SFA, including any copy thereof; </p>
                                    </div>
                                    <div class="d-flex">
                                        <p class="ml-5 mr-3">5.</p>
                                        <p>Rent, lease, lend, sell, sublicense, assign, distribute, publish, share, transfer or otherwise make available the SFA or any features or functionality of the SFA to any third party for any reason, including by making the SFA available on a network where it is capable of being accessed by more than one device at any time; or</p>
                                    </div>
                                    <div class="d-flex">
                                        <p class="ml-5 mr-3">6.</p>
                                        <p>Remove, disable, circumvent or otherwise create or implement any workaround to any copy protection, rights management or security features in or protecting the SFA.</p>
                                    </div>
                                
                                    <p>You further agree that your right to access and use the SFA is conditioned on your continued employment in any of the member companies of the La Filipina Group (the “Employer”). In the event that you are separated from the Employer, your access to the SFA shall likewise be terminated, without prejudice to the rights of the Employer that may have accrued prior to such termination of employment or access to the SFA, whichever comes later.  </p>
                                    <p>The La Filipina Group may from time to time, at its sole discretion, develop and provide updates to the SFA, which may include upgrades, bug fixes, patches and other error corrections and/or new features (collectively, the “Updates”). Updates may also modify or delete in their entirety certain features or functionalities of the SFA. You agree that the La Filipina Group has no obligation to provide any Updates or to continue to provide or enable any particular features or functionalities. Based on the settings of your company-issued smartphone, when you are connected to the internet, either: (i) the SFA will automatically download and install all available Updates; or (ii) you may receive a notice or be prompted to download and install available Updates. You shall promptly download and install all Updates and acknowledge and agree that the SFA or portions thereof may not properly operate should you fail to do so. You further agree that all Updates will be deemed part of the SFA and be subject to all terms and conditions of this Terms of Use. </p>
                                    <p>Finally, you agree that only legitimate and authentic receipts or invoices shall be uploaded to the SFA to support your requests for reimbursement from the Employer. Any discrepancy between the uploaded receipts or invoices and their original hard copies and/or any other irregularities in the submitted receipts or invoices shall be a ground for the Employer to disallow any reimbursement. Knowingly submitting a falsified, forged, bogus or simulated receipts or invoices is a ground for disciplinary action, among others. If reimbursement has already been paid, the Employer shall have the right to recover the same by deducting the amount of the disallowed reimbursement from any wages and/or other receivables of the User from the Employer. <strong>Your agreement to this Terms of Use shall serve as an authority to deduct the amount of the disallowed reimbursement from such wages and/or other receivables</strong>. In the event that your wages and/or other receivables are insufficient to cover the disallowed reimbursements, the Employer reserves the right to collect the shortfall through other means allowed by law.</p>


                                </div>
                                <div v-else class="text-center border p-3">
                                    <div class="mb-1">
                                        <i class="fa fa-check"></i>
                                        <span v-if="atd_accepted">Already</span>
                                        Accepted!
                                    </div>
                                    <div><small>{{ atd_data.atd_accepted_date | _date }}</small></div>
                                    <div><small>{{ atd_data.name | _uppercase }}</small></div>
                                </div>
                            </div>
                            <div class="card-footer text-center" v-if="!atd_data.atd_accepted">
                                <button class="btn btn-primary" @click="showAuthForm">Accept Terms of Use</button>
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