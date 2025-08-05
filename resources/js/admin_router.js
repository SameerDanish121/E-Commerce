import { createRouter, createWebHistory } from 'vue-router';

import AdminrHome from './admin_pages/AdminHome.vue';
import AdminCustomer from './admin_pages/AdminCustomer.vue';
import AdminOrders from './admin_pages/AdminOrder.vue';
import AdminProfile from './admin_pages/AdminProfile.vue';
import AdminManage from './admin_pages/AdminManageAdmin.vue';
import ChartDashboard from './admin_pages/admin_analytics.vue';
import { useAdminStore } from '../js/stores/adminStore';

const routes = [
  { path: '/chart', name: 'Home', component : ChartDashboard },
  { path: '/home', name: 'Product', component: AdminrHome, meta: { permission: 'Manage Product' } },
  { path: '/customer', name: 'Customer', component: AdminCustomer, meta: { permission: 'Manage Customer' }  },
  { path: '/orders', name: 'Orders', component: AdminOrders, meta: { permission: 'Manage Orders' }  },
  { path: '/manage', name: 'Admin', component: AdminManage, meta: { permission: 'Add Admin' } },
  { path: '/profile', name: 'Profile', component: AdminProfile },
];
const router = createRouter({
  history: createWebHistory('/admin/'),
  routes,
});
router.beforeEach((to, from, next) => {
  const adminStore = useAdminStore();

  const requiredPermission = to.meta.permission;
  if (requiredPermission && !adminStore.hasPermission(requiredPermission)) {
    return next('/profile');
  }
  next();
});


export { routes };
export default router;
