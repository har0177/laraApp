/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require("./bootstrap");

window.Vue = require("vue");
import moment from "moment"; // Moment package for date 
import VueRouter from "vue-router"; // for routing href
import VueProgressBar from "vue-progressbar"; // progressbar
import swal from "sweetalert2"; // Seetlaerts

import { Form, HasError, AlertError } from "vform"; // form validation
import Gate from './Gate';
Vue.prototype.$gate = new Gate(window.user);
window.Form = Form;
window.swal = swal;

const toast = swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    onOpen: (toast) => {
        toast.addEventListener('mouseenter', swal.stopTimer)
        toast.addEventListener('mouseleave', swal.resumeTimer)
    }
});

window.toast = toast;

Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);



Vue.use(VueRouter);

let routes = [{
        path: "/dashboard",
        component: require("./components/Dashboard.vue")
    },
    {
        path: "/profile",
        component: require("./components/Profile.vue")
    },
    {
        path: "/users",
        component: require("./components/Users.vue")
    },
    {
        path: "/products",
        component: require("./components/Products.vue")
    },
    {
        path: "/customers",
        component: require("./components/Customers.vue")
    },
    {
        path: "/vendors",
        component: require("./components/Vendors.vue")
    },
    {
        path: "/category",
        component: require("./components/Category.vue")
    },
    {
        path: "/developer",
        component: require("./components/Developer.vue")
    },
    {
        path: "*",
        component: require("./components/NotFound.vue")
    }
];

const router = new VueRouter({
    mode: "history",
    routes // short for `routes: routes`
});

Vue.filter("upText", function(text) {
    return text.charAt(0).toUpperCase() + text.slice(1);
});

Vue.filter("myDate", function(created) {
    return moment().format("MMMM DD YYYY, h:mm:ss a");
});

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.use(VueProgressBar, {
    color: 'rgb(143, 255, 199)',
    failedColor: 'red',
    height: '3px'
});

let Fire = new Vue();
window.Fire = Fire;

Vue.component(
    'passport-clients',
    require('./components/passport/Clients.vue')
);

Vue.component(
    'passport-authorized-clients',
    require('./components/passport/AuthorizedClients.vue')
);

Vue.component('dashbaord', require("./components/Dashboard.vue"));


Vue.component('select2', require('./components/Select2.vue'));

Vue.component(
    'passport-personal-access-tokens',
    require('./components/passport/PersonalAccessTokens.vue')
);

Vue.component(
    'not-found',
    require('./components/NotFound.vue')
);

Vue.component('pagination', require('laravel-vue-pagination'));

Vue.component(
    "example-component",
    require("./components/ExampleComponent.vue")
);

const app = new Vue({
    el: "#app",
    router,
    data: {
        search: ''
    },
    methods: {
        searchit: _.debounce(() => {
            Fire.$emit('searching');
        }, 1000)
    }
});