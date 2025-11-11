// src/services/ApiService.js
import axios from 'axios';

const ApiService = axios.create({
  baseURL: 'http://127.0.0.1:8000/api/', 
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
});

const token = localStorage.getItem('token');
if (token) {
  ApiService.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

export default ApiService;