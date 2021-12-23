import * as Cookies from '../cookie'
import {createRouter, createWebHistory} from 'vue-router'
import type {RouteComponent} from 'vue-router'

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            component: async (): Promise<RouteComponent> => import('./pages/purchase/component/AppComponentFamilies.vue'),
            meta: {requiresAuth: true},
            name: 'families',
            path: '/component/families'
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
router.beforeEach(async to => {
    if (to.matched.some(record => record.meta.requiresAuth && record.name !== 'login') && !Cookies.has())
        return {name: 'login'}
})

export default router
