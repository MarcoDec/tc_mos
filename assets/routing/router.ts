import {createRouter, createWebHistory} from 'vue-router'
import type {RouteComponent} from 'vue-router'

const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            component: async (): Promise<RouteComponent> => import('./pages/tree/AppTreePageWrapper.vue'),
            meta: {requiresAuth: true},
            name: 'component-families',
            path: '/component-families',
            props: {
                extraFields: [
                    {label: 'Code', name: 'code', type: 'text'},
                    {label: 'Cuivre', name: 'copperable', type: 'boolean'}
                ],
                title: 'composants',
                type: 'Composants',
                url: '/api/component-families'
            }
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
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/tree/AppTreePageWrapper.vue'),
            meta: {requiresAuth: true},
            name: 'product-families',
            path: '/product-families',
            props: {title: 'produits', type: 'Produits', url: '/api/product-families'}
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/AppProductionPlanning.vue'),
            meta: {requiresAuth: true},
            name: 'manufacturing-schedule',
            path: '/manufacturingSchedule',
            props: {
                fields: [
                   
                    {
                        label: 'Produit',
                        name: 'produit',
                        type: 'text'
                    },
                    {
                        label: 'Ind.',
                        name: 'ind',
                        type: 'text'
                    },
                    {
                        label: 'Client',
                        name: 'client',
                        type: 'text'
                    },
                    {
                        label: 'Stocks',
                        name: 'stocks',
                        type: 'text'
                    },
                    {
                        label: '3%VP',
                        name: 'vp',
                        type: 'text'
                    },
                    {
                        label: 'Retard',
                        name: 'retard',
                        type: 'text'
                    }
                ],
                icon: 'table-list',
                title: 'Planning de production'
            }
        }
    ]
})

export default router
