import Vue from 'vue';
import Vuex from 'vuex';

import app_setting from "./modules/app_setting";
import user from "./modules/user";
import linksMenu from "./modules/linksMenu";
import messenger from "./modules/messenger";
import notification from "./modules/notification";

Vue.use(Vuex);

export default new Vuex.Store(
    {
        modules: {
            app_setting,
            user,
            linksMenu,
            messenger,
            notification
        }
    });
