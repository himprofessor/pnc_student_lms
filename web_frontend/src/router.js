import { createRouter, createWebHistory } from 'vue-router';

import Register from './Auth/Register.vue';
import Login from './Auth/Login.vue';
import DashboardPage from './views/DashboardPage.vue';
import RequestLeavePage from './views/RequestLeavePage.vue';
import HistoryPage from './views/HistoryPage.vue';
import ProfilePage from './views/ProfilePage.vue';

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/register', component: Register },
    { path: '/login', component: Login },
    { path: '/dashboard', component: DashboardPage },
    { path: '/request-leave', component: RequestLeavePage },
    { path: '/history', component: HistoryPage },
    { path: '/profile', component: ProfilePage },

  ],
});

export default router;
