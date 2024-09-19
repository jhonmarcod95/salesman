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
                            <!-- <div class="card-header">
                                
                            </div> -->
                            <div class="card-body" :class="accepted ? 'pb-5' : ''">
                                <div class="text-center mb-5">
                                    <img src="/img/brand/group-of-companies.png" class="mb-2" style="height: 80px">
                                    <h4>Salesforce App</h4>
                                </div>
                                
                                <h3 class="mb-3 text-center">Authority To Deduct</h3>

                                <div v-if="!accepted">
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
                                <div v-else class="text-center">
                                    <i class="fa fa-check"></i>
                                    Accepted!
                                </div>
                            </div>
                            <div class="card-footer text-center" v-if="!accepted">
                                <button class="btn btn-primary" @click="triggerAuthtentication">Accept</button>
                            </div>
                        </div>
                    </div>
                </div>
        </div>

        <!-- Begin: Authentication -->
        <div class="auth-container" v-if="openAuthForm">
            <div class="auth-form">
                <div class="mb-4 mt-3">
                    <strong>Authentication</strong>
                </div>
                <div class="mb-3">{{ name }}</div>
                <input type="password" class="form-control mb-4" placeholder="Password">
                <button class="btn btn-primary" @click="closeAuthForm">Authenticate</button>
            </div>
        </div>
        <!-- End: Authentication -->
    </div>
</template>
<script>
    export default {
        props: ['name','email'],
        data() {
            return {
                openAuthForm: false,
                accepted: false
            }
        },
        methods: {
            triggerAuthtentication() {
                this.openAuthForm = true;
            },
            closeAuthForm() {
                this.openAuthForm = false;
                this.accepted = true;
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
        height: 270px;
        text-align: center;
    }

    .auth-container .auth-form input { 
        text-align: center; 
    }
</style>