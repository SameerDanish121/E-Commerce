import { createRouter, createWebHistory } from 'vue-router';
import Login from './components/auth/Login.vue';
import Signup from './components/auth/Signup.vue';
const routes = [
    { path: '/', component: Login, name: 'login' },
    { path: '/signup', component: Signup, name: 'signup' },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;

