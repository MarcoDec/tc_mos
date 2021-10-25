import type {RouteComponent, RouteLocationRaw} from 'vue-router'
import {createRouter, createWebHistory} from 'vue-router'
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

// eslint-disable-next-line @typescript-eslint/no-invalid-void-type,consistent-return
router.beforeEach((to): Promise<RouteLocationRaw | void> | void => {
    console.debug('router', 'beforeEach')
    if (to.matched.some(record => record.name !== 'login') && to.matched.some(record => record.meta.requiresAuth)) {
        // eslint-disable-next-line consistent-return
        return manager.users.hasCurrent().then(hasCurrent => {
            if (!hasCurrent)
                return {name: 'login'}
        })
    }
})

export default router
