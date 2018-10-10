
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

// Customer
Vue.component('customer-index', require('./components/Customer/CustomerIndex.vue'));
Vue.component('customer-form', require('./components/Customer/CustomerForm.vue'));
Vue.component('customer-edit-form', require('./components/Customer/CustomerEditForm.vue'));

// Tsr
Vue.component('tsr-index', require('./components/Tsr/TsrIndex.vue'));
Vue.component('tsr-form', require('./components/Tsr/TsrForm.vue'));
Vue.component('tsr-edit-form', require('./components/Tsr/TsrEditForm.vue'));

const app = new Vue({
    el: '#app'
});
