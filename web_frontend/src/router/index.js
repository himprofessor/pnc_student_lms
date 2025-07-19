// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import Dashboard from '../views/Dashboard.vue'
import RequestLeave from '../views/RequestLeave.vue'
import History from '../views/History.vue'

const routes = [
  { path: '/', name: 'Dashboard', component: Dashboard },
  { path: '/request-leave', name: 'RequestLeave', component: RequestLeave },
  { path: '/history', name: 'History', component: History },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

export default router
