import { createRouter, createWebHistory } from 'vue-router';

import Register from './Auth/Register.vue';
import Login from './Auth/Login.vue';


const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/register', component: Register },
    { path: '/login', component: Login },
  ],
});

export default router;
