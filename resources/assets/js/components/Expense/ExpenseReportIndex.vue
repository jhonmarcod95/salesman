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
                                    <h3 class="mb-0">Expenses Report</h3>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="row ml-2">
                                <div class="col-md-4 float-left">
                                    <div class="form-group">
                                        <label for="name" class="form-control-label">Search</label> 
                                        <input type="text" class="form-control" placeholder="Search" v-model="keywords" id="name">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="start_date" class="form-control-label">Start Date</label> 
                                        <input type="date" id="start_date" class="form-control form-control-alternative" v-model="startDate">
                                        <span class="text-danger" v-if="errors.startDate"> {{ errors.startDate[0] }} </span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="end_date" class="form-control-label">End Date</label> 
                                        <input type="date" id="end_date" class="form-control form-control-alternative" v-model="endDate">
                                        <span class="text-danger" v-if="errors.endDate"> {{ errors.endDate[0] }} </span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-sm btn-primary" @click="fetchExpenses"> Filter</button>
                                    <button class="btn btn-sm btn-success" :disabled="exportExcel" @click="exportExpenses"> Export</button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-items-center table-flush">
                                <thead class="thead-light">
                                <tr>
                                    <th scope="col"></th>
                                    <th scope="col">TSR</th>
                                    <th scope="col">Expense Submitted</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Total Expenses</th>
                                    

                                </tr>
                                </thead>
                                <tbody v-if="expenses.length">
                                    <tr v-for="(expense, e) in filteredQueues" v-bind:key="e">
                                        <td class="text-right" v-if="userLevel != 5">
                                            <div class="dropdown">
                                                <a class="btn btn-sm btn-icon-only text-light" href="#" role="button"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                                    <!-- <a class="dropdown-item" href="javascript:void(0)"  @click="fetchExpenseByTsr(expense.id, expense.user.name, expense.created_at)">View</a> -->
                                                </div>
                                            </div>
                                        </td>
                                        <td v-else></td>
                                        <td>{{ expense.user ? expense.user.name : "" }}</td>
                                        <td>{{ expense.expenses_model_count  }}</td>
                                        <td>{{ moment(expense.created_at).format('ll') }}</td>
                                        <td>PHP {{ countTotalExpenses(expense) }}</td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                       <tr>
                                           <td>No data available in the table</td>
                                       </tr>
                                </tbody>
                            </table>
                        </div>
                       <div class="card-footer py-4" v-if="expenses.length">
                            <nav aria-label="...">
                                <ul class="pagination justify-content-end mb-0">
                                    <li class="page-item">
                                        <button :disabled="!showPreviousLink()" class="page-link" v-on:click="setPage(currentPage - 1)"> <i class="fas fa-angle-left"></i> </button>
                                    </li>
                                    <li class="page-item">
                                        Page {{ currentPage + 1 }} of {{ totalPages }}
                                    </li>
                                    <li class="page-item">
                                        <button :disabled="!showNextLink()" class="page-link" v-on:click="setPage(currentPage + 1)"><i class="fas fa-angle-right"></i> </button>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- View Expense Modal -->
        <div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <span class="closed" data-dismiss="modal">&times;</span>
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCompanyLabel">Expenses Submitted</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                  <div class="row">
                        <div class="col"><h3>TSR: {{ this.tsrName }}</h3></div>
                        <div class="col"><h3>Date: {{ moment(this.date).format('ll') }} </h3></div>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-items-center table-flush">
                            <thead class="thead-light">
                            <tr>
                                <th scope="col">Attachment</th>
                                <th scope="col">Type of Expense</th>
                                <th scope="col">Date</th>
                                <th scope="col">Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(expenseBy, e) in expenseByTsr" v-bind:key="e">
                                    <td> <a :href="imageLink+expenseBy.attachment" target="__blank"><img class="rounded-circle" :src="imageLink+expenseBy.attachment" style="height: 70px; width: 70px" @error="noImage"></a></td>
                                    <td>{{ expenseBy.expenses_type ? expenseBy.expenses_type.name : "" }}</td>
                                    <td>{{ moment(expenseBy.created_at).format('ll') }}</td>
                                    <td>PHP {{ expenseBy.amount.toFixed(2) }} </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-round btn-fill" data-dismiss="modal">Close</button>
                </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import moment from 'moment';
import Excel from 'exceljs';
import FileSaver from 'file-saver';

