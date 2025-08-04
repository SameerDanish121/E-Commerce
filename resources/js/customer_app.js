import { createApp } from 'vue';
import CustomerApp from './CustomerApp.vue';
import router from './customer_router';
import { createPinia } from 'pinia';
import 'bootstrap/dist/css/bootstrap.min.css';
import axios from 'axios';
import Toast from 'vue-toast-notification';
import 'bootstrap-icons/font/bootstrap-icons.css';
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate';

const app = createApp(CustomerApp);
app.use(Toast);
axios.defaults.baseURL = ''; 
axios.defaults.headers.common['Accept'] = 'application/json';
const pinia = createPinia();
pinia.use(piniaPluginPersistedstate);
app.use(pinia);
app.use(router);
app.mount('#customer-app');
