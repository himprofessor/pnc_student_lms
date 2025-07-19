import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import ProfileSettings from '../views/ProfileSettings.vue' // ✅ import

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/about',
      name: 'about',
      component: () => import('../views/AboutView.vue'), // lazy load
    },
    {
      path: '/profile-settings',
      name: 'profile-settings',
      component: ProfileSettings, // ✅ add here
    },
  ],
})

export default router
