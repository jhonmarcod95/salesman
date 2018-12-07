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
                                    <h3 class="mb-0">Change Password</h3>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form>
                                <h6 class="heading-small text-muted mb-4">User Information</h6>
                                <div class="pl-lg-4">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label class="form-control-label" for="new-password">New Password</label>
                                                <input type="password" id="new-password" class="form-control form-control-alternative" v-model="user.new_password">
                                                <span class="text-danger" v-if="errors.new_password">{{ errors.new_password[0] }}</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label class="form-control-label" for="confirm-password">Confirm New Password</label>
                                                <input type="password" id="confirm-password" class="form-control form-control-alternative" v-model="user.new_password_confirmation">
                                                <span class="text-danger" v-if="errors.new_password_confirmation">{{ errors.new_password_confirmation[0] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pl-lg-4">
                                    <div class="row">
                                        <div class="text">
                                           <button @click="changePassword(user)" type="button" class="btn btn-primary mt-4" >Save</button>
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

<script>
export default {
    props: ['userId'],
    data(){
        return{
            user: {
                new_password: '',
                new_password_confirmation:'',
            },
            errors: []
        }
    },
    methods:{
        changePassword(user){
            axios.post('/change-password', {
                user_id: this.userId,
                new_password: user.new_password,
                new_password_confirmation: user.new_password_confirmation
            })
            .then(response => {
                this.user.new_password = "";
                this.user.new_password_confirmation= "";
                this.errors = [];
                alert('Password successfully changed');
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            })
        }
    }   
}
</script>

