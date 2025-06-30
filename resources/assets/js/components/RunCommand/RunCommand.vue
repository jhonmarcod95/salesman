<template>
      <div>
        <div class="header bg-green pb-6 pt-5 pt-md-6"></div>
        <div class="container-fluid m-2">
            <button :disabled="loading" class="btn btn-primary" @click="runCommand('/auto-posting/LFUG','Post LFUG expenses?')">Auto Posting LFUG</button>
            <button :disabled="loading" class="btn btn-primary" @click="runCommand('/auto-posting/HANA','Post HANA expenses?')">Auto Posting HANA</button>
            <button :disabled="loading" class="btn btn-primary" @click="runCommand('/auto-posting-reprocessing/LFUG','Reprocess LFUG postings?')">Auto Posting Reprocessing LFUG</button>
            <button :disabled="loading" class="btn btn-primary" @click="runCommand('/auto-posting-reprocessing/HANA','Reprocess HANA postings?')">Auto Posting Reprocessing HANA</button>
            <button :disabled="loading" class="btn btn-primary" @click="runCommand('/auto-cv','Post check voucher expenses?')">Auto CV</button>
            <button :disabled="loading" class="btn btn-primary" @click="runCommand('/auto-check','Post check expenses?')">Auto Check</button>

            <div v-if="this.output != ''" class="border border-success text-success rounded mt-4 p-2">
                {{ this.loading? "Loading...": this.output }}
            </div>
            <div v-if="this.errors != ''" class="border border-danger text-danger rounded mt-4 p-2">
                {{ this.loading? "Loading...": this.errors }}
            </div>
        </div>
        
    </div>
</template>
<script>
// import moment from 'moment';
import Swal from 'sweetalert2';

export default {
    data(){
        return{
            output: '',
            errors: '',
            loading: false
        }
    },
    // created(){
    // },
    methods:{
        runCommand(command, title) {
            Swal.fire({
                title: title,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Run",
            }).then((result) => {
                if (result.isConfirmed) {
                    this.loading = true;
                    this.output = '';
                    this.errors = '';
                    axios.get(command)
                    .then(response => {
                        this.output = response.data;
                        Swal.fire({
                            title: "Success!",
                            icon: "success",
                            confirmButtonColor: "666666",
                            confirmButtonText: "Close",
                        });
                        this.loading = false;
                    })
                    .catch (error => {
                        this.errors = error.response.data.message;
                        this.loading = false;
                    });
                }
            });
        }
    }
}
</script>
