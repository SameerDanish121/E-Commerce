import { createRouter, createWebHistory } from 'vue-router';

import CustomerHome from './pages/CustomerHome.vue';
import CustomerCart from './pages/CustomerCart.vue';
import CustomerOrders from './pages/CustomerOrders.vue';
import CustomerProfile from './pages/CustomerProfile.vue';

const routes = [
  { path: '/', redirect: '/home' },
  { path: '/home', name: 'Home', component: CustomerHome },
  { path: '/cart', name: 'Cart', component: CustomerCart },
  { path: '/orders', name: 'Orders', component: CustomerOrders },
  { path: '/profile', name: 'Profile', component: CustomerProfile },
];



const router = createRouter({
  history: createWebHistory('/customer/'), 
  routes,
});


export { routes };
export default router;
