<template>
    <modal name="form_modal"
        @opened="setDefaultData"
        :clickToClose="false"
        :shiftY=".1"
        :scrollable="true"
        height="auto">
        <div class="card card-custom">
            <div class="card-header d-flex justify-content-between">
                <div class="card-title">
                    <h4 class="card-label align-items-center">
                        <span v-if="formAction == 'add'">ADD</span>
                        <span v-else>EDIT</span>
                        VERSION RELEASE
                    </h4>
                </div>
                <!-- <span class="pt-8" @click="closeModal()"><i class="far fa-window-close"></i></span> -->
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Version (<em>year.version.fixes</em>)</label>
                            <label class="text-danger">*</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">{{ currentYear }}</span>
                                </div>
                                <input type="text" class="form-control" v-model="version" placeholder="0.0" />
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Release Date</label>
                            <label class="text-danger">*</label>
                            <input type="date" class="form-control" :max="maxDay" v-model="formData.release_date">
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="mb-2"><strong>New Feature: </strong></div>
                    <div class="input-group mb-2" v-for="(item, index) in formData.new_features" :key="index">
                        <input type="text" class="form-control" v-model="item.description" :placeholder="`New ${index+1}...`">
                        <div class="input-group-append">
                            <span class="input-group-text cursor-pointer" @click="newItem('new_features')">
                                <i class="fas fa-plus"></i>
                            </span>
                            <span class="input-group-text cursor-pointer" @click="removeItem('new_features', index)">
                                <i class="fas fa-minus"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="mb-2"><strong>Enhancement:</strong></div>
                    <div class="input-group mb-2" v-for="(item, index) in formData.updates" :key="index">
                        <input type="text" class="form-control" v-model="item.description" :placeholder="`Update ${index+1}...`">
                        <div class="input-group-append">
                            <span class="input-group-text cursor-pointer" @click="newItem('updates')">
                                <i class="fas fa-plus"></i>
                            </span>
                            <span class="input-group-text cursor-pointer" @click="removeItem('updates', index)">
                                <i class="fas fa-minus"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="mb-2"><strong>Fixes:</strong></div>
                    <div class="input-group mb-2" v-for="(item, index) in formData.fixes" :key="index">
                        <input type="text" class="form-control" v-model="item.description" :placeholder="`Fix ${index+1}...`">
                        <div class="input-group-append">
                            <span class="input-group-text cursor-pointer" @click="newItem('fixes')">
                                <i class="fas fa-plus"></i>
                            </span>
                            <span class="input-group-text cursor-pointer" @click="removeItem('fixes', index)">
                                <i class="fas fa-minus"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <!--Start: Error-->
                <error-messages :formErrors="formErrors" v-if="hasError"/>
                <!--Start: Error-->
            </div>
            <div class="card-footer text-right">
                <div class="btn btn-sm btn-secondary" @click="closeModal()">Cancel</div>

                <div class="btn btn-sm btn-primary"
                     @click="submit()">
                     <span :class="{ 'spinner spinner-primary spinner-right': requestProcessing }"></span>
                    Submit
                </div>
            </div>
        </div>
    </modal>
</template>

<script>
import moment from 'moment';
    export default {

        props: [ 
            'data', 
            'formErrors', 
            'formAction',
            'requestProcessing'
        ],

        data() {
            return {
                formData: {
                    new_features: [],
                    updates: [],
                    fixes: []
                },
                version: '',
                currentYear: moment().format('YY'),
                maxDay: moment().format('YYYY-MM-DD')
            }
        },

        methods: {
            submit() {
                // Format version
                //this.formData.version = '';
                this.formData.version = `${this.currentYear}.${this.version}`;

                this.formData.new_features = this.clearEmptyFields(this.formData.new_features);
                this.formData.updates = this.clearEmptyFields(this.formData.updates);
                this.formData.fixes = this.clearEmptyFields(this.formData.fixes);

                //Submit data
                this.$emit('submit', this.formData)
            },

            closeModal() {
                this.setDefaultData();
                this.$emit('formClose');
            },
            newItem(itemType) {
                this.formData[itemType].push({description: null});
            },
            removeItem(itemType, index) {
                if(this.formData[itemType].length == 1) { return }
                this.formData[itemType].splice(index, 1)
                console.log(this.formData[itemType])
            },
            setDefaultData() {
                if(this.formAction == 'edit') {
                    this.formData = this.data;
                    this.version = this.data.version;
                    this.currentYear = this.data.year;
                } else {
                    this.version = '',
                    this.currentYear = moment().format('YYYY')
                    this.formData = {
                        new_features: [ {description: null} ],
                        updates: [ {description: null} ],
                        fixes: [ {description: null} ]
                    }
                    this.currentYear = moment().format('YY');
                }
            },
            clearEmptyFields(data) { //Clears all additional empty fields in the data sheet
                if (data.length > 1)
                {
                    for (var i = 0; i < data.length; i++)
                    {
                        if (!data[i].description) data.splice(i,1);
                    }
                }
                return data;
            }

        },

        computed: {
            hasError() {
                return !_.isEmpty(this.formErrors)
            }
        }
    }
    
</script>
