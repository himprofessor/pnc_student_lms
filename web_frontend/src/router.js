 import Vue from 'vue';
import Router from 'vue-router';
import Register from './Auth/Register.vue';
import Login from './Auth/Login.vue';

Vue.use(Router);

export default new Router({
  routes: [
    { path: '/register', component: Register },
    { path: '/login', component: Login },
  ],
});