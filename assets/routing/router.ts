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
            component: async (): Promise<RouteComponent> => import('./pages/purchase/supplier/AppSupplierPage.vue'),
            meta: {requiresAuth: true},
            name: 'supplier-list',
            path: '/supplier-list'
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/purchase/component/AppComponentPage.vue'),
            meta: {requiresAuth: true},
            name: 'component-list',
            path: '/component-list'
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/selling/customer/AppCustomerPage.vue'),
            meta: {requiresAuth: true},
            name: 'customer-list',
            path: '/customer-list'
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/production/engine/AppEnginePage.vue'),
            meta: {requiresAuth: true},
            name: 'engine-list',
            path: '/engine-list'
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/production/order/AppManufacturingOrderPage.vue'),
            meta: {requiresAuth: true},
            name: 'manufacturingOrder-list',
            path: '/manufacturingOrder-list'
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/project/AppProductPage.vue'),
            meta: {requiresAuth: true},
            name: 'product-list',
            path: '/product-list'
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/employee/AppEmployeePage.vue'),
            meta: {requiresAuth: true},
            name: 'employee-list',
            path: '/employee-list'
        }
    ]
})
export default router
