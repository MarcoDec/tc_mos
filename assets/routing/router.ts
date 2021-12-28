import {createRouter, createWebHistory} from 'vue-router'
import type {RouteComponent} from 'vue-router'
import {app} from '../app'

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

// eslint-disable-next-line consistent-return
router.beforeEach(to => {
    if (
        to.matched.some(record => record.meta.requiresAuth && record.name !== 'login')
        // eslint-disable-next-line @typescript-eslint/no-unsafe-member-access,@typescript-eslint/strict-boolean-expressions
        && app.config.globalProperties.$store.getters['security/hasUser']
    )
        return {name: 'login'}
})

export default router
