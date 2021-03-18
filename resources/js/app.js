import {req} from "vuelidate/lib/validators/common";

window.Vue = require('vue')
window.moment = require('moment')

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.fileDownload = require('js-file-download');

import Vue from 'vue'

Vue.config.productionTip = false

import VueRouter from 'vue-router'
import VueToast from 'vue-toast-notification'
import 'vue-toast-notification/dist/theme-default.css'
import Vuelidate from 'vuelidate'
import router from './router'
import store from './store'
import VueProgressBar from 'vue-progressbar'

import App from './views/App'

import Loader from "./components/Loader"

import ShortNameFilter from "./filters/shortName.filter"
import ShortDescriptionFilter from "./filters/shortDescription.filter"

Vue.use(VueRouter)
Vue.use(VueToast)
Vue.use(Vuelidate)
Vue.use(VueProgressBar, {
    color: '#007bff',
    failedColor: '#dc3545',
    thickness: '3px',
    transition: {
        speed: '1.5s',
        opacity: '0.6s',
        termination: 400
    },
    autoRevert: true,
    location: 'top',
    inverse: false
})

Vue.component('loader', Loader)
Vue.component('pagination', require('laravel-vue-pagination'))

Vue.filter('shortName' ,ShortNameFilter)
Vue.filter('shortDescription' ,ShortDescriptionFilter)

require('./store/subscriber')

store.dispatch('auth/attempt', localStorage.getItem('token')).then(() => {
    new Vue({
        store,
        router,
        components: { App },
        mounted() {
            axios.interceptors.request.use(
                response => {
                    this.$Progress.start()
                    window.$loading = true
                    return response
                },
                error => {
                    this.$Progress.fail()
                    return Promise.reject(error);
                }
            )
            axios.interceptors.response.use(
                response => {
                    this.$Progress.finish()
                    window.$loading = false
                    return response
                },
                error => {
                    this.$Progress.fail()
                    return Promise.reject(error);
                }
            )
        }
    }).$mount('#app')
})




