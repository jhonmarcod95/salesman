<template>
    <div class="card shadow">
        <div class="card-header">
            <h3 class="mb-0">Haven't Submit <span v-if="!isEmpty(items) && !isProcessing">({{ pagination.total }})</span></h3>
        </div>
        <div class="card-body">
            <div class="position-relative">
                <!-- Begin:Block UI -->
                <app-block-ui v-if="!isEmpty(items) && isProcessing"></app-block-ui>
                <!-- End:Block UI -->

                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                        <tr>
                            <th scope="col">TSR</th>
                            <th scope="col">Month</th>
                            <th scope="col">Year</th>
                            <th scope="col">Expense Status</th>
                            <th scope="col">Expense Count</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr v-if="isEmpty(items) && isProcessing">
                                <td colspan="10">Loading Data...</td>
                            </tr>
                            <tr v-else v-for="(user, e) in items" v-bind:key="e">
                                <td>
                                    <strong>{{ user.name }}</strong> <br>
                                    <span>{{ user.companies ? user.companies[0].name : '' }}</span>
                                </td>
                                <td>{{ user.month }}</td>
                                <td>{{ user.year }}</td>
                                <td>
                                    <div class="mb-0"><span style="width:90px; display: inline-block;">Verified: </span>{{ user.expense_status.verified }}</div>
                                    <div class="mb-0"><span style="width:90px; display: inline-block;">Unverified: </span>{{ user.expense_status.unverified }}</div>
                                    <div class="mb-0"><span style="width:90px; display: inline-block;">Rejected: </span>{{ user.expense_status.rejected }}</div>
                                    <!-- <div class="mb-0"><span style="width:90px; display: inline-block;">Not Verified: </span>{{ user.expense_status.not_verified }}</div> -->
                                </td>
                                <td>{{ user.expense_status.expense_count }}</td>
                            </tr>
                            <tr v-if="isEmpty(items) && !isProcessing">
                                <td>No data available in the table</td>
                            </tr>
                        </tbody>
                        <!-- <tbody v-else>
                            
                        </tbody> -->
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <!--begin::Pagination-->
            <table-pagination v-if="items.length > 0" :pagination="pagination" v-on:updatePage="goToPage" v-on:doChangeLimit="changePageCount"/>
            <!--end::Pagination-->
        </div>
    </div>
</template>

<script>
    import listFormMixins from '../../list-form-mixins.vue'
    export default {
        props: ['filterParams'],
        mixins: [listFormMixins],
        data() {
            return {
                endpoint: '/dms-received-expense',
                listEndpoint: 'not-received-expense'
            }
        },
        created() {
            this.filterData = this.filterParams;
            this.fetchList();
        },
        methods: {
            
        },
        watch: {
            filterParams: {
                handler(val){
                    this.filterData = val;
                    this.fetchList();
                },
                deep: true
            }
        },
    }
</script>