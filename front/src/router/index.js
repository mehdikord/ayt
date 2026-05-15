import { createRouter, createWebHistory } from 'vue-router'
import Index from "@/views/Index/Index.vue";
import Auth from "@/views/Auth/Auth.vue";
import About from "@/views/Pages/About.vue";
import Profile from "@/views/Profile/Profile.vue";
import Discounts from "@/views/Pages/Discounts.vue";
import { setAdminUnauthorizedHandler } from '@/services/admin/http'
import { useAdminStore } from '@/stores/admin'
import { setUserUnauthorizedHandler } from '@/services/user/http'
import { useUserStore } from '@/stores/user'

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
    },
    {
        path: '/profile',
        name: 'profile',
        component: Profile,
        meta: { requiresUser: true }
    },
    {
        path: '/discounts',
        name: 'discounts',
        component: Discounts,
        meta: { requiresUser: true }
    },
    {
        path: '/admins/login',
        name: 'admin-login',
        component: () => import('@/views/Admin/Login.vue'),
        meta: { layout: 'admin', adminPublic: true, title: 'ورود مدیران' }
    },
    {
        path: '/admins',
        component: () => import('@/layouts/admin/AdminLayout.vue'),
        meta: { layout: 'admin', requiresAdmin: true },
        children: [
            {
                path: '',
                name: 'admin-dashboard',
                component: () => import('@/views/Admin/Dashboard.vue'),
                meta: { layout: 'admin', requiresAdmin: true, title: 'داشبورد' }
            },
            {
                path: 'categories',
                name: 'admin-categories',
                component: () => import('@/views/Admin/CategoryManagement.vue'),
                meta: { layout: 'admin', requiresAdmin: true, title: 'مدیریت دسته بندی ها' }
            },
            {
                path: 'menu',
                name: 'admin-menu',
                component: () => import('@/views/Admin/MenuManagement.vue'),
                meta: { layout: 'admin', requiresAdmin: true, title: 'مدیریت آیتم های منو' }
            },
            {
                path: 'users',
                name: 'admin-users',
                component: () => import('@/views/Admin/UserManagement.vue'),
                meta: { layout: 'admin', requiresAdmin: true, title: 'مدیریت کاربران' }
            },
            {
                path: 'discounts',
                name: 'admin-discounts',
                component: () => import('@/views/Admin/DiscountManagement.vue'),
                meta: { layout: 'admin', requiresAdmin: true, title: 'مدیریت تخفیف ها' }
            },
            {
                path: 'pages',
                name: 'admin-pages',
                component: () => import('@/views/Admin/PagesManagement.vue'),
                meta: { layout: 'admin', requiresAdmin: true, title: 'مدیریت صفحات' }
            },
            {
                path: 'settings',
                name: 'admin-settings',
                component: () => import('@/views/Admin/Settings.vue'),
                meta: { layout: 'admin', requiresAdmin: true, title: 'تنظیمات پروفایل' }
            }
        ]
    }

]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

router.beforeEach(async (to) => {
    const needsAdmin = to.matched.some((record) => record.meta?.requiresAdmin)
    const isAdminPublic = to.matched.some((record) => record.meta?.adminPublic)
    const adminStore = useAdminStore()
    const needsUser = to.matched.some((record) => record.meta?.requiresUser)
    const userStore = useUserStore()

    if (needsAdmin) {
        const hasSession = await adminStore.ensureSession()
        if (!hasSession) {
            return { name: 'admin-login', query: { redirect: to.fullPath } }
        }
    }

    if (needsUser) {
        const hasSession = await userStore.ensureSession()
        if (!hasSession) {
            return { name: 'auth', query: { redirect: to.fullPath } }
        }
    }

    if (isAdminPublic && adminStore.canAccessAdmin) {
        return { name: 'admin-dashboard' }
    }

    if (to.name === 'auth' && userStore.canAccessProtectedRoutes) {
        return { name: 'profile' }
    }

    return true
})

setAdminUnauthorizedHandler(() => {
    const adminStore = useAdminStore()
    adminStore.clearSession()
    const currentRouteName = router.currentRoute.value?.name
    if (currentRouteName !== 'admin-login') {
        router.push({ name: 'admin-login' })
    }
})

setUserUnauthorizedHandler(() => {
    const userStore = useUserStore()
    userStore.clearSession()
    const currentRouteName = router.currentRoute.value?.name
    if (currentRouteName !== 'auth') {
        router.push({ name: 'auth' })
    }
})

export default router
