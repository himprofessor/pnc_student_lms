import { createRouter, createWebHistory } from 'vue-router'
import axios from 'axios'

import Home from '@/views/student/HomeView.vue'
import Login from '@/views/Auth/Login.vue'
import Register from '@/views/Auth/Register.vue'
import DashboardPage from '@/views/student/DashboardPage.vue'
import RequestLeavePage from '@/views/student/RequestLeavePage.vue'
import HistoryPage from '@/views/student/HistoryPage.vue'
import ProfilePage from '@/views/student/ProfilePage.vue'
import CalendarPage from '@/views/student/CalendarPage.vue'

import EducatorDashboard from '@/views/educator/EducatorDashboard.vue'
import EducatorHistory from '@/views/educator/EducatorHistory.vue'
import EducatorProfile from '@/views/educator/EducatorProfile.vue'
import ImportStudent from '@/views/educator/ImprotData.vue'
import CreateAccountStudent from '@/views/educator/CreatAccount.vue'

const routes = [
  { path: '/', component: Home, meta: { hideStudentNavbar: true } },
  { path: '/login', component: Login, meta: { hideStudentNavbar: true } },
  { path: '/register', component: Register, meta: { hideStudentNavbar: true } },

  // Student routes
  { path: '/dashboard', component: DashboardPage, meta: { requiresAuth: true, role: 'student' } },
  { path: '/request-leave', component: RequestLeavePage, meta: { requiresAuth: true, role: 'student' } },
  { path: '/history', component: HistoryPage, meta: { requiresAuth: true, role: 'student' } },
  { path: '/profile', component: ProfilePage, meta: { requiresAuth: true, role: 'student' } },
  { path: '/calendar', component: CalendarPage, meta: { requiresAuth: true, role: 'student' } },

  // Teacher routes
  { path: '/educator-dashboard', component: EducatorDashboard, meta: { requiresAuth: true, role: 'teacher' } },
  { path: '/educator-history', component: EducatorHistory, meta: { requiresAuth: true, role: 'teacher' } },
  { path: '/educator-profile', component: EducatorProfile, meta: { requiresAuth: true, role: 'teacher'}},
  {
    // ⚠️ CORRECTED PATH: Must match the router-link's `to` attribute
    path: '/create-account',
    name: 'CreateAccount',
    component: CreateAccountStudent,
    // ⚠️ CORRECTED ROLE: The role is 'teacher', not 'educator'
    meta: { requiresAuth: true, role: 'teacher' }
  },
  {
    path: '/educator-importdata',
    component: ImportStudent,
    meta: { requiresAuth: true, role: 'teacher' }
  },
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('authToken')
  const role = localStorage.getItem('role')

  if (to.meta.requiresAuth && !token) {
    return next('/login')
  }

  if (to.meta.role && to.meta.role !== role) {
    // Corrected logic for redirection based on roles
    if (role === 'teacher' && to.path !== '/educator-dashboard') {
      return next('/educator-dashboard')
    } else if (role === 'student' && to.path !== '/dashboard') {
      return next('/dashboard')
    } else if (!role) {
      return next('/login')
    } else {
      // This handles cases where a logged-in user with a specific role tries to access
      // a page meant for a different role.
      return next(false)
    }
  }

  if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
  }

  next()
})

export default router