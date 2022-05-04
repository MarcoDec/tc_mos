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
            component: async (): Promise<RouteComponent> => import('./pages/security/AppLogin.vue'),
            name: 'login',
            path: '/login'
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/project/AppOperationList.vue'),
            name: 'operationList',
            path: '/operation/list'
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/direction/AppCardableCollectionTable.vue'),
            name: 'societyList',
            path: '/society/list'
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
