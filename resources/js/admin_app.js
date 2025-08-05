import { createApp } from 'vue';
import CustomerApp from './AdminApp.vue';
import router from './admin_router';
import { createPinia } from 'pinia';
import 'bootstrap/dist/css/bootstrap.min.css';
import axios from 'axios';
import Toast from 'vue-toast-notification';
import 'bootstrap-icons/font/bootstrap-icons.css';
import { useNotification } from "@kyvg/vue3-notification";
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate';
import { io } from "socket.io-client";
import { setupSocketEventListeners } from '../js/EventHandler.js';
import { Chart as ChartJS } from 'chart.js';
import zoomPlugin from 'chartjs-plugin-zoom';
import ChartDataLabels from 'chartjs-plugin-datalabels';
// import '@fortawesome/fontawesome-free/css/all.css';
ChartJS.register(zoomPlugin, ChartDataLabels);


const socket = io("http://localhost:3001", {
  auth: {
    privateKey: "abc"
  }
});
setupSocketEventListeners(socket);
const app = createApp(CustomerApp);
app.use(useNotification, {
  position: 'top right',
  duration: 6000,
  pauseOnHover: true,
  closeOnClick: true,
  maxToasts: 5,
  newestOnTop: true
});
app.use(Toast);
axios.defaults.baseURL = ''; 
axios.defaults.headers.common['Accept'] = 'application/json';
const pinia = createPinia();
pinia.use(piniaPluginPersistedstate);
app.use(pinia);
app.use(router);
app.mount('#admin-app');
