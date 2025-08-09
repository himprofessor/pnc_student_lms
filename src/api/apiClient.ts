// apiClient.ts
import axios from 'axios';

const apiClient = axios.create({
  baseURL: 'http://10.193.247.163:8080', // ✅ Use your real IP here
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
  withCredentials: true, // if using cookies
});

export default apiClient;
