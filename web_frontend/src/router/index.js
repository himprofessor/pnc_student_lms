import { createRouter, createWebHistory } from 'vue-router'
import axios from 'axios'

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
  { path: '/dashboard', component: DashboardPage, meta: { requiresAuth: true } },
  { path: '/request-leave', component: RequestLeavePage, meta: { requiresAuth: true } },
  { path: '/history', component: HistoryPage, meta: { requiresAuth: true } },
  { path: '/profile', component: ProfilePage, meta: { requiresAuth: true } },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

router.beforeEach(async (to, from, next) => {
  const token = localStorage.getItem('token')

  if (token) {
    // Set Axios header on navigation (for fresh loads)
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
  }

  const authRequired = to.meta.requiresAuth

  if (authRequired && !token) {
    console.log('No token found, redirecting to login')
    return next('/login')
  }

  if (authRequired && token) {
    const storedUser = localStorage.getItem('user_data')
    if (!storedUser) {
      try {
        const response = await axios.get('http://localhost:8000/api/user')
        localStorage.setItem('user_data', JSON.stringify(response.data))
        console.log('Fetched user data in router guard', response.data)
      } catch (err) {
        console.error('Failed to fetch user:', err)
        if (err.response?.status === 401) {
          localStorage.removeItem('token')
          localStorage.removeItem('user_data')
          return next('/login')
        }
      }
    }
  }

  next()
})

export default router
