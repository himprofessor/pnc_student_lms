import './assets/main.css';
<<<<<<< HEAD


import { createApp } from 'vue'
import { createPinia } from 'pinia'
=======
>>>>>>> 4c7df4aec0fdac2788799808f3bd67f4da808222

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import axios from 'axios';

import App from './App.vue';
import router from './router';

// Set the base URL for Axios
axios.defaults.baseURL = 'http://127.0.0.1:8000/api'; // Your API base URL

const app = createApp(App);

app.config.globalProperties.$axios = axios; // Make Axios available globally

app.use(createPinia());
app.use(router);

app.mount('#app');