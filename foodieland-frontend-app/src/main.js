// src/main.js
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import './style.css'

import Toast from 'vue-toastification'
import 'vue-toastification/dist/index.css'
const app = createApp(App)
const pinia = createPinia()

const options = {
    position: 'top-right',
    timeout: 5000,
    closeOnClick: true,
    pauseOnFocusLoss: true,
    pauseOnHover: true,
    draggable: true,
    draggablePercent: 0.6,
    showCloseButtonOnHover: false,
    hideProgressBar: false,
    closeButton: 'button',
    icon: true,
    rtl: false,
    transition: 'Vue-Toastification__bounce',
    maxToasts: 20,
    newestOnTop: true,
};

app.use(pinia)
app.use(Toast, options);
app.use(router)
app.mount('#app')