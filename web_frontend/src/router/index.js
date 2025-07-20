<<<<<<< HEAD
import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
=======
import { createRouter, createWebHistory } from 'vue-router' 
import Home from '../views/HomeView.vue'
import Login from '../views/Auth/Login.vue'
import Register from '../views/Auth/Register.vue'

import DashboardPage from '../views/DashboardPage.vue'
import RequestLeavePage from '../views/RequestLeavePage.vue'
import HistoryPage from '../views/HistoryPage.vue'
import ProfilePage from '../views/ProfilePage.vue'
>>>>>>> 4c7df4aec0fdac2788799808f3bd67f4da808222

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
<<<<<<< HEAD
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/about',
      name: 'about',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../views/AboutView.vue'),
    },
  ],
=======
    { path: '/', component: Home },
    { path: '/login', component: Login, meta: { hideNavbar: true } },
    { path: '/register', component: Register, meta: { hideNavbar: true } },
    { path: '/dashboard', component: DashboardPage },
    { path: '/request-leave', component: RequestLeavePage },
    { path: '/history', component: HistoryPage },
    { path: '/profile', component: ProfilePage },
  ]
>>>>>>> 4c7df4aec0fdac2788799808f3bd67f4da808222
})

export default router
