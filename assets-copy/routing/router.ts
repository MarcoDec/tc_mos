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
            component: async (): Promise<RouteComponent> => import('./pages/supplier/AppSupplierFormShow.vue'),
            meta: {requiresAuth: true},
            name: 'supplierFormShow',
            path: '/supplier/formshow'
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/component/AppcomponentFormShow.vue'),
            meta: {requiresAuth: true},
            name: 'componentFormShow',
            path: '/component/formshow'
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/customer/AppCustomerFormShow.vue'),
            meta: {requiresAuth: false},
            name: 'customerFormShow',
            path: '/customer/formshow'
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/product/AppProductFormShow.vue'),
            meta: {requiresAuth: false},
            name: 'productFormShow',
            path: '/product/formshow'
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/employee/AppEmployeeFormShow.vue'),
            meta: {requiresAuth: false},
            name: 'employeeFormShow',
            path: '/employee/formshow'
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/equipment/AppWorkstationFormShow.vue'),
            meta: {requiresAuth: false},
            name: 'workstationFormShow',
            path: '/workstation/formshow'
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/equipment/AppToolFormShow.vue'),
            meta: {requiresAuth: false},
            name: 'toolFormShow',
            path: '/tool/formshow'
        },
        {
            component: async (): Promise<RouteComponent> => import('./pages/equipment/AppTestCounterPartFormShow.vue'),
            meta: {requiresAuth: false},
            name: 'testCounterPartFormShow',
            path: '/testcounterpart/formshow'
        }
    ]
})

export default router
