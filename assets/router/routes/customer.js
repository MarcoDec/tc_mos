export default [
    {
        component: () => import('../pages/customer/AppCustomerPage.vue'),
        meta: {requiresAuth: true},
        name: 'customer-list',
        path: '/customer-list'
    }
]
