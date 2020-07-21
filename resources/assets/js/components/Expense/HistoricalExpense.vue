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
                                    <h3 class="mb-0">Historical Information Expense</h3>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="row ml-2 mr-2">
                                <div class="col-md-3 float-left">
                                    <div class="form-group">
                                        <label class="form-control-label" for="role">Select Month</label>
                                        <select class="form-control" v-model="month">
                                            <option v-for="(select_month,c) in months" v-bind:key="c" :value="select_month.id"> {{ select_month.name }}</option>
                                        </select>
                                        <span class="text-danger" v-if="errors.month  ">{{ errors.month[0] }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3 float-left">
                                    <div class="form-group">
                                        <label class="form-control-label" for="role">Select Year</label>
                                        <select class="form-control" v-model="year">
                                            <option v-for="(select_year,c) in years" v-bind:key="c" :value="select_year.id"> {{ select_year.name }}</option>
                                        </select>
                                        <span class="text-danger" v-if="errors.year  ">{{ errors.year[0] }}</span>
                                    </div>
                                 </div>
                                <div class="col-md-2 text-center">
                                    <button class="btn btn-sm btn-primary" @click="fetchHistoricalExpense"> Filter</button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">ID</th>
                                    <th scope="col">TSR Name</th> 
                                    <th scope="col">Month</th>
                                    <th scope="col">Total Expense</th>
                                    <th scope="col">Total Budget</th>
                                </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    export default {
        data(){
            return{
                months: [
                    {
                        "id": '01',
                        "name": "January",
                    },
                    {
                        "id": '02',
                        "name": "February",
                    },
                    {
                        "id": '03',
                        "name": "March",
                    },
                    {
                        "id": '04',
                        "name": "April",
                    },
                    {
                        "id": '05',
                        "name": "May",
                    },
                    {
                        "id": '06',
                        "name": "June",
                    },
                    {
                        "id": '07',
                        "name": "July",
                    },
                    {
                        "id": '08',
                        "name": "August",
                    },
                    {
                        "id": '09',
                        "name": "September",
                    },
                    {
                        "id": '10',
                        "name": "October",
                    },
                    {
                        "id": '11',
                        "name": "November",
                    },
                    {
                        "id": '12',
                        "name": "December",
                    },
                ],
                years : [
                    {
                        'id':'2020',
                        'name':'2020',
                    },
                    {
                        'id':'2019',
                        'name':'2019',
                    },
                    {
                        'id':'2018',
                        'name':'2018',
                    },
                    {
                        'id':'2017',
                        'name':'2017',
                    },
                ],
                month: '',
                year: '',
                errors: [],
            }
        },
        created(){

        },
        methods: {
            currentHistoricalExpense(){

            },
            fetchHistoricalExpense(){

            }
        }
    }
</script>
