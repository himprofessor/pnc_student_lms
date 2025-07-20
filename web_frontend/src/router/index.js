import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/HomeView.vue'
import Login from '../views/Auth/Login.vue'
import Register from '../views/Auth/Register.vue'
import About from '../views/AboutView.vue'
 

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/', component: Home },
    { path: '/login', component: Login },
    { path: '/register', component: Register },
    { path: '/about', component: About },
    {
    
  },
  ]
})

export default router
