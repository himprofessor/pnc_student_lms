import axios from "axios";

axios.defaults.baseURL = "http://localhost:8000/api";
axios.defaults.withCredentials = true;
axios.defaults.headers.common["Authorization"] =
  `Bearer ${localStorage.getItem("token")}`;

export default axios;
