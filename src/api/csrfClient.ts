import axios from 'axios';

const csrfClient = axios.create({
  baseURL: 'http://192.168.108.43:8080', // Use your PC's IP address + port
  withCredentials: true,
});

export default csrfClient;
