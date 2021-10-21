import {createRouter, createWebHistory} from 'vue-router'
import type {RouteComponent} from 'vue-router'
import manager from '../store/repository/RepositoryManager'

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

router.beforeEach(async (to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth) && !await manager.users.hasCurrent())
        next({name: 'login'})
    else
        next()
})

export default router
