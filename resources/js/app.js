
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

// Vue.prototype.authorized = function(handler) {
//     let user = window.App.user;
//     return user ? handler(user) : false;
//     // return handler(window.App.user);
// };

let authorization = require('./authorization');

Vue.prototype.authorized = function(...params) {
    if(!this.signedIn) { return false;}
    if(typeof(params[0]) == 'string') {
        return authorization[params[0]](params[1]);
    }
    return params[0](window.App.user)
};

window.events =  new Vue();

Vue.prototype.signedIn = window.App.signedIn;

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

Vue.component('flash', require('./components/Flash.vue'));
Vue.component('paginator', require('./components/Paginator.vue'));
Vue.component('thread-view', require('./pages/Thread.vue'));
Vue.component('user-notifications', require('./components/userNotifications.vue'));
Vue.component('avatar-form', require('./components/AvatarForm.vue'));
// Vue.component('favorite', require('./components/Favotire.vue'));

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key)))

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const app = new Vue({
    el: '#app'
});