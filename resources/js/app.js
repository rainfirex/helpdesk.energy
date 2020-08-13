import VueRouter from 'vue-router';
import Application from "./vue/Application";
import router from './vue/routers'
import Vuex from 'vuex';
import Vuelidate from 'vuelidate'
import state from "./vue/store/state";
import getters from "./vue/store/getters";
import mutations from "./vue/store/mutations";

import './vue/assets/scss/main.scss';

require('./bootstrap');
window.Vue = require('vue');

Vue.use(VueRouter);
Vue.use(Vuex);
Vue.use(Vuelidate);

const store = new Vuex.Store({state: state, getters: getters, mutations: mutations });
window.store = store;

const app = new Vue({
    el: '#app',
    components: {},
    render : h => h(Application),
    store,
    router
});
