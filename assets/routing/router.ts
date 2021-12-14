import type {NavigationGuardNext, RouteComponent, RouteLocationNormalized} from 'vue-router'
import {connect, hasUser} from '../store/store'
import {createRouter, createWebHistory} from 'vue-router'
import {initUser} from '../store/security/User'

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            component: async (): Promise<RouteComponent> => import('./pages/AppHome'),
            meta: {requiresAuth: true},
            name: 'home',
            path: '/'
        },
        {
            component: async (): Promise<RouteComponent> => import('../components/bootstrap-5/notification/AppNotification.vue'),
            meta: {requiresAuth: false},
            name: 'notification',
            path: '/notification'
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/security/AppLogin.vue'),
            name: 'login',
            path: '/login'
        }
    ]
})

router.beforeEach(async (to: RouteLocationNormalized, from: RouteLocationNormalized, next: NavigationGuardNext): Promise<void> => {
    if (to.matched.some(record => record.meta.requiresAuth) && !hasUser().value) {
        const user = await initUser()
        if (user === null) {
            next({name: 'login'})
            return
        }
        connect(user)
    }
    next()
})

export default router
