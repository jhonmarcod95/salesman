<template>
    <div>
        <div class="header bg-green pb-6 pt-5 pt-md-6"></div>
        <div class="container-fluid mt--7">
            <div class="row mt-5">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h3 class="mb-0">Message List</h3>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="button" class="btn btn-sm btn-primary float-right" data-toggle="modal" data-target="#addModal" @click="fetchRecipient">
                                                <div>
                                                    <i class="ni ni-fat-add"></i>
                                                    <span>New message</span>
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="container">
                            <div class="messaging">
                                <div class="inbox_msg">
                                    <div class="inbox_people">
                                        <div class="headind_srch">
                                            <div class="recent_heading">
                                                <h4>Recent</h4>
                                            </div>
                                            <div class="srch_bar">
                                                <div class="stylish-input-group">
                                                    <input type="text" class="search-bar"  placeholder="Search" v-model="keywords" >
                                                    <span class="input-group-addon">
                                                        <button type="button"> <i class="fa fa-search" aria-hidden="true"></i> </button>
                                                    </span> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="inbox_chat">
                                            <div :class="[ !message.seen && message.user_id != userId ? 'chat_list active_chat' : 'chat_list']" @click="fetchSpecificMessage(message.last_message_for, message.id, message.recipient.name)" v-for="(message, m) in filteredMessage" v-bind:key="m">
                                                <div class="chat_people">
                                                    <div class="chat_img"> <img src="https://ptetutorials.com/images/user-profile.png" alt="sunil"> </div>
                                                    <div class="chat_ib">
                                                        <h5 :class="[ !message.seen && message.user_id != userId ? 'unseen_h5' : 'seen_h5']">{{ message.recipient.name }} <span class="chat_date">{{ moment(message.created_at).format('LLL') }}</span></h5>
                                                        <p :class="[ !message.seen && message.user_id != userId ? 'font-weight-bold text-dark' : '']">
                                                            {{ message.message | truncate(100, message.message.length > 100 ?' ...' : '' ) }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mesgs" v-if="message_specific.length">
                                        <div class="headind_srch pb-2">
                                            <div class="recent_heading">
                                                <h4>{{ header_name }}</h4>
                                            </div>
                                        </div>
                                        <div class="msg_history" id="my_div">
                                            <div v-for="(specific, s) in message_specific" v-bind:key="s">
                                                <div class="incoming_msg" v-if="specific.user_id != userId">
                                                    <div class="incoming_msg_img"> <img src="https://ptetutorials.com/images/user-profile.png" alt="sunil"> </div>
                                                    <div class="received_msg">
                                                        <div class="received_withd_msg">
                                                            <p>{{ specific.message }}</p>
                                                            <span class="time_date"> {{ specific.user.name + ' || ' +  moment(specific.created_at).format('LLL')  }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="outgoing_msg"  v-else>
                                                    <div class="sent_msg">
                                                        <p>{{ specific.message }}</p>
                                                        <span class="time_date"> {{ specific.user.name + ' || ' +  moment(specific.created_at).format('LLL')  }} </span> 
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="type_msg">
                                            <div class="input_msg_write">
                                            <input type="text" class="write_msg" placeholder="Type a message" v-model="new_message"/>
                                            <button class="msg_send_btn" type="button" :disabled="!new_message.length" @click="sendMessage(new_message)"><i class="ni ni-send"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Modal -->
        <div  class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addCompanyLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title" id="addCompanyLabel">New Message</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="comment">To</label>
                        <v-select v-model="selected" label="name" :options="recipients" style="width:100%"></v-select>
                        <label for="admin_new_message">Message:</label>
                        <textarea class="form-control" rows="5" id="admin_new_message" v-model="admin_new_message"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-default btn-round btn-fill" data-dismiss="modal">Close</button>
                <button @click="adminNewMessage(selected, admin_new_message)" type="button" class="btn btn-secondary" >Save</button>
                </div>
            </div>
            </div>
        </div>

    </div>
</template>

<script>
import moment from 'moment';
import Vue from 'vue'
import vSelect from 'vue-select'
export default {
    props:['userId'],
    components:{
        vSelect,
    },
    data(){
        return{
            messages: [],
            message_specific:[],
            message_id: '',
            new_message:'',
            admin_new_message:'',
            errors: [],
            keywords: '',
            header_name: '',
            recipients:[],
            selected: null
        }
    },
    created(){
        this.fetchMessage();
        Echo.private('chat')
            .listen('MessageSent', (e) => {
                var index = this.messages.findIndex(item => item.last_message_for == e.message[0][0].last_message_for);
                this.messages.splice(index,1);
                this.messages.unshift(e.message[0][0]);
            });
    },
    filters: {
        truncate: function (text, length, suffix) {
            return text.substring(0, length) + suffix;
        },
    },
    methods: {
        moment,
        fetchRecipient(){
            axios.get('/recipients')
            .then(response =>{
                this.recipients = response.data
            })
            .catch(error => { 
                this.errors = error.response.data.errors;
            })
        },
        fetchMessage(){
            axios.get('/messages-all')
            .then(response => { 
                this.messages = response.data;
            })
            .catch(error => { 
                this.errors = error.response.data.errors;
            })
        },
        fetchSpecificMessage(id, message_id, message_name){
            axios.get(`/messages-specific/${id}`)
            .then(response => { 
                this.message_specific = response.data;
                this.message_id = id;
                this.header_name = message_name;

                this.$nextTick(() => {
                    var myDiv = document.getElementById("my_div");
                    myDiv.scrollTop = myDiv.scrollHeight;

                    var lastMessage = this.message_specific.findIndex(item => item.id == message_id);
                    var index = this.messages.findIndex(item => item.id == message_id);
                    this.messages.splice(index,1,this.message_specific[lastMessage]);

                })
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            })
        },
        sendMessage(new_message){
            axios.post('/messages', {
                message: new_message,
                message_id: this.message_id
            })
            .then(response => { 
                this.message_specific.push(response.data[0]);
                this.new_message = '';
                this.$nextTick(() => {
                    var myDiv = document.getElementById("my_div");
                    myDiv.scrollTop = myDiv.scrollHeight;

                })
            })
            .catch(error => {
                this.errors = error.response.data.errors
            })  
        },
        adminNewMessage(recipient, message){
              axios.post('/messages', {
                message: message,
                message_id: recipient.id
            })
            .then(response => { 
                $('#addModal').modal('hide');
                var index = this.messages.findIndex(item => item.last_message_for == response.data[0].last_message_for);
                this.messages.splice(index,1);
                this.messages.unshift(response.data[0]);
            })
            .catch(error => {
                this.errors = error.response.data.errors
            })
        }
    },  
    computed:{
        filteredMessage(){
            let self = this;
            return self.messages.filter(message => {
                return message.recipient.name.toLowerCase().includes(this.keywords.toLowerCase());
            });
        },
    }
}
</script>
<style>
    .container{max-width:100%; margin:auto;}
    img{ max-width:100%;}
    .inbox_people {
    background: #f8f8f8 none repeat scroll 0 0;
    float: left;
    overflow: hidden;
    width: 40%; border-right:1px solid #c4c4c4;
    }
    .inbox_msg {
    border: 1px solid #c4c4c4;
    clear: both;
    overflow: hidden;
    }
    .top_spac{ margin: 20px 0 0;}


    .recent_heading {float: left; width:40%;}
    .srch_bar {
    display: inline-block;
    text-align: right;
    width: 60%;
    }
    .headind_srch{ padding:10px 29px 10px 20px; overflow:hidden; border-bottom:1px solid #c4c4c4;}

    .recent_heading h4 {
    color: #05728f;
    font-size: 21px;
    margin: auto;
    }
    .srch_bar input{ border:1px solid #cdcdcd; border-width:0 0 1px 0; width:80%; padding:2px 0 4px 6px; background:none;}
    .srch_bar .input-group-addon button {
    background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
    border: medium none;
    padding: 0;
    color: #707070;
    font-size: 18px;
    }
    .srch_bar .input-group-addon { margin: 0 0 0 -27px;}
    .unseen_h5{ font-size:15px; color:#464646; margin:0 0 8px 0;}
    .seen_h5{ font-size:15px; color:#989898; margin:0 0 8px 0;}
     /* .chat_ib h5{ font-size:15px; color:#464646; margin:0 0 8px 0;} */
    .chat_ib h5 span{ font-size:13px; float:right;}
    .chat_ib p{ font-size:14px; color:#989898; margin:auto}
    .chat_img {
    float: left;
    width: 11%;
    }
    .chat_ib {
    float: left;
    padding: 0 0 0 15px;
    width: 88%;
    }

    .chat_people{ overflow:hidden; clear:both; cursor: pointer;}
    .chat_list {
    border-bottom: 1px solid #c4c4c4;
    margin: 0;
    padding: 18px 16px 10px;
    }
    .inbox_chat { height: 666px; overflow-y: scroll;}

    .active_chat{ background:#ebebeb;}

    .incoming_msg_img {
    display: inline-block;
    width: 6%;
    }
    .received_msg {
    display: inline-block;
    padding: 0 0 0 10px;
    vertical-align: top;
    width: 92%;
    }
    .received_withd_msg p {
    background: #ebebeb none repeat scroll 0 0;
    border-radius: 3px;
    color: #646464;
    font-size: 14px;
    margin: 0;
    padding: 5px 10px 5px 12px;
    width: 100%;
    }
    .time_date {
    color: #747474;
    display: block;
    font-size: 12px;
    margin: 8px 0 0;
    }
    .received_withd_msg { width: 57%;}
    .mesgs {
    float: left;
    padding: 30px 15px 0 25px;
    width: 60%;
    }

    .sent_msg p {
    background: #2dce89  none repeat scroll 0 0;
    border-radius: 3px;
    font-size: 14px;
    margin: 0; color:#fff;
    padding: 5px 10px 5px 12px;
    width:100%;
    }
    .outgoing_msg{ overflow:hidden; margin:26px 0 26px;}
    .sent_msg {
    float: right;
    width: 46%;
    }
    .input_msg_write input {
    background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
    border: medium none;
    color: #4c4c4c;
    font-size: 15px;
    min-height: 48px;
    width: 100%;
    }

    .type_msg {border-top: 1px solid #c4c4c4;position: relative;}
    .msg_send_btn {
    background: #2dce89 none repeat scroll 0 0;
    border: medium none;
    border-radius: 50%;
    color: #fff;
    cursor: pointer;
    font-size: 17px;
    height: 33px;
    position: absolute;
    right: 0;
    top: 11px;
    width: 33px;
    }
    .messaging { padding: 0 0 50px 0;}
    .msg_history {
    height: 639px;
    overflow-y: scroll;
    }
</style>
