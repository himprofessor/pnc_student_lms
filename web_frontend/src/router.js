import { createRouter, createWebHistory } from 'vue-router';

import Register from './Auth/Register.vue';
import Login from './Auth/Login.vue';
import DashboardPage from './views/DashboardPage.vue';
import HistoryPage from './views/HistoryPage.vue';
import RequestLeavePage from './views/RequestLeavePage.vue';


const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/register', component: Register },
    { path: '/login', component: Login },
    { path: '/', component: DashboardPage },
    { path: '/request-leave', component: RequestLeavePage},
    { path: '/history', component: HistoryPage },
  ],
});

export default router;
