import { createRouter, createWebHistory } from 'vue-router';

import Register from './Auth/Register.vue';
import Login from './Auth/Login.vue';
import Dashboard from './views/Dashboard.vue';
import RequestLeave from './views/RequestLeave.vue';
import History from './views/History.vue';


const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/register', component: Register },
    { path: '/login', component: Login },
    { path: '/', component: Dashboard },
    { path: '/request-leave', component: RequestLeave},
    { path: '/history', component: History },
  ],
});

export default router;
