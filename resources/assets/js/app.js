

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

require('./mixins.js'); // Global reusable functions

import Multiselect from 'vue-multiselect';
Vue.component('multiselect', Multiselect);

import HighchartsVue from 'highcharts-vue'
Vue.use(HighchartsVue)

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

// Vue.component('example-component', require('./components/ExampleComponent.vue'));

// SapUser
Vue.component('sap-user-index', require('./components/SapUser/SapUserIndex.vue'));

// Schedule
Vue.component('request-schedule-index', require('./components/Schedule/RequestScheduleIndex.vue'));

Vue.component('missed-itineraries', require('./components/Schedule/MissedItineraries.vue'));

Vue.component('change-planned-schedules', require('./components/Schedule/ChangePlannedSchedules.vue'));

Vue.component('virtual-schedule-report', require('./components/Schedule/VirtualScheduleReport.vue'));

// Payment
Vue.component('payment-index', require('./components/Payment/PaymentIndex.vue'));

// Payment Posted
Vue.component('payment-posted-index', require('./components/PaymentPosted/PaymentPostedIndex.vue'));
// Payment Unposted
Vue.component('payment-unposted-index', require('./components/PaymentUnposted/PaymentUnpostedIndex.vue'));

// Companeies
Vue.component('company-index', require('./components/Company/CompanyIndex.vue'));

// Expenses
Vue.component('expense-index', require('./components/Expense/ExpenseIndex.vue'));
// Vue.component('expense-report-index', require('./components/Expense/ExpenseReportIndex.vue'));


Vue.component('expense-report-index', require('./components/Expense/ExpenseReportIndexV2.vue'));
Vue.component('expense-submitted-index', require('./components/Expense/ExpenseSubmittedIndex.vue'));

Vue.component('historical-expense-index', require('./components/Expense/HistoricalExpense.vue'));

Vue.component('expense-top-spender', require('./components/Expense/ExpenseTopSpender.vue'));
Vue.component('dms-received-expense', require('./components/Expense/ExpenseDmsReceivedIndex.vue'));

// Announcement
Vue.component('announcement-index', require('./components/Announcement/AnnouncementIndex.vue'));

// User
Vue.component('user-index', require('./components/User/UserIndex.vue'));
Vue.component('user-form', require('./components/User/UserForm.vue'));
Vue.component('user-edit-form', require('./components/User/UserEditForm.vue'));
Vue.component('user-change-password-index', require('./components/User/UserChangePasswordIndex.vue'));

// Customer
Vue.component('customer-index', require('./components/Customer/CustomerIndex.vue'));
Vue.component('customer-form', require('./components/Customer/CustomerForm.vue'));
Vue.component('customer-edit-form', require('./components/Customer/CustomerEditForm.vue'));
Vue.component('customer-visited', require('./components/Customer/CustomerVisited.vue'));

// Tsr
Vue.component('tsr-index', require('./components/Tsr/TsrIndex.vue'));
Vue.component('tsr-form', require('./components/Tsr/TsrForm.vue'));
Vue.component('tsr-edit-form', require('./components/Tsr/TsrEditForm.vue'));

// Message
Vue.component('message-index', require('./components/Message/MessageIndex.vue'));

// Attendance Report
Vue.component('attendance-report-index', require('./components/AttendanceReport/AttendanceReportIndex.vue'));

// Dashboard
Vue.component('dashboard-index', require('./components/Dashboard/DashboardIndex.vue'));

Vue.component('dashboard', require('./components/Dashboard/Dashboard.vue'));

// Loader
Vue.component('loader', require('./components/Loader.vue'));

// Internal Order
Vue.component('internal-order-index', require('./components/InternalOrder/InternalOrderIndex.vue'));

//Close Visit Index
Vue.component('close-visit', require('./components/CloseVisit/Index.vue'));

// Map Analytics Report
Vue.component('map-analytics-report-index', require('./components/MapAnalyticsReport/MapAnalyticsReportIndex.vue'));
Vue.component('map-analytics-report-map-users', require('./components/MapAnalyticsReport/MapAnalyticsReportMapUsers.vue'));
Vue.component('map-analytics-report-map-customers', require('./components/MapAnalyticsReport/MapAnalyticsReportMapCustomers.vue'));


//Sales Report 
Vue.component('sales-report-index', require('./components/SalesReport/SalesReportIndex.vue'));

//Appointment Duration Report
Vue.component('appointment-duration-report-index', require('./components/AppointmentDurationReport/AppointmentDurationReportIndex.vue'));

// Survey Report Component
Vue.component('survey-index', require('./components/Survey/SurveyIndex.vue'));

// Survey Display List
Vue.component('survey-display', require('./components/Survey/CreatedSurvey.vue'));

// Web Based survey
Vue.component('survey-start', require('./components/Survey/SurveyStart.vue'));

// Individual Performance
Vue.component('individual-performance', require('./components/IndividualPerformance/IndividualPerformance.vue'));

// Expense per IO
Vue.component('expense-io-report-index', require('./components/Expense/ExpenseIoReportIndex.vue'));

// AAPC Farmer meeting
Vue.component('aapc-farmer-create', require('./components/AapcFarmer/AapcFarmerCreate.vue'));

Vue.component('aapc-farmer-index', require('./components/AapcFarmer/AapcFarmerIndex.vue'));

//Common
Vue.component('table-pagination', require('./components/Common/TablePagination.vue'));
Vue.component('app-block-ui', require('./components/Common/BlockUi.vue'));
Vue.component('app-select', require('./components/Common/Select2.vue'));
Vue.component('error-messages', require('./components/Common/ErrorMessage.vue'));

const app = new Vue({
    el: '#app',
});
