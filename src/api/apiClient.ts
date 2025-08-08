// apiClient.ts
import axios from 'axios';

const apiClient = axios.create({
  baseURL: 'http://192.168.108.43:8080', // ✅ Use your real IP here
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
  withCredentials: true, // if using cookies
});

export default apiClient;
