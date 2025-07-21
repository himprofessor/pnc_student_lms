import { createRouter, createWebHistory } from 'vue-router'

import Home from '../views/HomeView.vue'
import Login from '../views/Auth/Login.vue'
import Register from '../views/Auth/Register.vue'

import DashboardPage from '../views/DashboardPage.vue'
import RequestLeavePage from '../views/RequestLeavePage.vue'
import HistoryPage from '../views/HistoryPage.vue'
import ProfilePage from '../views/ProfilePage.vue'

const routes = [
  { path: '/', component: Home, meta: { hideNavbar: true } },
  { path: '/login', component: Login, meta: { hideNavbar: true } },
  { path: '/register', component: Register, meta: { hideNavbar: true } },
  { path: '/dashboard', component: DashboardPage },
  { path: '/request-leave', component: RequestLeavePage },
  { path: '/history', component: HistoryPage },
  { path: '/profile', component: ProfilePage }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

// Global navigation guard
router.beforeEach((to, from, next) => {
  const publicPages = ['/', '/login', '/register']
  const authRequired = !publicPages.includes(to.path)
  const token = localStorage.getItem('authToken') // Make sure you're using the same key used in login

  if (authRequired && !token) {
    return next('/login')
  }

  next()
})

export default router