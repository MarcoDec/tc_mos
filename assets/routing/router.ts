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
            component: async (): Promise<RouteComponent> => import('./pages/AppHome.vue'),
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
            component: async (): Promise<RouteComponent> => import('./pages/chart/supplier/AppSupplierShow.vue'),
            meta: {requiresAuth: false},
            name: 'supplier-show',
            path: '/supplier/show'
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/chart/component/AppComponentShow.vue'),
            meta: {requiresAuth: false},
            name: 'component-show',
            path: '/component/show'
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/chart/customer/AppCustomerShow.vue'),
            meta: {requiresAuth: false},
            name: 'customer-show',
            path: '/customer/show'
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/chart/product/AppProductShow.vue'),
            meta: {requiresAuth: false},
            name: 'product-show',
            path: '/product/show'
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/chart/company/AppCompanyShow.vue'),
            meta: {requiresAuth: false},
            name: 'company-show',
            path: '/company/show'
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/chart/employee/AppEmployeeShow.vue'),
            meta: {requiresAuth: false},
            name: 'employee-show',
            path: '/employee/show'
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/needs/AppNeedsPage.vue'),
            meta: {requiresAuth: false},
            name: 'needs',
            path: '/needs'
        },
    ]
})

export default router
