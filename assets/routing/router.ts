/* eslint-disable consistent-return */
import {createRouter, createWebHistory} from 'vue-router'
import type {Getters} from '../store/security'
import type {RouteComponent} from 'vue-router'
import store from '../store'
import {useNamespacedGetters} from 'vuex-composition-helpers'

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

router.beforeEach(to => {

    if (
        to.matched.some(record => record.meta.requiresAuth && record.name !== 'login')
        && !useNamespacedGetters<Getters>(store, 'users', ['hasUser']).hasUser.value
    )
        return {name: 'login'}
})

export default router
