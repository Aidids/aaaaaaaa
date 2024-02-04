import axios from 'axios';

const $api = axios.create({
    withCredentials: true,
});

$api.interceptors.request.use((config) => {
    config.headers['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    config.headers.Authorization = `Bearer ${localStorage.getItem('token')}`;
    config.headers['Content-Type'] = 'application/json';
    return config;
});

export default $api;