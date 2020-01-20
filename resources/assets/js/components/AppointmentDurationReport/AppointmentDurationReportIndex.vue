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
                                    <h3 class="mb-0">APPOINTMENT DURATION REPORT</h3>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="row col-sm-12">
                                <div class="col-md-6 float-left">
                                    <div class="form-group">
                                        <label for="customerSelect" class="form-control-label">Select User</label> 
                                        <multiselect
                                                v-model="selectedUser"
                                                :options="userOptions"
                                                :multiple="false"
                                                track-by="id"
                                                :custom-label="customLabelUser"
                                                placeholder="Select User"
                                                id="selected_user"
                                        >
                                        </multiselect>
                                        <span class="text-danger" v-if="errors.selectedUser"> {{ errors.selectedUser[0] }} </span>
                                    </div>
                                </div>
                                <div class="col-md-3 float-left">
                                    <div class="form-group">
                                        <label for="selectedDate" class="form-control-label">Select Date</label> 
                                        <input type="date" id="selectedDate" class="form-control form-control-alternative" v-model="selectedDate">
                                        <span class="text-danger" v-if="errors.selectedDate"> {{ errors.selectedDate[0] }} </span>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <button class="btn btn-sm btn-primary" @click="getUserAppointments"> Search</button>                                
                                </div>

                                
                            </div>
                            
                            <div class="row ml-3 mt-3 mb-3 pl-3">
                                <h4 class="mt-3">Legend:</h4>
                                <div class="col-sm-2">
                                    <div class="icon icon-shape bg-success text-white rounded-circle shadow" style="font-size: .8rem!important;">{{countVisited}}</div>
                                    <span class="text-sm"> Visited</span>
                                </div>
                                <div class="col-sm-2">
                                    <div class="icon icon-shape bg-danger text-white rounded-circle shadow" style="font-size: .8rem!important;">{{countNonVisited}}</div>
                                    <span class="text-sm"> Non Visited</span>
                                </div>
                            </div>
                            <!-- <div class="row">
                                <div class="col-md-12 ml-2" v-if="name">
                                    <h4>Name: {{ name }}</h4>
                                </div>
                            </div> -->
                        
                            <div class="table-responsive">
                               
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">Customer</th>
                                    <th scope="col">Address</th>
                                    <th scope="col">Schedule</th>
                                    <th scope="col">Attendance</th>
                                    <th scope="col">Duration</th>
                                    <th scope="col">Travel Time</th>
                                </tr>
                                </thead>
                                 <tbody v-if="userAppointmentScheduleData.length > 0">
                                    <tr v-for="(appointment, e) in userAppointmentScheduleData" v-bind:key="e"> 
                                        <td>{{ appointment.name }}</td>
                                        <td>{{ appointment.address }}</td>
                                        <td>{{ appointment.schedule }}</td>
                                        <td>{{ appointment.datetime }}</td>
                                        <td>{{ appointment.duration }}</td>
                                        <td>{{ appointment.travel_time }}</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td align="right">Visit Duration/Travel Time Total:</td>
                                        <td align="rigth">{{ appointment_total_duration }}</td>
                                        <td align="rigth">{{ appointment_total_travel_time }}</td>
                                    </tr>    
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td colspan='6' align="center">No Results Found</td>
                                    </tr>
                                </tbody>    
                                
                            </table>
                        </div>
                        

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
    import moment from 'moment';
    export default {
        components: {
            Multiselect
        },
       data(){
           return {
                userOptions:[],
                errors:[],
                selectedUser: '',
                selectedDate: '',
                userAppointmentsAll: [],
                userAppointmentScheduleData: [],
                name: '',
                appointment_total_duration : 0,
                appointment_total_travel_time : 0,
                countVisited : 0,
                countNonVisited : 0,
           }
       },
       created(){
           this.fetchUsers();
       },
       methods:{
           fetchUsers(){
               axios.get('/map-users-all')
                .then(response => { 
                    this.userOptions = response.data;
                })
                .catch(error => {
                    this.errors = error.response.data.errors;
                })
            },
            customLabelUser (user) {
                return `${ user.name + ' (' + user.company.name  + ')' }`
            },
            getUserAppointments(){
                let v = this;
                v.userAppointmentsAll = [];
                v.userAppointmentScheduleData = [];
                v.appointment_total_duration = 0;
                v.appointment_total_travel_time = 0;
                v.countVisited = 0;
                v.countNonVisited = 0;

                axios.post('/appointment-duration-report-data', {
                    selectedUser: this.selectedUser,
                    selectedDate: this.selectedDate,
                })
                .then(response =>{
                    if(response.data){
                        // v.name = response.data[0].user.name;
                        v.userAppointmentsAll = response.data;

                        v.userAppointmentScheduleData = [];
                        
                        let last_date_time = '';
                        let first = true;
                        let total_duration = 0;
                        let total_travel_time = 0;

                        v.userAppointmentsAll.forEach((appointment) => {
                            var datetime = '';
                            var duration = '';
                            var travel_time = '';
                            var get_duration = 0;

                            
                            if(first){
                                
                                travel_time = '';
                                first = false;

                                if(appointment.attendances){
                                    last_date_time = appointment.attendances ? appointment.attendances.sign_out : '';
                                    datetime = appointment.attendances.sign_in + ' - ' + appointment.attendances.sign_out;
                                    
                                    duration = v.rendered(appointment.attendances.sign_out ? appointment.attendances.sign_out : "", appointment.attendances.sign_in ? appointment.attendances.sign_in : "");
                                    total_duration += v.getTotalMinutes(appointment.attendances.sign_out ? appointment.attendances.sign_out : "", appointment.attendances.sign_in ? appointment.attendances.sign_in : "");
                                    
                                    v.countVisited += 1;
                                }else{
                                    datetime = '';
                                    duration = '';
                                    total_duration += 0;
                                    v.countNonVisited += 1;
                                }

                            }else{

                                // if(last_date_time){
                                    // if(appointment.attendances){
                                    //     travel_time = v.rendered(appointment.attendances.sign_in ? appointment.attendances.sign_in : '',last_date_time ? last_date_time : '');  
                                    //     total_travel_time += v.getTotalMinutes(appointment.attendances.sign_in ? appointment.attendances.sign_in : "",last_date_time ? last_date_time : '');  
                                    //     last_date_time = appointment.attendances.sign_out ? appointment.attendances.sign_out : '';     
                                    // }else{
                                    //     travel_time = '';
                                    // }
                                // }else{
                                //     travel_time = v.rendered(appointment.attendances ? appointment.attendances.sign_in : '',last_date_time);
                                //     total_travel_time += v.getTotalMinutes(appointment.attendances ? appointment.attendances.sign_in : "",last_date_time);
                                //     last_date_time = appointment.attendances ? appointment.attendances.sign_out : '';
                                // }
                                
                                if(appointment.attendances){

                                    travel_time = v.rendered(appointment.attendances.sign_in ? appointment.attendances.sign_in : '',last_date_time ? last_date_time : '');  
                                    total_travel_time += v.getTotalMinutes(appointment.attendances.sign_in ? appointment.attendances.sign_in : "",last_date_time ? last_date_time : '');  
                                    
                                    last_date_time = appointment.attendances.sign_out ? appointment.attendances.sign_out : appointment.attendances.sign_in;  

                                    datetime = appointment.attendances.sign_in + ' - ' + appointment.attendances.sign_out;
                                    
                                    duration = v.rendered(appointment.attendances.sign_out ? appointment.attendances.sign_out : "", appointment.attendances.sign_in ? appointment.attendances.sign_in : "");
                                    total_duration += v.getTotalMinutes(appointment.attendances.sign_out ? appointment.attendances.sign_out : "", appointment.attendances.sign_in ? appointment.attendances.sign_in : "");
                                    
                                    v.countVisited += 1;
                                }else{
                                    last_date_time = '';
                                    travel_time = '';
                                    datetime = '';
                                    duration = '';
                                    total_duration += 0;
                                    v.countNonVisited += 1;
                                }
                            }

                            v.userAppointmentScheduleData.push({
                                'name' : appointment.name,
                                'address' : appointment.address,
                                'schedule': appointment.date + ' ' + appointment.start_time + '-' + appointment.end_time,
                                'datetime':datetime,
                                'duration':duration,
                                'travel_time':travel_time
                            });

                        });
                        console.log(total_duration);
                        if(total_duration){
                            v.appointment_total_duration = parseFloat(total_duration / 60).toFixed(2);
                        }else{
                            v.appointment_total_duration = '';
                        }
                        if(total_travel_time > 0){
                            v.appointment_total_travel_time = parseFloat(total_travel_time / 60).toFixed(2);
                        }else{
                            v.appointment_total_travel_time = '';
                        }
                    }
                })
                .catch(error => {
                    this.errors = error;
                });
            },
            rendered(endTime, startTime){ 
                if(endTime && startTime){
                    var ms = moment(endTime,"YYYY/MM/DD HH:mm a").diff(moment(startTime,"YYYY/MM/DD HH:mm a"));
                    var d = moment.duration(ms);
                    var hours = Math.floor(d.asHours());
                    var minutes = moment.utc(ms).format("mm");
                    return hours + 'h '+ minutes+' min.'; 
                }else{
                    return 0;
                }                                  
            },
            getTotalMinutes(endTime, startTime){
                 if(endTime && startTime){
                    var ms = moment(endTime,"YYYY/MM/DD HH:mm a").diff(moment(startTime,"YYYY/MM/DD HH:mm a"));
                    var d = moment.duration(ms);
                    var minutes = Math.floor(d.asMinutes());
                    return minutes; 
                }else{
                    return 0;
                }        
            },
       },
       computed:{
    
       }
    }
</script>