export default {
    props:['userLevel'],
    data(){
        return{
            expenses: [],
            expenseByTsr: [],
            startDate: '',
            endDate: '',
            tsrName: '',
            date: '',
            exportExcel:true,  
            errors: [],
            keywords: '',
            currentPage: 0,
            itemsPerPage: 10,
        }
    },
    created(){

    },
    methods:{
        moment,
        noImage(event){
            event.target.src = window.location.origin+'/img/brand/no-image.png';
        },
        countTotalExpenses(expense){
            var totalExpenses = 0;
            expense.expenses_model.forEach(element => {
                totalExpenses = totalExpenses + element.amount;
            });
            return totalExpenses.toFixed(2);
        },
        exportExpenses(){
           let v = this;
            var workbook = new Excel.Workbook();
            var worksheet = workbook.addWorksheet('Expense Report');
            
            //Header 
            worksheet.columns = [{ width: 20 },{ width: 20},{ width: 20},{ width: 20},{ width: 20},{ width: 20}];

            worksheet.getCell("A1").value = 'Collector';
            worksheet.getCell("A1" + from).border = {top: {style:'thin'},left: {style:'thin'},bottom: {style:'thin'},right: {style:'thin'}};
            worksheet.getCell("B1").value = 'EXPENSE SUBMITTED';
            worksheet.getCell("B1" + from).border = {top: {style:'thin'},left: {style:'thin'},bottom: {style:'thin'},right: {style:'thin'}};
            worksheet.getCell("C1").value = 'DATE';
            worksheet.getCell("C1" + from).border = {top: {style:'thin'},left: {style:'thin'},bottom: {style:'thin'},right: {style:'thin'}};
            worksheet.getCell("D1").value = 'TYPE OF EXPENSE';
            worksheet.getCell("D1" + from).border = {top: {style:'thin'},left: {style:'thin'},bottom: {style:'thin'},right: {style:'thin'}};
            worksheet.getCell("E1").value = 'AMOUNT';
            worksheet.getCell("E1" + from).border = {top: {style:'thin'},left: {style:'thin'},bottom: {style:'thin'},right: {style:'thin'}};
            worksheet.getCell("F1").value = 'TOTAL';
            worksheet.getCell("F1" + from).border = {top: {style:'thin'},left: {style:'thin'},bottom: {style:'thin'},right: {style:'thin'}};


            let from = 2;
            let to = 2;
            let total_submitted = 0; 
            let total_amount_expense = 0; 
            let total_grand_amount = 0; 
            let total_grand_amount_expense = 0; 
            v.expenses.forEach(function(w){
                if(from == 2){
                    let count_expense = Number(w.expenses_model.length)
                    
                    to = count_expense + 1;
                    if(count_expense > 0){
                        //Collector
                        worksheet.mergeCells("A" + from + ":" + "A" + to);
                        worksheet.getCell("A" + from).value = w.user ? w.user.name : "";
                        worksheet.getCell("A" + from).border = {top: {style:'thin'},left: {style:'thin'},bottom: {style:'thin'},right: {style:'thin'}};

                        //Submitted
                        worksheet.mergeCells("B" + from + ":" + "B" + to);
                        worksheet.getCell("B" + from).value = count_expense;
                        worksheet.getCell("B" + from).border = {top: {style:'thin'},left: {style:'thin'},bottom: {style:'thin'},right: {style:'thin'}};

                        //Date
                        worksheet.mergeCells("C" + from + ":" + "C" + to);
                        worksheet.getCell("C" + from).value = moment(w.created_at).format('ll');
                        worksheet.getCell("C" + from).border = {top: {style:'thin'},left: {style:'thin'},bottom: {style:'thin'},right: {style:'thin'}};

                        //Expense and Amount
                        total_amount_expense = 0; 
                        var expense = from;
                        w.expenses_model.forEach(function(e){

                            worksheet.getCell("D" + expense).value = e.expenses_type ? e.expenses_type.name : "";
                            worksheet.getCell("D" + expense).border = {top: {style:'thin'},left: {style:'thin'},bottom: {style:'thin'},right: {style:'thin'}};

                            worksheet.getCell("E" + expense).value = e.amount;
                            worksheet.getCell("E" + expense).border = {top: {style:'thin'},left: {style:'thin'},bottom: {style:'thin'},right: {style:'thin'}};

                            expense++;

                            total_amount_expense += e.amount;
                        });

                        //Total
                        worksheet.mergeCells("F" + from + ":" + "F" + to);
                        worksheet.getCell("F" + from).value = total_amount_expense;
                        worksheet.getCell("F" + from).border = {top: {style:'thin'},left: {style:'thin'},bottom: {style:'thin'},right: {style:'thin'}};
                        

                        total_submitted += count_expense;
                        total_grand_amount += total_amount_expense;
                        total_grand_amount_expense += total_amount_expense;
                        
                    }
                    from = to + 1;    
                }else{
                    let count_expense = Number(w.expenses_model.length);

                    if(count_expense > 0){
                        to += count_expense;
                        //Collector
                        worksheet.mergeCells("A" + from + ":" + "A" + to);
                        worksheet.getCell("A" + from).value = w.user ? w.user.name : "";
                        worksheet.getCell("A" + from).border = {top: {style:'thin'},left: {style:'thin'},bottom: {style:'thin'},right: {style:'thin'}};

                        //Submitted
                        worksheet.mergeCells("B" + from + ":" + "B" + to);
                        worksheet.getCell("B" + from).value = count_expense;
                        worksheet.getCell("B" + from).border = {top: {style:'thin'},left: {style:'thin'},bottom: {style:'thin'},right: {style:'thin'}};

                        //Date
                        worksheet.mergeCells("C" + from + ":" + "C" + to);
                        worksheet.getCell("C" + from).value = moment(w.created_at).format('ll');
                        worksheet.getCell("C" + from).border = {top: {style:'thin'},left: {style:'thin'},bottom: {style:'thin'},right: {style:'thin'}};

                        //Expense and Amount
                        total_amount_expense = 0; 
                        var expense = from;
                        w.expenses_model.forEach(function(e){

                            worksheet.getCell("D" + expense).value = e.expenses_type ? e.expenses_type.name : "";
                            worksheet.getCell("D" + expense).border = {top: {style:'thin'},left: {style:'thin'},bottom: {style:'thin'},right: {style:'thin'}};

                            worksheet.getCell("E" + expense).value = e.amount;
                            worksheet.getCell("E" + expense).border = {top: {style:'thin'},left: {style:'thin'},bottom: {style:'thin'},right: {style:'thin'}};

                            expense++;

                            total_amount_expense += e.amount;
                        });

                        //Total
                        worksheet.mergeCells("F" + from + ":" + "F" + to);
                        worksheet.getCell("F" + from).value = total_amount_expense;
                        worksheet.getCell("F" + from).border = {top: {style:'thin'},left: {style:'thin'},bottom: {style:'thin'},right: {style:'thin'}};

                        from = to + 1;

                        total_submitted += count_expense;
                        total_grand_amount += total_amount_expense;
                        total_grand_amount_expense += total_amount_expense;
                    }
                }

            });

            from = to + 1;
            to = from;

            worksheet.getCell("A" + from).value = "Total";
            worksheet.getCell("A" + from).border = {top: {style:'thin'},left: {style:'thin'},bottom: {style:'thin'},right: {style:'thin'}};

            worksheet.getCell("B" + from).value = total_submitted;
            worksheet.getCell("B" + from).border = {top: {style:'thin'},left: {style:'thin'},bottom: {style:'thin'},right: {style:'thin'}};

            worksheet.getCell("C" + from).border = {top: {style:'thin'},left: {style:'thin'},bottom: {style:'thin'},right: {style:'thin'}};
            worksheet.getCell("D" + from).border = {top: {style:'thin'},left: {style:'thin'},bottom: {style:'thin'},right: {style:'thin'}};

            worksheet.getCell("E" + from).value = total_grand_amount;
            worksheet.getCell("E" + from).border = {top: {style:'thin'},left: {style:'thin'},bottom: {style:'thin'},right: {style:'thin'}};

            worksheet.getCell("F" + from).value = total_grand_amount_expense;
            worksheet.getCell("F" + from).border = {top: {style:'thin'},left: {style:'thin'},bottom: {style:'thin'},right: {style:'thin'}};
                
        
            workbook.xlsx.writeBuffer()
            .then(buffer => FileSaver.saveAs(new Blob([buffer]), `ExpenseReport.xlsx`))
            .catch(err => console.log('Error writing excel export', err));
                
        },


        fetchExpenses(){
            axios.post('/expense-report-bydate', {
                startDate: this.startDate,
                endDate: this.endDate
            })
            .then(response => {
                this.expenses = response.data;
                this.exportExcel = false;
                this.errors = []; 
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            })
        },
        fetchExpenseByTsr(id,name,created){
            axios.get(`/expense-report/${id}`)
            .then(response => { 
                this.expenseByTsr = response.data;
                this.tsrName = name;
                this.date = created;
                $('#viewModal').modal('show');
            })
            .catch(error => {
                this.errors = error.response.data.errors;
            })
        },
        setPage(pageNumber) {
            this.currentPage = pageNumber;
        },

        resetStartRow() {
            this.currentPage = 0;
        },

        showPreviousLink() {
            return this.currentPage == 0 ? false : true;
        },

        showNextLink() {
            return this.currentPage == (this.totalPages - 1) ? false : true;
        }
    },
    computed:{
        filteredExpenses(){
            let self = this;
            return self.expenses.filter(expense => {
                if(expense.user){
                    return expense.user.name.toLowerCase().includes(this.keywords.toLowerCase())
                }
            });
        },
        totalPages() {
            return Math.ceil(this.filteredExpenses.length / this.itemsPerPage)
        },
        filteredQueues() {
            var index = this.currentPage * this.itemsPerPage;
            var queues_array = this.filteredExpenses.slice(index, index + this.itemsPerPage);

            if(this.currentPage >= this.totalPages) {
                this.currentPage = this.totalPages - 1
            }

            if(this.currentPage == -1) {
                this.currentPage = 0;
            }

            return queues_array;
        },
        imageLink(){
            return window.location.origin+'/storage/';
        }
    },
}
</script>

<style>

</style>
