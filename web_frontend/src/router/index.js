import { createRouter, createWebHistory } from 'vue-router'
import axios from 'axios'

import Home from '../views/student/HomeView.vue'
import Login from '../views/Auth/Login.vue'
import Register from '../views/Auth/Register.vue'
import DashboardPage from '../views/student/DashboardPage.vue'
import RequestLeavePage from '../views/student/RequestLeavePage.vue'
import HistoryPage from '../views/student/HistoryPage.vue'
import ProfilePage from '../views/student/ProfilePage.vue'

import EducatorDashboard from '../views/educator/EducatorDashboard.vue'
import EducatorHistory from '../views/educator/EducatorHistory.vue'

const routes = [

  // User Authentication
  { path: '/', component: Home, meta: { hideStudentNavbar: true } },
  { path: '/login', component: Login, meta: { hideStudentNavbar: true } },
  { path: '/register', component: Register, meta: { hideStudentNavbar: true } },

  // Student
  { path: '/dashboard', component: DashboardPage, meta: { requiresAuth: true } },
  { path: '/request-leave', component: RequestLeavePage, meta: { requiresAuth: true } },
  { path: '/history', component: HistoryPage, meta: { requiresAuth: true } },
  { path: '/profile', component: ProfilePage, meta: { requiresAuth: true } },

  // Educator
  { path: '/educator-dashboard', component: EducatorDashboard },
  { path: '/educator-history', component: EducatorHistory}
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

router.beforeEach(async (to, from, next) => {
  const token = localStorage.getItem('authToken')

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