<template>
    <div>
        <!-- Header -->
        <div class="header bg-green pb-8 pt-5 pt-md-8" style="min-height: 300px; background-image: url(/img/bg.jpg); background-size: cover; background-position: center bottom;">
            <span class="mask bg-gradient-primary opacity-7"></span>
            <div class="container-fluid">
                <div class="header-body">
                    <div class="row">
                        <div class="col-xl-3 col-lg-6" style="cursor:pointer">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">Schedule for Visit</h5>
                                            <span class="h2 font-weight-bold mb-0">0</span>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon icon-shape bg-success text-white rounded-circle shadow">
                                                <i class="fas fa-calendar"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mt-3 mb-0 text-muted text-sm">
                                        <span class="text-nowrap">Today</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6" style="cursor:pointer">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">Actual Visited</h5>
                                            <span class="h2 font-weight-bold mb-0">0</span>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon icon-shape bg-info text-white rounded-circle shadow">
                                                <i class="fas fa-map-marker"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mt-3 mb-0 text-muted text-sm">
                                        <span class="text-nowrap">Today</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6" style="cursor:pointer">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">Total Travel Time</h5>
                                            <span class="h2 font-weight-bold mb-0">0</span>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon icon-shape bg-primary text-white rounded-circle shadow">
                                                <i class="fas fa-plane"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mt-3 mb-0 text-muted text-sm">
                                        <span class="text-nowrap">Today</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-lg-6" style="cursor:pointer">
                            <div class="card card-stats mb-4 mb-xl-0">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col">
                                            <h5 class="card-title text-uppercase text-muted mb-0">Total Client Interaction</h5>
                                            <span class="h2 font-weight-bold mb-0">0</span>
                                        </div>
                                        <div class="col-auto">
                                            <div class="icon icon-shape bg-warning text-white rounded-circle shadow">
                                                <i class="fas fa-users"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="mt-3 mb-0 text-muted text-sm">
                                        <span class="text-nowrap">Today</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid mt--8">
            <div class="row mt-5">

                <div class="col-xl-12 mb-5 mb-xl-0">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="mb-0">Actual Visited Customer</h3>
                                </div>
                                <div class="col-12">
                                    <div class="card-body">
                                        <line-chart :chart-data="actualVisitedCustomer" :height="70" :download="true"  ></line-chart>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 mt-3 mb-5 mb-xl-0">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="mb-0">Customer Visits</h3>
                                </div>
                                <div class="col text-right">
                                    <a href="#!" class="btn btn-sm btn-primary">Most</a>
                                    <a href="#!" class="btn btn-sm btn-primary">Less</a>
                                    <a href="#!" class="btn btn-sm btn-primary">Average</a>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">Customer Name</th>
                                    <th scope="col">Total Visits</th>
                                </tr>
                                </thead>
                                <tbody>
                                   
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 mt-3 mb-5 mb-xl-0">
                    <div class="card shadow">
                        <div class="card-header border-0">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3 class="mb-0">TSR Customer Visits</h3>
                                </div>
                                <div class="col text-right">
                                    <a href="#!" class="btn btn-sm btn-primary">Most</a>
                                    <a href="#!" class="btn btn-sm btn-primary">Less</a>
                                    <a href="#!" class="btn btn-sm btn-primary">Average</a>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col">TSR Name</th>
                                    <th scope="col">Total Visits</th>
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
import moment from 'moment';
import BarChart from '../Charts/BarChart.js'
import LineChart from '../Charts/LineChart.js'

export default {
    components: {
        BarChart,
        LineChart
    },
    data(){
        return{
            actualVisitedCustomer : null
        }
    },
    created(){
      this.actualVisitedCustomerData();
    },
    methods:{
        actualVisitedCustomerData ()
        {
            var count = [];

            this.actualVisitedCustomer = {
                labels: ['Jan','Feb','Mar','Apr','May', 'Jun' , 'Jul', 'Aug', 'Sept', 'Oct','Nov','Dec'],
                datasets: [
                    {
                        label: "Actual Visited Customer",
                        backgroundColor: 'rgba(45, 206 , 137, 0.5)',
                        pointBackgroundColor: 'white',
                        borderWidth: 1,
                        pointBorderColor: '#249EBF',
                        data: [1,2,3,4,5,6,7,8,10,11,12,3],
                    },
                    {
                        label: "Non Visited Customer",
                        backgroundColor: 'rgba(94,114,228, 0.5)',
                        pointBackgroundColor: 'white',
                        borderWidth: 1,
                        pointBorderColor: '#249EBF',
                        data: [11,2,23,4,15,6,7,18,10,2,3,12],
                    },
                ]
            }
        },
    },
    computed:{
        
    }
}
</script>

<style>
    @media (min-width: 768px) {
        .modal-xl {
            width: 90%;
            max-width:1200px;
        }
    }
</style>
