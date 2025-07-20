import { createRouter, createWebHistory } from 'vue-router' 
import Home from '../views/HomeView.vue'
import Login from '../views/Auth/Login.vue'
import Register from '../views/Auth/Register.vue'

import DashboardPage from '../views/DashboardPage.vue'
import RequestLeavePage from '../views/RequestLeavePage.vue'
import HistoryPage from '../views/HistoryPage.vue'
import ProfilePage from '../views/ProfilePage.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/', component: Home, meta: { hideNavbar: true } },
    { path: '/login', component: Login, meta: { hideNavbar: true } },
    { path: '/register', component: Register, meta: { hideNavbar: true } },
    { path: '/dashboard', component: DashboardPage },
    { path: '/request-leave', component: RequestLeavePage },
    { path: '/history', component: HistoryPage },
    { path: '/profile', component: ProfilePage },
  ]
})

// Enhanced route guard with better user data handling
router.beforeEach(async (to, from, next) => {
  const publicPages = ['/login', '/register', '/']
  const authRequired = !publicPages.includes(to.path)
  const token = localStorage.getItem('token')

  if (authRequired && !token) {
    console.log('No token found, redirecting to login')
    return next('/login')
  }

  // If user is authenticated and going to a protected route
  if (authRequired && token) {
    // Check if we have user data, if not try to fetch it
    const userData = localStorage.getItem('user_data')
    if (!userData) {
      try {
        // Import api here to avoid circular dependency
        const { default: api } = await import('@/api.js')
        const response = await api.get('/user')
        localStorage.setItem('user_data', JSON.stringify(response.data))
        console.log('User data fetched in router guard:', response.data)
      } catch (error) {
        console.error('Failed to fetch user data in router guard:', error)
        if (error.response?.status === 401) {
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
