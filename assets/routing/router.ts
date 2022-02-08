import {createRouter, createWebHistory} from 'vue-router'
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
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/customer/AppCustomerOrderShow.vue'),
            meta: {requiresAuth: true},
            name: 'customerOrderShow',
            path: '/customerOrder/show'
        }
    ]
})

export default router
