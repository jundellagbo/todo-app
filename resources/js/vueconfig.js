import Vue from "vue";
import VueRouter from "vue-router";
import VuetifyConfirm from 'vuetify-confirm'
import routes from "./router";
import App from "./App.vue";
import DatetimePicker from 'vuetify-datetime-picker'
import 'typeface-roboto'
import 'material-design-icons-iconfont/dist/material-design-icons.css'
import 'vuetify/dist/vuetify.min.css'
import 'vuetify-datetime-picker/src/stylus/main.styl'
import moment from "vue-moment"
import {user} from "./config"
import MultiFiltersPlugin from "./MultiFiltersPlugin"
/**
 * Themes
 */

import Vuetify from "vuetify"


Vue.use( Vuetify, {
    theme: {
        primary: '#3f51b5',
        secondary: '#b0bec5',
        accent: '#8c9eff',
        error: '#b71c1c'
    },
    iconfont: 'md'
})

Vue.use( VueRouter )
Vue.use(VuetifyConfirm)
Vue.use(DatetimePicker)
Vue.use(moment)
Vue.use(MultiFiltersPlugin);

const router = new VueRouter({
    mode: "history",
    routes
})

router.beforeEach((to, from, next) => {
    if( to.matched.some( record => record.meta.requiresAuth ) ) {
        if( !user ) {
            next("/login");
        } else {
            next();
        }
    }
    else {
        if( (to.path == "/login" || to.path == "/register") && user ) {
            next("/");
        } else {
            next();
        }
    }
    next();
});

new Vue({
    el: '#app',
    router,
    render: h => h(App)
})
