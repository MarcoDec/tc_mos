export default [
    {
        component: async () => import('../pages/customer/AppCustomerShow.vue'),
        meta: {requiresAuth: true},
        name: 'customerShow',
        path: '/customer/show'
    }
]
