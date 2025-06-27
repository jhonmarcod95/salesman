<template>
      <div>
        <loader v-if="loading"></loader>
        <div class="header bg-green pb-6 pt-5 pt-md-6"></div>
        <div class="container-fluid m-2">
            <a class="btn btn-sm btn-primary" href='/auto-posting/LFUG'>Auto Posting LFUG</a>
            <a class="btn btn-sm btn-primary" href='/auto-posting/HANA'>Auto Posting HANA</a>
            <a class="btn btn-sm btn-primary" href='/auto-posting-reprocessing/LFUG'>Auto Posting Reprocessing LFUG</a>
            <a class="btn btn-sm btn-primary" href='/auto-posting-reprocessing/HANA'>Auto Posting HANA</a>
            <a class="btn btn-sm btn-primary" href='/auto-cv'>Auto CV</a>
            <a class="btn btn-sm btn-primary" href='//auto-check'>Auto Check</a>
        </div>
        
    </div>
</template>
<script>
import moment from 'moment';
import loader from '../Loader';
import Swal from 'sweetalert2';

export default {
    components: { loader },
    data(){
        return{
            currentIndex: '',
            expenses: [],
            expenses_id: [],
            expenseByTsr: [],
            weeks: [],
            current_week: '',
            year: '',
            week: '',
            startDate: '',
            endDate: '',
            tsrName: '',
            date: '',
            submit: '',
            companies: [],
            company: '',
            errors: [],
            keywords: '',
            currentPage: 0,
            itemsPerPage: 10,
            loading: false,
            errorDetails: []
        }
    },
    created(){
        this.fetchCompanies();
    },
    methods:{
        moment,
        noImage(event){
            event.target.src = window.location.origin+'/img/brand/no-image.png';
        },
        fetchCompanies(){
            axios.get('/companies-all')
            .then(response => { 
                this.companies = response.data;
            })
            .catch(error => { 
                this.errors = error.response.data.errors;
            })
        },
        getExpenseSubmitted(index,name,expenses,errors){
            this.currentIndex = index;
            this.tsrName = name? name: this.filteredQueues[index].user.name;
            this.expenseByTsr = expenses? expenses: this.filteredQueues[index].user.expenses;
            this.errorDetails = errors? errors: this.filteredQueues[index].payment_header_detail_error;
        },
        deleteExpense(target){
            Swal.fire({
                title: "Delete Expense?",
                text: 'Delete expense type ' + target.expenses_type.name + ' with amount PHP ' + target.amount + '?',
                showCancelButton: true,
                showConfirmButton: true,
                confirmButtonColor: "#e24444",
                confirmButtonText: "Delete",
            })
            .then(response => {
                if (response.isConfirmed) {
                    axios.delete(`/expense-unposted-delete/${target.id}`)
                    .then(response => {
                        this.fetchExpenses();
                    });
                    // .catch(error => { 
                    //     this.errors = error.response.data.errors;
                    // });
                }
            });
        },
        cancelDeletion(target){
            Swal.fire({
                title: "Restore Expense?",
                text: 'Restore expense type ' + target.expenses_type.name + ' with amount PHP ' + target.amount + '?',
                showCancelButton: true,
                showConfirmButton: true,
                confirmButtonText: "Restore",
            })
            .then(response => {
                if (response.isConfirmed) {
                    axios.delete(`/expense-unposted-restore/${target.id}`)
                    .then(response => {
                        this.fetchExpenses();
                    })
                    // .catch(error => { 
                    //     this.errors = error.response.data.errors;
                    // });
                }
            });
        },
        fetchExpenses(){
            this.loading = true;
            var dates = this.week.split('-');
            var date1 = dates[0];
            var date2= dates[1];
            
            var moment1 = moment(date1);
            var moment2 = moment(date2);
            this.startDate = moment1.format('YYYY-MM-DD');
            this.endDate = moment2.format('YYYY-MM-DD');
            axios.post('/expense-unposteds', {
                startDate: this.startDate,
                endDate: this.endDate,
                company: this.company
            })
            .then(response => {
                this.expenses = response.data;
                this.errors = [];
                this.loading = false;
                //updates the view modal if open
                if (this.currentIndex || this.currentIndex == 0) this.getExpenseSubmitted(this.currentIndex);
            })
            .catch(error => {
                this.errors = error.response.data.errors;
                this.loading = false;
            })
        },
        getyear(){
            var start = moment(this.year).day('Monday');
            var end   = moment();
            var day   = 1;

            var result = [];
            var current = start.clone();
            result.push(moment(start).format('ll') +' - ' + moment(start.add(6, 'days')).format('ll'));
            while (current.day(7 + day).isBefore(end)) {
                result.push(moment(current.clone()).format('ll') +' - ' + moment(current.clone().add(6, 'days')).format('ll'));
            }
            this.weeks = result;
            this.current_week = this.weeks[this.weeks.length - 1];
        },
        countExpenseSubmitted(expenses){
            var totalSubmitted = 0;
            expenses.forEach(element => {
              totalSubmitted = totalSubmitted + element.expenses_model_count;
            });
            return totalSubmitted;
        },
        countTotalExpenses(expenses){
            var totalExpenses = 0;
            expenses.forEach(element => {
                totalExpenses = totalExpenses + element.amount;
            });
            return totalExpenses.toFixed(2);
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
            return Object.values(self.expenses).filter(expense => {
                return expense.user.name.toLowerCase().includes(this.keywords.toLowerCase())
            });
        },
        totalPages() {
            return Math.ceil(Object.values(this.filteredExpenses).length / this.itemsPerPage)
        },
        filteredQueues() {
            var index = this.currentPage * this.itemsPerPage;
            var queues_array = Object.values(this.filteredExpenses).slice(index, index + this.itemsPerPage);

            if(this.currentPage >= this.totalPages) {
                this.currentPage = this.totalPages - 1
            }

            if(this.currentPage == -1) {
                this.currentPage = 0;
            }

            return queues_array;
        },
        years () {
            const year = new Date().getFullYear()
            return Array.from({length: year - 2018}, (value, index) => 2019 + index)
        },
        imageLink(){
            return window.location.origin+'/storage/';
        }
    },
}
</script>
