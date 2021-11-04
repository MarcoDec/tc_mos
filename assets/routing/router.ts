import {createRouter, createWebHistory} from 'vue-router'
import type {RouteComponent} from 'vue-router'

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            component: async (): Promise<RouteComponent> => import('./pages/purchase/component/AppComponentFamilies.vue'),
            meta: {requiresAuth: false},
            name: 'families',
            path: '/families'
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/AppHome'),
            meta: {requiresAuth: true},
            name: 'home',
            path: '/'
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/security/AppLogin.vue'),
            meta: {requiresAuth: false},
            name: 'login',
            path: '/login'
        }
    ]
})

// eslint-disable-next-line consistent-return
router.beforeEach(to => {
    if (to.matched.some(record => record.name !== 'login' && record.meta.requiresAuth))
        return {name: 'login'}
})

export default router
