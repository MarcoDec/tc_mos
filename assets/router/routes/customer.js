export default [
    {
        component: async () => import('../pages/customer/AppCustomerOrderShow.vue'),
        meta: {requiresAuth: true},
        name: 'customerOrderShow',
        path: '/customerOrder/show'
    }
]
