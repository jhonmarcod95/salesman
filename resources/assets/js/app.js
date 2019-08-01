
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

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
Vue.component('expense-report-index', require('./components/Expense/ExpenseReportIndex.vue'));
Vue.component('expense-submitted-index', require('./components/Expense/ExpenseSubmittedIndex.vue'));

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

// Loader
Vue.component('loader', require('./components/Loader.vue'));

// Internal Order
Vue.component('internal-order-index', require('./components/InternalOrder/InternalOrderIndex.vue'));

const app = new Vue({
    el: '#app',
});
