/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
require('./bootstrap');
import 'es6-promise/auto';
import Vuetify from 'vuetify';
import VueRouter from 'vue-router';
import Vuex from 'vuex'
import routes from './routes';
import store from './store';
import "./validate";
window.Vue = require('vue');
Vue.use(Vuetify);
Vue.use(VueRouter);
Vue.use(Vuex);

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

import movable from "v-movable"
Vue.use(movable)

import VueHtmlToPaper from 'vue-html-to-paper';

const options = {
    name: '_blank',
    specs: [
      'fullscreen=yes',
      'titlebar=yes',
      'scrollbars=yes'
    ],
    styles: [
        'css/app.css'
      ]
  }

Vue.use(VueHtmlToPaper, options);

Vue.component('welcome-component', require('./components/Welcome.vue').default);
Vue.component('login-component', require('./components/Login.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

new Vue({
    el: '#app',
    vuetify: new Vuetify({
        theme: {
            themes: {
                light: {
                    primary: '#0675BB',
                },
            },
        },
    }),
    router: new VueRouter(routes),
    store: new Vuex.Store(store),
});
