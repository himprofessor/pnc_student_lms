import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import ProfileSettings from '../views/ProfileSettings.vue' // ✅ Correct name

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/profile-settings',
      name: 'profile-settings',
      component: ProfileSettings, // ✅ Correct component
    },
  ],
})

export default router
