/* eslint-disable consistent-return,@typescript-eslint/prefer-readonly-parameter-types */
import {createRouter, createWebHistory} from 'vue-router'
import Cookies from 'js-cookie'
import type {RouteComponent} from 'vue-router'

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
            meta: {requiresAuth: false},
            name: 'login',
            path: '/login'
        }
    ]
})

router.beforeEach(to => {
    const token = Cookies.get('token') ?? ''
    if (to.matched.some(record => record.meta.requiresAuth && record.name !== 'login') && !token)
        return {name: 'login'}
})

export default router
