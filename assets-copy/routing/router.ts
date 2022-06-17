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
            component: async (): Promise<RouteComponent> => import('./pages/production/AppWarehouseList.vue'),
            meta: {requiresAuth: true},
            name: 'warehouse-list',
            path: '/warehouse/list',
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
                        create: false,
                        filter: true,
                        label: 'Famille',
                        name: 'famille',
                        options: [{text: 'prison', value: 'prison'}, {text: 'production', value: 'production'}, {text: 'réception', value: 'réception'}, {text: 'magasin piéces finies', value: 'magasinPiécesFinies'}, {text: 'expédition', value: 'expédition'}, {text: 'magasin matières premiéres', value: 'magasinMatièresPremiéres'}, {text: 'camion', value: 'camion'}],
                        sort: false,
                        type: 'select',
                        update: true
                    }
                ],
                icon: 'warehouse',
                title: 'Entrepots'
            }
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/production/AppWarehouseShow.vue'),
            meta: {requiresAuth: true},
            name: 'warehouse-show',
            path: '/warehouse/show'
        }
    ]
})

export default router
