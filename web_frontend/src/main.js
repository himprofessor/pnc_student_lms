 // src/main.js
import './assets/main.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import axios from 'axios';

import App from './App.vue';
import router from './router';
import axios from "@/axios";

const { data } = await axios.get("/profile");





// Set the base URL for Axios

axios.defaults.baseURL = 'http://127.0.0.1:8000/api'; // Your API base URL

// ****** CRITICAL ADDITION: Axios Request Interceptor for Authorization header ******
axios.interceptors.request.use(
  config => {
    // Retrieve 'authToken' from localStorage (consistent with Login.vue now)
    const token = localStorage.getItem('authToken');
    if (token) {
      // Attach the token as a Bearer token in the Authorization header
      config.headers.Authorization = `Bearer ${token}`;
      console.log('Axios Interceptor: Attaching Authorization header for request to', config.url); // Debugging
    }
    return config;
  },
  error => {
    return Promise.reject(error);
  }
);

// Optional but Recommended: Axios Response Interceptor for global 401 handling
axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response && error.response.status === 401) {
      console.error('Axios Response Interceptor: 401 Unauthorized. Clearing token and redirecting to login.');
      localStorage.removeItem('authToken'); // Clear token on 401
      delete axios.defaults.headers.common['Authorization']; // Remove default header
      router.push('/login'); // Redirect to login page
      // Optionally, display a user-friendly message
      // alert('Your session has expired or you are unauthorized. Please log in again.');
    }
    return Promise.reject(error);
  }
);


const app = createApp(App);

// app.config.globalProperties.$axios = axios; // You generally don't need this if you import axios directly in components

app.use(createPinia());
app.use(router);

app.mount('#app');