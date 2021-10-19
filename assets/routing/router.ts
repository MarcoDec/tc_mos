import type {NavigationGuardNext, RouteComponent, RouteLocationNormalized} from 'vue-router'
import {createRouter, createWebHistory} from 'vue-router'
import {hasUser} from '../cookies'

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
            component: async (): Promise<RouteComponent> => import('./pages/security/AppLogin.vue'),
            name: 'login',
            path: '/login'
        }
    ]
})

router.beforeEach(async (to: RouteLocationNormalized, from: RouteLocationNormalized, next: NavigationGuardNext): Promise<void> => {
    if (to.matched.some(record => record.meta.requiresAuth) && !await hasUser())
        next({name: 'login'})
    else
        next()
})

export default router
