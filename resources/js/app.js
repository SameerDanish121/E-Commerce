import "./bootstrap";
import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import 'bootstrap/dist/css/bootstrap.min.css';
import axios from 'axios';
export * from './stores/adminStore';
export * from './stores/customerStore';
import { createPinia } from 'pinia';
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate';

axios.defaults.baseURL = ''; 
axios.defaults.headers.common['Accept'] = 'application/json';
const app = createApp(App);
const pinia = createPinia();
pinia.use(piniaPluginPersistedstate);
app.use(pinia);
app.use(router);
app.mount('#app');
