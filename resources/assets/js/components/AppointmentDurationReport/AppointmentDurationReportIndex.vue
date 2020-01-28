<template>
<div>
    <loader v-if="loading"></loader>
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
                                                v-model="selectedUsers"
                                                :options="userOptions"
                                                :multiple="true"
                                                track-by="id"
                                                :custom-label="customLabelUser"
                                                placeholder="Select User"
                                                id="selected_user"
                                        >
                                        </multiselect>

                                        <span class="text-danger" v-if="errors.selectedUsers"> {{ errors.selectedUsers[0] }} </span>
                                    </div>
                                </div>
                                <div class="col-md-3 float-left">
                                    <div class="form-group">
                                        <label for="startDate" class="form-control-label">From</label> 
                                        <input type="date" id="startDate" class="form-control form-control-alternative" v-model="startDate">
                                        <span class="text-danger" v-if="errors.startDate"> {{ errors.startDate[0] }} </span>
                                    </div>
                                </div>
                                <div class="col-md-3 float-left">
                                    <div class="form-group">
                                        <label for="endDate" class="form-control-label">To</label> 
                                        <input type="date" id="endDate" class="form-control form-control-alternative" v-model="endDate">
                                        <span class="text-danger" v-if="errors.endDate"> {{ errors.endDate[0] }} </span>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <button class="btn btn-sm btn-primary" @click="getUserAppointments"> Apply Filter</button>                                
                                </div>

                                
                            </div>
                            
                            <div v-for="(user, e) in userAppointmentScheduleData" v-bind:key="e" class="mt-3">
                                    <div class="col-md-12 mt-1">
                                        <span style="font-size:1.2rem;color:#5e72e4;font-weight:bold">{{user.name}}</span>
                                        <span style="font-size:0.9rem;font-weight:bold" class="ml-2"><strong style="color:#2dce89">Visited:</strong> {{user.user_count_visited}}</span>
                                        <span style="font-size:0.9rem;font-weight:bold" class="ml-1"><strong style="color:#f5365c">Non Visited:</strong> {{user.user_count_non_visited}}</span>
                                        <span style="font-size:0.9rem;font-weight:bold" class="ml-1"><strong style="color:#FFA809">Incomplete Attendance:</strong> {{user.user_count_incomplete_attendance}}</span>
                                    </div>
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
                                            <tbody v-if="user.schedule_data.length > 0">
                                                <tr v-for="(schedule, k) in user.schedule_data" v-bind:key="k"> 
                                                    <td>{{ schedule.name }}</td>
                                                    <td>{{ schedule.address }}</td>
                                                    <td>{{ schedule.schedule }}</td>
                                                    <td v-if="schedule.sub_total" align="right"  style="font-weight:bold;">Visit Duration/Travel Time Total:</td>
                                                    <td v-else align="center">
                                                            <span v-if="schedule.isCompleteAttendance" style="color:#525f7f">{{ schedule.datetime }}</span>
                                                            <span v-else style="color:orange;font-weight:bold;">{{ schedule.datetime }}</span>
                                                    </td>
                                                    <td v-if="schedule.sub_total" align="left" style="font-weight:bold;">{{ schedule.sub_total_duration }}</td>
                                                    <td v-else>{{ schedule.duration }}</td>
                                                    <td v-if="schedule.sub_total" align="left" style="font-weight:bold;">{{ schedule.sub_total_time_travel }}</td>
                                                    <td v-else>{{ schedule.travel_time }}</td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td align="right" style="font-weight:bold;">Visit Duration/Travel Time Total:</td>
                                                    <td align="left" style="font-weight:bold;">{{ user.last_total_duration }}</td>
                                                    <td align="left" style="font-weight:bold;">{{ user.last_total_travel_time }}</td>
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
                selectedUsers: '',
                startDate: '',
                endDate: '',
                userAppointmentsAll: [],
                userAppointmentScheduleData: [],
                name: '',
                last_total_duration : 0,
                last_total_travel_time : 0,
                countVisited : 0,
                countNonVisited : 0,
                loading:false
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
                this.loading = true;
         
                v.userAppointmentsAll = [];
                v.userAppointmentScheduleData = [];
                v.last_total_duration = 0;
                v.last_total_travel_time = 0;

                axios.post('/appointment-duration-report-data', {
                    selectedUsers: this.selectedUsers,
                    startDate: this.startDate,
                    endDate: this.endDate,
                })
                .then(response =>{
                    if(response.data){
                  
                        v.userAppointmentsAll = response.data;
                        v.userAppointmentScheduleData = [];
        
                        let tsr_user_id = '';
                        let tsr_name = '';
                        let schedule_date = '';
                        let first_user = true;

                        v.userAppointmentsAll.forEach((user,key) => {

                            let last_date_time = '';
                            let first = true;

                            let total_duration = 0;
                            let total_travel_time = 0;

                            let last_total_duration = '';
                            let last_total_travel_time = '';
        
                            let last_schedule_date = '';

                            let schedule_data = [];

                            let user_count_visited = 0;
                            let user_count_non_visited = 0;
                            let user_count_incomplete_attendance = 0;

                            let last_schedule_id = '';

                            if(user.schedules){

                                user.schedules.forEach((appointment,key) => {

                                    var datetime = '';
                                    var duration = '';
                                    var travel_time = '';
                                    var get_duration = 0;
                                    var isCompleteAttendance = false;
                                
                                    if(first){
                                        
                                        travel_time = '';
                                        first = false;

                                        if(appointment.attendances){
                                            user_count_visited += 1;
                                            last_date_time = appointment.attendances ? appointment.attendances.sign_out : '';
                                            
                                            if(appointment.attendances.sign_out){
                                                datetime = appointment.attendances.sign_in  + ' - ' + appointment.attendances.sign_out;
                                                isCompleteAttendance = true;
                                            }else{
                                                datetime = appointment.attendances.sign_in  + ' - No Sign Out';
                                                isCompleteAttendance = false;
                                                user_count_incomplete_attendance += 1;
                                            }
                                            
                                            duration = v.rendered(appointment.attendances.sign_out ? appointment.attendances.sign_out : "", appointment.attendances.sign_in ? appointment.attendances.sign_in : "");
                                            total_duration += v.getTotalMinutes(appointment.attendances.sign_out ? appointment.attendances.sign_out : "", appointment.attendances.sign_in ? appointment.attendances.sign_in : "");
                                                
                                        }else{
                                            datetime = '';
                                            duration = '';
                                            total_duration += 0;
                                            user_count_non_visited += 1;
                                        }

                                        last_schedule_date = appointment.date;
                                        last_schedule_id = appointment.id;

                                        schedule_data.push({
                                            'name' : appointment.name,
                                            'address' : appointment.address,
                                            'schedule': appointment.date + ' ' + appointment.start_time + '-' + appointment.end_time,
                                            'datetime': datetime,
                                            'duration': duration,
                                            'travel_time': travel_time,
                                            'isCompleteAttendance':isCompleteAttendance
                                        });

                                    }else{

                                        if(appointment.attendances){
                                            if(last_schedule_id != appointment.id){
                                                user_count_visited += 1;
                                                if(last_schedule_date == appointment.date){
                                                    travel_time = v.rendered(appointment.attendances.sign_in ? appointment.attendances.sign_in : '',last_date_time ? last_date_time : '');  
                                                    total_travel_time += v.getTotalMinutes(appointment.attendances.sign_in ? appointment.attendances.sign_in : "",last_date_time ? last_date_time : '');  
    
                                                }else{
                                                    //If Date is last
                                                    travel_time = '';
                                                    total_travel_time += 0;
                                                    
                                                    if(total_duration){
                                                        last_total_duration = parseFloat(total_duration / 60).toFixed(2);
                                                    }else{
                                                        last_total_duration = '';
                                                    }
                                                    if(total_travel_time > 0){
                                                        last_total_travel_time = parseFloat(total_travel_time / 60).toFixed(2);
                                                    }else{
                                                        last_total_travel_time = '';
                                                    }

                                                    schedule_data.push({
                                                        'sub_total' : true,
                                                        'sub_total_duration' : last_total_duration,
                                                        'sub_total_time_travel' : last_total_travel_time,
                                                    });

                                                    total_duration = 0;
                                                    total_travel_time = 0;

                                                }
                                                
                                                last_date_time = appointment.attendances.sign_out ? appointment.attendances.sign_out : appointment.attendances.sign_in;  

                                                if(appointment.attendances.sign_out){
                                                    datetime = appointment.attendances.sign_in  + ' - ' + appointment.attendances.sign_out;
                                                    isCompleteAttendance = true;
                                                }else{
                                                    datetime = appointment.attendances.sign_in  + ' - No Sign Out';
                                                    isCompleteAttendance = false;
                                                    user_count_incomplete_attendance += 1;
                                                    
                                                }

                                                duration = v.rendered(appointment.attendances.sign_out ? appointment.attendances.sign_out : "", appointment.attendances.sign_in ? appointment.attendances.sign_in : "");
                                                total_duration += v.getTotalMinutes(appointment.attendances.sign_out ? appointment.attendances.sign_out : "", appointment.attendances.sign_in ? appointment.attendances.sign_in : "");

                                                
                                               
                                            }
                                            
                                        }else{
                                            last_date_time = '';
                                            travel_time = '';
                                            datetime = '';
                                            duration = '';
                                            total_duration += 0;
                                            user_count_non_visited += 1;
                                        }

                                        last_schedule_date = appointment.date;
                                        
                                    }
                                    
                                    

                                    if(last_schedule_id != appointment.id){
                                        schedule_data.push({
                                            'name' : appointment.name,
                                            'address' : appointment.address,
                                            'schedule': appointment.date + ' ' + appointment.start_time + '-' + appointment.end_time,
                                            'datetime': datetime,
                                            'duration': duration,
                                            'travel_time': travel_time,
                                            'isCompleteAttendance':isCompleteAttendance
                                        });

                                        last_schedule_id = appointment.id;
                                    }

                                    
                            
                                });
                            }

                            if(total_duration){
                                last_total_duration = parseFloat(total_duration / 60).toFixed(2);
                            }else{
                                last_total_duration = '';
                            }
                            if(total_travel_time > 0){
                                last_total_travel_time = parseFloat(total_travel_time / 60).toFixed(2);
                            }else{
                                last_total_travel_time = '';
                            }

                            v.userAppointmentScheduleData.push({
                                'name' : user.name,
                                'schedule_data' : schedule_data,
                                'last_total_duration' : last_total_duration,
                                'last_total_travel_time' : last_total_travel_time,
                                'user_count_visited':user_count_visited,
                                'user_count_non_visited':user_count_non_visited,
                                'user_count_incomplete_attendance':user_count_incomplete_attendance,
                            });
                        });
                        v.loading = false;
                    }else{
                        v.loading = false;
                    }
                })
                .catch(error => {
                    this.errors = error;
                    v.loading = false;
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