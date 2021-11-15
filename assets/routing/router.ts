/* eslint-disable consistent-return */
import type {Actions, Getters} from '../store/security'
import {createRouter, createWebHistory} from 'vue-router'
import {useNamespacedActions, useNamespacedGetters} from 'vuex-composition-helpers'
import {ActionTypes} from '../store/security'
import type {RouteComponent} from 'vue-router'
import store from '../store'

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

router.beforeEach(async to => {
    if (
        to.matched.some(record => record.name === 'login')
        || useNamespacedGetters<Getters>(store, 'users', ['hasUser']).hasUser.value
    )
        return
    if (to.matched.some(record => record.meta.requiresAuth)) {
        await useNamespacedActions<Actions>(store, 'users', [ActionTypes.CONNECT])[ActionTypes.CONNECT]()
        if (!useNamespacedGetters<Getters>(store, 'users', ['hasUser']).hasUser.value)
            return {name: 'login'}
    }
})

export default router
