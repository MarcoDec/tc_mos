export default [
    {
        component: async () => import('../pages/customer/AppCustomerShowInlist.vue'),
        meta: {requiresAuth: true},
        name: 'customerShow',
        path: '/customer/show/:id_customer'
    }
]
