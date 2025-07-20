<<<<<<< HEAD
import './assets/main.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import axios from 'axios';

import App from './App.vue';
import router from './router';
=======
import './assets/main.css'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
>>>>>>> feat/view_history

// Set the base URL for Axios
axios.defaults.baseURL = 'http://127.0.0.1:8000/api'; // Your API base URL

const app = createApp(App);

app.config.globalProperties.$axios = axios; // Make Axios available globally

app.use(createPinia());
app.use(router);

app.mount('#app');