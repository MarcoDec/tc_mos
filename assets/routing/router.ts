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
            component: async (): Promise<RouteComponent> => import('./pages/setting/AppSetting.vue'),
            name: 'setting',
            path: '/setting',
            props: {
                fields: [
                    {
                        create: false,
                        filter: true,
                        label: 'Nom',
                        name: 'name',
                        sort: true,
                        type: 'text',
                        update: true
                    },
                    {
                        create: true,
                        filter: true,
                        label: 'Valeur',
                        name: 'valeur',
                        sort: true,
                        type: 'text',
                        update: false
                    },
                ],
                icon: 'cogs',
                title: 'Paramètres'
            }
        },

    ]
})

export default router
