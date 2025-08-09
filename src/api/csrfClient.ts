import axios from 'axios';

const csrfClient = axios.create({
  baseURL: 'http://10.193.247.163:8080', // Use your PC's IP address + port
  withCredentials: true,
});

export default csrfClient;
