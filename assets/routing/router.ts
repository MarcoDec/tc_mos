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
            component: async (): Promise<RouteComponent> => import('./pages/AppCollectionTablePage.vue'),
            name: 'operation-list',
            path: '/operation/list',
            props: {
                fields: [
                    {
                        create: true,
                        filter: true,
                        label: 'Code',
                        name: 'code',
                        sort: true,
                        type: 'text',
                        update: true
                    },
                    {
                        create: false,
                        filter: true,
                        label: 'Nom',
                        name: 'name',
                        options: [{text: 'aaaaa', value: 'aaaaa'}, {text: 'bbbb', value: 'bbbb'}],
                        sort: false,
                        type: 'select',
                        update: true
                    },
                    {
                        create: true,
                        filter: true,
                        label: 'Type',
                        name: 'type',
                        sort: true,
                        type: 'number',
                        update: false
                    },
                    {
                        create: false,
                        filter: true,
                        label: 'Auto',
                        name: 'auto',
                        sort: false,
                        type: 'boolean',
                        update: true
                    },
                    {
                        create: true,
                        filter: false,
                        label: 'Limite',
                        name: 'limite',
                        sort: false,
                        type: 'text',
                        update: true
                    },
                    {
                        create: false,
                        filter: true,
                        label: 'cadence',
                        name: 'cadence',
                        sort: true,
                        type: 'number',
                        update: false
                    },
                    {
                        create: true,
                        filter: true,
                        label: 'Prix',
                        name: 'prix',
                        sort: false,
                        type: 'number',
                        update: true
                    },
                    {
                        create: true,
                        filter: true,
                        label: 'Temps(en ms)',
                        name: 'Temps',
                        sort: false,
                        type: 'date',
                        update: false
                    }
                ],
                icon: 'atom',
                title: 'Opération'
            }
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/AppCollectionTablePage.vue'),
            name: 'setting',
            path: '/setting',
            props: {
                fields: [
                    {
                        create: true,
                        filter: true,
                        label: 'Nom',
                        name: 'nom',
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
                        update: true
                    }
                ],
                icon: 'atom',
                title: 'Opération'
            }
        }
    ]
})

export default router
