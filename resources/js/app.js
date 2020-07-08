import VueRouter from 'vue-router';
import Application from "./vue/Application";
import router from './vue/routers'

import Vuex from 'vuex';
import state from "./vue/store/state";
import getters from "./vue/store/getters";
import mutations from "./vue/store/mutations"

require('./bootstrap');
window.Vue = require('vue');
Vue.use(VueRouter);
Vue.use(Vuex);

const store = new Vuex.Store({state: state, getters: getters, mutations: mutations });
window.store = store;

const app = new Vue({
    el: '#app',
    components: {},
    render : h => h(Application),
    store,
    router
});
