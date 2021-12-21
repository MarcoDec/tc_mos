/* eslint-disable consistent-return,@typescript-eslint/prefer-readonly-parameter-types */
import {createRouter, createWebHistory} from 'vue-router'
import type {Getters} from '../store/security'
import type {RouteComponent} from 'vue-router'
import {nextTick} from "vue";
import store from '../store'
import {useNamespacedGetters} from 'vuex-composition-helpers'
import Cookies from "js-cookie";

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            component: async (): Promise<RouteComponent> => import('./pages/AppHome'),
            meta: {requiresAuth: true},
            name: 'home',
            path: '/',
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
    const token = Cookies.get('token')
    if (
        to.matched.some(record => record.meta.requiresAuth && record.name !== 'login')
        && !token
    )
        return {name: 'login'}
})

export default router
