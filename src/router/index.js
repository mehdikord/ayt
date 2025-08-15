import { createRouter, createWebHistory } from 'vue-router'
import Index from "@/views/Index/Index.vue";
import Auth from "@/views/Auth/Auth.vue";
import About from "@/views/Pages/About.vue";

const routes = [
    {
        path: '/',
        name: 'index',
        component: Index
    },
    {
        path: '/auth',
        name: 'auth',
        component: Auth
    },
    {
        path: '/about',
        name: 'about',
        component: About
    }

]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

export default router
