<template>
    <div>
        <div class="header bg-green pb-6 pt-5 pt-md-6"></div>
        <div class="container-fluid mt--7">
            <!-- Table -->
            <div class="row mt-5">
                <div class="col">
                    <div class="card shadow">

                        <!-- <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="mb-0">Survey Start</h3>
                                </div>
                            </div>
                        </div> -->

                        <div v-if="loading === true" class="mb-3  p-5">

                            <div class="center-align py-3" style="display: flex; align-items: center; justify-content: center;">
                                <div>
                                <svg class="spinner" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg">
                                    <circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle>
                                </svg>
                                <h2 class="text-muted">
                                    Loading...
                                </h2>
                                </div>
                            </div>

                        </div>

                        <div v-if="hasCurrentSignIn === false &&  loading === false" class="mb-3">

                            <div class="d-flex justify-content-center">

                                <div class="row p-5">

                                    <div class="col text-center">

                                        <i class="ni ni-pin-3" style="font-size: 70px;"></i>
                                        <h1 class="text-muted">
                                            You're not currently signed in to customer
                                        </h1>
                                        <p>
                                            Please sign-in first in Salesforce Mobile App.
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div v-if="ifCustomerVisited === 1 && hasCurrentSignIn === true &&  loading === false" class="mb-3">

                            <div class="d-flex justify-content-center">

                                <div class="row p-5">

                                    <div class="col text-center">

                                        <i class="ni ni-notification-70" style="font-size: 70px;"></i>
                                        <h1 class="text-muted">
                                            You surveyed this customer today!
                                        </h1>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div v-if="ifCustomerVisited === 0 && hasCurrentSignIn === true && loading === false" class="mb-3">

                            <form-wizard @on-complete="onComplete"
                                title="Customer Satisfaction Survey"
                                subtitle="Sales personnel should currently signed-in via mobile application in the customer to start the survey"
                            >
                            <tab-content title="Current Customer">

                                <div class="d-flex justify-content-center">
                                    <div>
                                        <div class="row">
                                            <div class="col">
                                                <h2>Step 1</h2>
                                                <p>
                                                    We would like to know your feedback for the past six(6) months to help us better understanding your needs
                                                </p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label class="form-control-label" for="input-username">Current Customer</label>
                                                    <input type="text" id="input-username" disabled :value="currentCustomer.name" class="form-control form-control-lg form-control-alternative">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                            </tab-content>
                            <tab-content title="Brands / Commodity">

                                <div class="d-flex justify-content-center">
                                    <div>
                                        <div class="row">
                                            <div class="col">
                                                <h2>Step 2</h2>
                                                <p>
                                                    Please list the brand(s) of commodity you purchased from us
                                                </p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <ul class="list-group list-group-flush">
                                                    <li v-for="(brand, b) in brands" :key="b" class="list-group-item">
                                                        <div class="custom-control custom-checkbox mt-2">
                                                            <input type="checkbox" :value="brand.id" v-model="form.brands" class="custom-control-input" :id="`with_customer_sap_${brand.id}`">
                                                            <label class="custom-control-label" :for="`with_customer_sap_${brand.id}`">
                                                                {{ brand.name }}
                                                            </label>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </tab-content>
                            
                            <tab-content title="Rating">

                                <div class="d-flex justify-content-center">
                                    <div>
                                        <div class="row">
                                            <div class="col">
                                                <h2>Step 3</h2>
                                                <p>
                                                    On the scale of 1-5 (5-Excellent, 4-Very Good, 3-Good, 2-Fair and 1-Poor) provide the required information that most accurately describes your level of satisfaction to our products and services.
                                                </p>
                                            </div>
                                        </div>
                                        <div class="row mb-3 mt-3">
                                            <div class="col-lg-12">

                                                <ul v-for="(item, q) in questionnaires" :key="q" class="list-group list-group-flush">
                                                    <li v-for="(x,z) in item.questions" :key="z" class="list-group-item">
                                                        <h3>{{ x.question }}</h3>

                                                        <star-rating v-bind:max-rating="5"
                                                                    inactive-color="#CBD5E0"
                                                                    active-color="#FC8181"
                                                                    @rating-selected ="x.rating = $event"
                                                                    v-bind:star-size="75">
                                                        </star-rating>
                                                    </li>
                                                </ul>

                                                <div class="row mt-5">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <label for="admin_new_message">Remarks:</label>
                                                        <textarea class="form-control" rows="5" id="admin_new_message" v-model="form.remarks"></textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </tab-content>
                            
                            </form-wizard>

                        </div>
                        
                    </div>
                </div>
            </div>
        </div>

     
    </div>
</template>

<script>
import {FormWizard, TabContent} from 'vue-form-wizard'
import 'vue-form-wizard/dist/vue-form-wizard.min.css'
import StarRating from 'vue-star-rating'
import Swal from 'sweetalert2'

