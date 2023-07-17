export default [
    {
        component: () => import('../pages/customer/AppCustomerFormShow.vue'),
        meta: {requiresAuth: true},
        name: 'customerFormShow',
        path: '/customer/formshow'
    }
]
