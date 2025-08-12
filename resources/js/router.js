import { createRouter, createWebHistory } from 'vue-router';
import Login from './components/auth/Login.vue';
import Signup from './components/auth/Signup.vue';
import Chat from './pages/Chat.vue';
import Admin_Chat from './admin_pages/ChatBot.vue';
const routes = [
    { path: '/', component: Login, name: 'login' },
    { path: '/signup', component: Signup, name: 'signup' },
    { path: '/chat', component: Chat, name: 'chat' },
    { path: '/admin_chat', component: Admin_Chat, name: 'admin_chat' },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;