export default {

    components: {
        FormWizard,
        TabContent,
        StarRating,
    },
    
    // props: ['userRole'],
    data(){
        return{
            errors: [],
            form: {
                customer_id: null,
                ranks: [],
                brands: [],
                remarks: '',
            },
            loading: false,
            brands: [],
            questionnaires: [],
            ratingdescription: [
                {
                text: 'Poor',
                class: 'star-poor'
                },
                {
                text: 'Fair',
                class: 'star-belowAverage'
                },
                {
                text: 'Good',
                class: 'star-average'
                },
                {
                text: 'Very Good',
                class: 'star-good'
                },
                {
                text: 'Excellent',
                class: 'star-excellent'
                }
            ],
            maxstars: 5,
            starsize: 'lg',
            hasdescription: true,
            hasresults: true,
            currentSchedule: {},
            currentCustomer: {},
            ifCustomerVisited: 1,
            hasCurrentSignIn: true,
        }
    },
    created(){
        this.fetchCurrentSchedule()        
    },
    methods:{
        checkIfCustomerVisited(customer_id) {
            axios.get(`/api/surveys/customer-visited/${customer_id}`)
            .then(response => {
                this.ifCustomerVisited = response.data;
            })
        },
        fetchCurrentSchedule() {
            return new Promise((resolve,reject) => {
                this.loading = true;
                axios.get(`/api/schedules/current`)
                .then(response => {
                    this.currentSchedule = response.data
                    return response.data.code;
                })
                .then(result => {
                     return axios.get(`/api/customer/${result}`)
                    .then(response => {
                        console.log('check if customer is not empty: ', Object.keys(response.data).length)
                        console.log('check if customer  data: ', response.data);
                        if(Object.keys(response.data).length > 0) {
                            this.currentCustomer = response.data
                            this.hasCurrentSignIn = true;
                            this.loading = false
                            return this.currentCustomer.id;
                        }
                        this.hasCurrentSignIn = false
                        this.loading = false
                        return 0;
                    })
                    .then(x => {
                        this.checkIfCustomerVisited(x);
                        this.fetchBrands();
                        this.getSurveyQuestionnaires()
                    })                    
                })
            })
        },
        fetchBrands() {
            return new Promise((resolve, reject) => {
                axios.get(`/api/brands`)
                .then(response => {
                    this.brands = response.data
                });
            })
        },
        getSurveyQuestionnaires() {
            return new Promise((resolve, reject) => {
                axios.get(`/api/surveys/questionnaires`)
                .then(response => {
                    this.questionnaires = response.data
                })
            })
        },
        onComplete() {

            if(this.form.brands.length === 0) {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Please select brands/commodity from step 2',
                    icon: 'infor',
                    confirmButtonText: 'Okay'
                });
                return false;
            }

            if(this.form.remarks === null || this.form.remarks === '') {
                Swal.fire({
                    title: 'Warning!',
                    text: 'Please input remarks.',
                    icon: 'infor',
                    confirmButtonText: 'Okay'
                });
                return false;
            }

            this.form.ranks = this.questionnaires
            this.form.brands = this.form.brands.map(x => {
                return {
                    id: x
                };
            });

            return new Promise((resolve, reject) => {
                axios.post(`/api/surveys`,{
                    customer_id: this.currentCustomer.id,
                    brands: JSON.stringify(this.form.brands),
                    ranks: JSON.stringify(this.form.ranks),
                    remarks: this.form.remarks
                })
                .then(response => {
                    if(response.status === 200 || response.status === 201) {
                        // success
                        
                        Swal.fire({
                            title: 'Success!',
                            text: 'Survey saved successfully',
                            icon: 'success',
                            confirmButtonText: 'Okay'
                        })
                        .then((result) => {
                            if(result.isConfirmed) {
                                window.location.reload();
                            }
                        });
                    }
                })
                .catch(error => {
                    if(error.response.status === 422) {
                        this.errors = error.response.data.errors
                    }
                })
            })
        }
    },
   
}
</script>

<style>
    .wizard-title {
        font-size: 30px;
        font-weight: bold;
    }
    .disabled {
        cursor: not-allowed;
    }
    .modal{
        background-color: rgba(0,0,0,0.9);
    }
    /* The Close Button */
    .closed {
        position: absolute;
        top: 15px;
        right: 35px;
        color: #f1f1f1;
        font-size: 40px;
        font-weight: bold;
        transition: 0.3s;
    }
    .closed:hover,
    .closed:focus {
        color: #bbb;
        text-decoration: none;
        cursor: pointer;
    }
</style>
<style lang="scss">
 .star {
  color: red;
 }
 .star.active {
  color: red;
 }
 .list, .list.disabled {
  &:hover {
    .star {
      color: red !important;
    }
    .star.active {
      color: red;
    }
  }
}
</style>
