import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router/index.js'

// ✅ import and create pinia
import { createPinia } from 'pinia'

const app = createApp(App)

// ✅ use pinia
const pinia = createPinia()
app.use(pinia)

// ✅ use router
app.use(router)

app.mount('#app')
