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
            component: async (): Promise<RouteComponent> => import('./pages/AppRowsTablePage.vue'),
            meta: {requiresAuth: true},
            name: 'prices',
            path: '/prices',
            props: {
                fields: [
                 
                    {
                        create: false,
                        filter: true,
                        label: 'Nom',
                        name: 'name',
                        sort: false,
                        type: 'text',
                        update: true,
                        prefix: 'componentSuppliers'
                    },
                    {
                        create: true,
                        filter: true,
                        label: 'Proportion',
                        name: 'proportion',
                        sort: false,
                        type: 'text',
                        update: true,
                        prefix: 'componentSuppliers'
                    },
                    {
                        create: true,
                        filter: true,
                        label: 'Délai',
                        name: 'delai',
                        sort: false,
                        type: 'number',
                        update: false,
                        prefix: 'componentSuppliers'
                    },
                    {
                        create: false,
                        filter: true,
                        label: 'Moq',
                        name: 'moq',
                        sort: false,
                        type: 'number',
                        update: true,
                        prefix: 'componentSuppliers'
                    },
                    {
                        create: false,
                        filter: true,
                        label: 'Poids cu',
                        name: 'poidsCu',
                        sort: false,
                        type: 'text',
                        update: true,
                        prefix: 'componentSuppliers'
                    },
                    {
                        create: false,
                        filter: true,
                        label: 'Référence',
                        name: 'reference',
                        sort: false,
                        type: 'text',
                        update: false,
                        prefix: 'componentSuppliers'
                    },
                    {
                        create: true,
                        filter: true,
                        label: 'Indice',
                        name: 'indice',
                        sort: false,
                        type: 'number',
                        update: true,
                        prefix: 'componentSuppliers'
                    },
                    {
                        create: true,
                        filter: true,
                        children: [
                            {label: '€', name: 'price',prefix: 'componentSupplierPrices'}, 
                            {label: 'Q', name: 'quantite',prefix: 'componentSupplierPrices'},
                            {label: 'ref', name: 'ref',prefix: 'componentSupplierPrices'}
                        ],
                        label: 'Prix',
                        name: 'prices',
                        sort: false,
                        type: 'text',
                        update: false,
                        prefix: 'componentSuppliers'
                    }
                ]
            }
        }
    ]
})

export default router
