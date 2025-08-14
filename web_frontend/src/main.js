// src/main.js
import './assets/main.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import axios from 'axios';

import App from './App.vue';
import router from './router';

// Set the base URL for all Axios requests
axios.defaults.baseURL = 'http://127.0.0.1:8000/api'; // Change this to your backend URL

// Add a request interceptor to automatically attach the Authorization header
axios.interceptors.request.use(
  config => {
    // Retrieve 'authToken' from localStorage
    const token = localStorage.getItem('authToken');
    if (token) {
      // Attach the token as a Bearer token
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  error => {
    return Promise.reject(error);
  }
);

// Add a response interceptor to handle 401 Unauthorized errors globally
axios.interceptors.response.use(
  response => response,
  error => {
    if (error.response && error.response.status === 401) {
      // Clear token and redirect to login on 401 error
      console.error('Unauthorized request. Redirecting to login.');
      localStorage.removeItem('authToken');
      delete axios.defaults.headers.common['Authorization'];
      router.push('/login');
    }
    return Promise.reject(error);
  }
);

const app = createApp(App);
app.use(createPinia());
app.use(router);

app.mount('#app');
