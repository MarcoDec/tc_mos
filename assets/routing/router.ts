import type {NavigationGuardNext, RouteComponent, RouteLocationNormalized} from 'vue-router'
import {createRouter, createWebHistory} from 'vue-router'

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            component: async(): Promise<RouteComponent> => import('./pages/AppHome'),
            meta: {requiresAuth: true},
            name: 'home',
            path: '/'
        },
        {
            component: async(): Promise<RouteComponent> => import('./pages/security/AppLogin'),
            name: 'login',
            path: '/login'
        }
    ]
})

router.beforeEach((to: RouteLocationNormalized, from: RouteLocationNormalized, next: NavigationGuardNext): void => {
    if (to.matched.some(record => record.meta.requiresAuth))
        next({name: 'login'})
    else
        next()
})

export default router
