<template>
      <div>
        <loader v-if="loading"></loader>
        <div class="header bg-green pb-6 pt-5 pt-md-6"></div>
        <div class="container-fluid m-2">
            <button class="btn btn-sm btn-primary" @click="runCommand('/auto-posting/LFUG')">Auto Posting LFUG</button>
            <button class="btn btn-sm btn-primary" @click="runCommand('/auto-posting/HANA')">Auto Posting HANA</button>
            <button class="btn btn-sm btn-primary" @click="runCommand('/auto-posting-reprocessing/LFUG')">Auto Posting Reprocessing LFUG</button>
            <button class="btn btn-sm btn-primary" @click="runCommand('/auto-posting-reprocessing/HANA')">Auto Posting HANA</button>
            <button class="btn btn-sm btn-primary" @click="runCommand('/auto-cv')">Auto CV</button>
            <button class="btn btn-sm btn-primary" @click="runCommand('/auto-check')">Auto Check</button>

            <div class="mt-4 mb-1">Output:</div>
            <div class="border border-light rounded p-2">{{ this.output }}</div>
        </div>
        
    </div>
</template>
<script>
// import moment from 'moment';
import loader from '../Loader';
import Swal from 'sweetalert2';

export default {
    components: { loader },
    data(){
        return{
            output: 'Nothing to display',
            errors: [],
            loading: false
        }
    },
    // created(){
    // },
    methods:{
        runCommand(command) {
            Swal.fire({
                title: "asdasd",
                text: "asdasdasd",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#e24444",
                cancelButtonColor: "#666666",
                confirmButtonText: "Run",
            }).then((result) => {
                if (result.isConfirmed) {
                    this.loading = true;
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
                        this.output = error.response.data.errors;
                        this.loading = false;
                    });
                }
            });
        }
    }
}
</script>
