import VueRouter from 'vue-router';
import Vuelidate from 'vuelidate';
import App from "./app/App";
import router from './app/router/';
import store from './app/store';

import './app/assets/scss/main.scss';

require('./bootstrap');
window.Vue = require('vue');
window.store = store;

Vue.use(VueRouter);
Vue.use(Vuelidate);

const app = new Vue({
    el: '#app',
    store,
    router,
    render : h => h(App)
});
