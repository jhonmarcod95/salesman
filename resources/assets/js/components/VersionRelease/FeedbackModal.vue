<template>
    <modal name="feedback_modal"
        @opened="setDefaultData"
        :clickToClose="false"
        :shiftY=".1"
        :scrollable="true"
        height="auto">
        <div class="card card-custom">

            <div class="card-header d-flex justify-content-between">
                <div class="card-title">
                    <h4 class="card-label align-items-center">
                        <span v-if="formAction == 'add'">SEND</span>
                        <span v-else-if="formAction == 'delete'">DELETE</span>
                         FEEDBACK</h4>
                </div>
            </div>

            <div class="card-body">
                <div class="my-1">
                    <div v-if="!authenticated" class="form-group mb-2">
                        <div><strong>Enter account details to proceed</strong></div>
                        <label>Email Address:<span class="text-danger">*</span></label>
                        <input type="text" class="form-control mb-2" v-model="email" placeholder="juan.delacruz@lafilgroup.com" />
                        <label>Password:<span class="text-danger">*</span></label>
                        <input type="password" class="form-control mb-2" v-model="password" />
                    </div>
                    <div v-if="formAction == 'add'">
                        <label>Feedback:<span class="text-danger">*</span></label>
                        <textarea class="form-control mb-1" v-model="feedback"></textarea>
                    </div>
                    <div v-else-if="formAction == 'delete'">
                        <div class="text-center">Press confirm to delete feedback.</div>
                    </div>
                </div>
                <!--Start: Error-->
                <error-messages :formErrors="formErrors" v-if="hasError"/>
                <!--Start: Error-->
            </div>

            <div class="card-footer text-right">
                <div class="btn btn-secondary" @click="closeModal()">Cancel</div>

                <div class="btn btn-primary" @click="submit()">
                     <span :class="{ 'spinner spinner-primary spinner-right': requestProcessing }"></span>
                    {{ this.formAction == 'add'? 'Submit': 'Confirm' }}
                </div>
            </div>

        </div>
    </modal>
</template>

<script>
	import Swal from 'sweetalert2';
    export default {
        props: [ 
            'versionReleaseId', 
            'authenticated',
            'formAction', //add, delete
            'feedbackId'
        ],
        data() {
            return {
                email: '',
                feedback: '',
                password: '',
                formErrors: [],
                requestProcessing: false
            }
        },
        methods: {
            submit() {
                if (this.requestProcessing) return;
                this.requestProcessing = true;
                
                if (this.formAction == 'add') {
                    let data = {
                        'version_release_id' : this.versionReleaseId,
                        'email' : this.email,
                        'feedback' : this.feedback,
                        'password' : this.password,
                        'authenticated' : this.authenticated
                    };
                    axios.post('/version-release/submit-feedback', data)
                    .then(res => {
                        this.closeModal();
                        this.requestProcessing = false;
            	  	  	Swal.fire({
            	  	  	  	title: "Feedback submitted!",
            	  	  	  	icon: "success",
            	  	  	  	confirmButtonColor: "666666",
            	  	  	  	confirmButtonText: "Close",
            	  	  	}).then((result) => {
            	  	  	    if (result.isConfirmed) window.location.reload();
            	  	  	});
                    })
			        .catch( error => {
                        this.formErrors = error.response.data.errors;
                        this.requestProcessing = false;
			        });
                }
                else if (this.formAction == 'delete') {
                    let data = {
                        'email' : this.email,
                        'password' : this.password,
                        'authenticated' : this.authenticated,
                        'feedbackId' : this.feedbackId
                    };
                    axios.post('/version-release/delete-feedback', data)
                    .then(res => {
                        this.closeModal();
                        this.requestProcessing = false;
            	  	  	Swal.fire({
            	  	  	  	title: "Feedback deleted!",
            	  	  	  	icon: "success",
            	  	  	  	confirmButtonColor: "666666",
            	  	  	  	confirmButtonText: "Close",
            	  	  	}).then((result) => {
            	  	  	    if (result.isConfirmed) window.location.reload();
            	  	  	});
                    })
			        .catch( error => {
                        this.formErrors = error.response.data.errors;
                        this.requestProcessing = false;
			        });
                }
            },
            closeModal() {
                this.$emit('fetchList');
                this.setDefaultData();
                this.$emit('formClose');
            },
            setDefaultData() {
                this.email = '';
                this.password = '';
                this.feedback = '';
                this.formErrors = [];
            }
        },
        computed: {
            hasError() {
                return !_.isEmpty(this.formErrors);
            }
        }
    }
</script>