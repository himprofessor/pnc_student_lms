import './assets/main.css'
import Vue from 'vue';
import App from './App.vue';
import router from './router';
import axios from 'axios';

// Axios Instance
const http = axios.create({
  baseURL: 'http://127.0.0.1:8000/api',
  headers: {
    'Content-Type': 'application/json',
  },
});

Vue.prototype.$http = http;

Vue.config.productionTip = false;

new Vue({
  router,
  render: h => h(App),
}).$mount('#app');