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
                        prefix: 'componentSuppliers',
                        sort: false,
                        type: 'text',
                        update: true
                    },
                    {
                        create: true,
                        filter: true,
                        label: 'Proportion',
                        name: 'proportion',
                        prefix: 'componentSuppliers',
                        sort: false,
                        type: 'text',
                        update: true
                    },
                    {
                        create: true,
                        filter: true,
                        label: 'Délai',
                        name: 'delai',
                        prefix: 'componentSuppliers',
                        sort: false,
                        type: 'number',
                        update: true
                    },
                    {
                        create: false,
                        filter: true,
                        label: 'Moq',
                        name: 'moq',
                        prefix: 'componentSuppliers',
                        sort: false,
                        type: 'number',
                        update: true
                    },
                    {
                        create: false,
                        filter: true,
                        label: 'Poids cu',
                        name: 'poidsCu',
                        prefix: 'componentSuppliers',
                        sort: false,
                        type: 'text',
                        update: true
                    },
                    {
                        create: false,
                        filter: true,
                        label: 'Référence',
                        name: 'reference',
                        prefix: 'componentSuppliers',
                        sort: false,
                        type: 'text',
                        update: true
                    },
                    {
                        create: true,
                        filter: true,
                        label: 'Indice',
                        name: 'indice',
                        prefix: 'componentSuppliers',
                        sort: false,
                        type: 'number',
                        update: true
                    },
                    {
                        children: [
                            {create: true, filter: true, label: '€', name: 'price', prefix: 'componentSupplierPrices', sort: false, type: 'number', update: true},
                            {create: true, filter: true, label: 'Q', name: 'quantite', prefix: 'componentSupplierPrices', sort: false, type: 'number', update: true},
                            {create: true, filter: true, label: 'ref', name: 'ref', prefix: 'componentSupplierPrices', sort: false, type: 'number', update: true}
                        ],
                        create: false,
                        filter: true,
                        label: 'Prix',
                        name: 'prices',
                        prefix: 'componentSuppliers',
                        sort: false,
                        type: 'text',
                        update: true
                    }
                ]
            }
        }
    ]
})

export default router
