<template>
    <div class="d-flex">
        <div>
            <a class="btn btn-sm mr-2" 
                href="/expenses-report" 
                :class="pathName == '/expenses-report' ? 'btn-default' : 'btn-outline-default'">
                Expenses Report
            </a>
        </div>
        <div v-if="isItRole">
            <a class="btn btn-sm mr-2" 
                href="/coordinator-report" 
                :class="pathName == '/coordinator-report' ? 'btn-default' : 'btn-outline-default'">
                Coordinator Report
            </a>
        </div>
        <div v-if="salesHeadRole || isItRole">
            <a class="btn btn-sm mr-2" 
                href="/dms-received-expense" 
                :class="pathName == '/dms-received-expense' ? 'btn-default' : 'btn-outline-default'">
                 DMS Submitted Expense
            </a>
        </div>
        <div v-if="salesHeadRole || isItRole">
            <a class="btn btn-sm mr-2" 
                href="/expenses-deduction-report" 
                :class="pathName == '/expenses-deduction-report' ? 'btn-default' : 'btn-outline-default'">
                 Expense Deduction Report
            </a>
        </div>
        <div>
            <a class="btn btn-sm mr-2" 
                href="/expenses-top-spender-report" 
                :class="pathName == '/expenses-top-spender-report' ? 'btn-default' : 'btn-outline-default'">
                Expense Top Spender
            </a>
        </div>
        <!-- <div v-if="salesHeadRole || isItRole"><a class="btn btn-sm btn-default btn-outline-default mr-2" href="/dms-received-expense"> DMS Submitted Expense</a></div>
        <div><a class="btn btn-sm btn-default btn-outline-default mr-2" href="/expenses-top-spender-report"> Expense Top Spender</a></div> -->
    </div>
</template>

<script>
export default {
    props:['user-role'],
    computed: {
        isItRole() {
            return this.userRole == 1  // IT
        },
        presidentRole() {
            let userRole = [
                2,  // President,
                // 3,  // EVP,
            ];
            return _.includes(userRole, this.userRole);
        },
        salesHeadRole() {
            return this.userRole == 4  // VP/Sales Head
        },
        pathName() {
            return window.location.pathname;
        }
    }
}
</script>