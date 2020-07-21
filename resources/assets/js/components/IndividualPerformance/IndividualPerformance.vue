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
                                    <h3 class="mb-0">Individual Performance</h3>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="row ml-2 mr-2">
                                <div class="col-md-3 float-left">
                                    <div class="form-group">
                                        <label for="name" class="form-control-label">Search TSR</label> 
                                        <input type="text" class="form-control" placeholder="Search TSR" v-model="keywords" id="name">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">TSR</th>
                                    <th scope="col">Total Customer Visited</th>
                                    <th scope="col">Total Customer Order</th>
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
    
}
</script>